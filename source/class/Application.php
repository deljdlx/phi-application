<?php

namespace Phi\Application;


use Phi\Core\Exception;
use Phi\Event\Traits\Listenable;
use Phi\HTTP\Header;
use Phi\Routing\Request;


use Phi\Routing\ResponseCollection;
use Planck\Container;

use Phi\Routing\Route;
use Phi\Routing\Router;

use \Phi\Container\Interfaces\Container as IContainer;


/**
 * Class Application
 *
 * @property Router $router
 *
 * @package Phi
 */
class Application implements IContainer
{

    use Listenable;

    const DEFAULT_APPLICATION_NAME = 'phi-application-main';

    const EVENT_RUN_START = 'APPLICATION_EVENT_RUN_START';
    const EVENT_INITIALIZE = 'APPLICATION_EVENT_INITIALIZE';
    const EVENT_RUN_BEFORE_ROUTING = 'EVENT_RUN_BEFORE_ROUTING';
    const EVENT_RUN_AFTER_ROUTING = 'EVENT_RUN_AFTER_ROUTING';

    const EVENT_RUN_BEFORE_ROUTE_EXECUTION = 'EVENT_RUN_BEFORE_ROUTE_EXECUTION';
    const EVENT_RUN_AFTER_ROUTE_EXECUTION = 'EVENT_RUN_AFTER_ROUTE_EXECUTION';

    const EVENT_NO_RESPONSE = __CLASS__.'EVENT_RUN_AFTER_ROUTE_EXECUTION';
    const EVENT_SUCCESS = __CLASS__.'EVENT_SUCCESS';





    const DEFAULT_ROUTER_NAME = 'main';






    static protected $instances = array();

    protected $path;


    /**
     * @var Router[]
     */
    protected $routers;


    /**
     * @var Request
     */
    protected $request;


    protected $datasources;


    protected $output = '';

    protected $returnValue = 0;


    protected $callback = null;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Container[]
     */
    protected $containers = array();


    /**
     * @var ResponseCollection[]
     */
    protected $responsesCollections;

    /**
     * @var Header[]
     */
    protected $headers;


    /**
     * @var Route[]
     */
    protected $executedRoutes;


    /**
     * @param string $name
     * @return Application
     * @throws Exception
     */
    public static function getInstance($name = null)
    {

        if($name === null) {
            $name = static::DEFAULT_APPLICATION_NAME;
        }

        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        else {
            throw new Exception('Application instance with name ' . $name . ' does not exist');
        }
    }


    public function __construct($path = null, $instanceName = null, $autobuild = true)
    {

        if ($path === null) {
            $path = getcwd();
        }

        if($instanceName === null) {
            $instanceName = static::DEFAULT_APPLICATION_NAME;
        }


        $this->path = $path;
        static::$instances[$instanceName] = $this;

        if($autobuild) {
            $this->autobuild();
        }

        $this->initialize();
    }


    public function getFilepathRoot()
    {
        return $this->path;
    }




    protected function initialize()
    {
        $this->fireEvent(
            static::EVENT_INITIALIZE,
            array(
                'request' => $this->request,
                'application' => $this
            )
        );
    }


    public function getExecutedRoutes()
    {
        return $this->executedRoutes;
    }




    /**
     * @return Container
     */
    public function getContainer($name = null)
    {
        if($name === null) {
            if ($this->container === null) {
                $this->container = $this->getDefaultContainer();
            }
            return $this->container;
        }
        else {
            if(isset($this->containers[$name])) {
                return $this->containers[$name];
            }
            else {
                throw new \Exception('Application has no container named "'.$name.'"');
            }
        }
    }


    /**
     * @return Container[]
     */
    public function getContainers()
    {
        return array_merge(
            array($this->container),
            $this->containers
        );
    }

    /**
     * @param IContainer $container
     * @return $this
     */
    public function setContainer(IContainer $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param IContainer $container
     * @return $this
     */
    public function addContainer(IContainer $container, $containerName = null)
    {
        if($containerName === null) {
            $this->containers[] = $container;
        }
        else {
            $this->containers[$containerName] = $container;
        }

        return $this;
    }


    public function exists($name, $containerName = null)
    {

        if($containerName !== null) {
            try {
                $container = $this->getContainer($containerName);
                return $container->offsetExists($name);
            }
            catch(\Exception $e) {
                return false;
            }
        }
        else {
            if($this->getContainer()->offsetExists($name)) {
                return true;
            }
            else {
                foreach ($this->containers as $container) {
                    if($container->offsetExists($name)) {
                        return true;
                    }
                }

            }
        }

        return false;

    }

    public function set($name, $callback, $isStatic = true, $containerName = null)
    {
        $this->getContainer($containerName)->set($name, $callback, $isStatic);
        return $this;
    }

    public function get($name, $parameters =array(), $containerName = null)
    {
        if($containerName !== null) {
            $container = $this->getContainer($containerName);
            return $container->get($name, $parameters);
        }

        try {
            return $this->getContainer()->get($name, $parameters);
        }
        catch(\Exception $exception) {
            foreach ($this->containers as $container) {


                if($container->offsetExists($name)) {
                    return $container->get($name, $parameters);
                }
            }
            throw $exception;
        }

    }









    /**
     * @param $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function executeRoute($routeId)
    {
        return $this->getRouter()->executeRoute($routeId);
    }




    public function setRequest(Request $request = null)
    {
        if($request === null) {
            $request = Request::getInstance();
        }

        $this->request = $request;

        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }





    public function run(Request $request = null, array $variables = array(), $flush = false)
    {

        if ($request !== null || !$this->request) {
            $this->setRequest($request);
        }



        $this->fireEvent(
            static::EVENT_RUN_START,
            array(
                'request' => $this->request,
                'application' => $this
            )
        );



        if ($this->callback) {
            if (is_string($this->callback)) {

                $this->output = $this->callback;
            }
            else if (is_callable($this->callback)) {
                $this->output = call_user_func_array($this->callback, array($request));
            }
        }

        else if (!empty($this->routers)) {


            $this->fireEvent(
                static::EVENT_RUN_BEFORE_ROUTING,
                array(
                    'request' => $this->request,
                    'application' => $this
                )
            );

            $this->runRouters($request, $variables);


            if ($flush) {
                $this->responsesCollection->send();
            }
        }

        return $this;
    }


    /**
     * @param null $request
     * @param array $variables
     * @return Route[]
     */
    public function getValidatedRoutes($request = null, array $variables = array())
    {

        if ($request == null) {
            $request = Request::getInstance();
        }
        elseif(is_string($request)) {
            $uri = $request;
            $request = new Request();
            $request->setURI($uri);
        }



        $responsesCollections = array();
        $routes = array();

        foreach ($this->routers as $key=> $router) {
            $collection = $router->route($request, $variables, $responsesCollections);

            if(!$collection->isEmpty()) {

                $responses = $collection->getResponses();
                foreach ($responses as $response) {
                    $routes[] = $response->getRoute();
                }
            }
        }

        return $routes;

    }

    public function runRouters($request = null, array $variables = array())
    {

        $this->headers = array();

        if ($request == null) {
            $request = Request::getInstance();
        }

        $this->responsesCollections = array();

        foreach ($this->routers as $router) {


            $collection = $router->route($request, $variables, $this->executedRoutes);

            if(!$collection->isEmpty()) {
                $this->responsesCollections[] = $collection;
            }

        }


        $this->fireEvent(
            static::EVENT_RUN_AFTER_ROUTING,
            array(
                'request' => $this->request,
                'application' => $this
            )
        );


        $this->fireEvent(
            static::EVENT_RUN_BEFORE_ROUTE_EXECUTION,
            array(
                'request' => $this->request,
                'application' => $this
            )
        );

        //=======================================================
        $continue = true;
        foreach ($this->responsesCollections as $collection) {
            $continue = $collection->execute();
            if(!$continue) {
                break;
            }
        }



        //=======================================================


        $this->fireEvent(
            static::EVENT_RUN_AFTER_ROUTE_EXECUTION,
            array(
                'request' => $this->request,
                'application' => $this
            )
        );

        $noResponse = true;
        foreach ($this->responsesCollections as $collection) {
            if(!empty($collection->getExecutedResponses())) {
                $noResponse = false;
                break;
            }
        }


        if($noResponse) {



            $this->fireEvent(
                static::EVENT_NO_RESPONSE,
                array(
                    'request' => $this->request,
                    'application' => $this
                )
            );
        }
        else {
            $this->fireEvent(
                static::EVENT_SUCCESS,
                array(
                    'request' => $this->request,
                    'application' => $this
                )
            );
        }


        $output = '';

        foreach ($this->responsesCollections as $collection) {
            $this->headers = array_merge($this->headers, $collection->getHeaders());
            $output .=$collection->__toString($this->headers);
        }



        $this->output = $output;
    }



    public function addHeader(Header $header)
    {
        $this->headers[] = $header;
        return $this;
    }



    public function getHeaders()
    {
        return $this->headers;
    }


    public function isHTMLResponse()
    {
        $headers = $this->getHeaders();
        foreach ($headers as $header) {
            if($header->isHTML()) {
                return true;
            }
        }

        return false;

    }



    public function getPath()
    {
        return $this->path;
    }


    public function getReturnValue()
    {
        return $this->returnValue;
    }


    public function getOutput()
    {
        return $this->output;
    }

    public function setOutput($buffer)
    {
        $this->output = $buffer;
        return $this;
    }



    public function sendHeaders()
    {
        foreach ($this->getHeaders() as $header) {
            $header->send();
        }
        return $this;

    }





    public function getResponses($merge = true)
    {
        if($merge) {
            $responses = array();
            foreach ($this->responsesCollections as $responseCollection) {

                foreach ($responseCollection->getResponses() as $response) {
                    $responses [] = $response;
                }
            }
            return $responses;
        }
        else {
            return $this->responsesCollections;
        }

    }







    public function setDatasources($sources)
    {
        $this->datasources = $sources;
        return $this;
    }

    public function getDatasource($name)
    {
        return $this->datasources->getSource($name);
    }

    /**
     * @return $this
     */
    public function autobuild()
    {
        $this->setRequest();
        $this->setDefaultRouter();
        return $this;
    }


    //=======================================================
    /**
     * @return $this
     */
    public function setDefaultRouter(Router $router = null)
    {

        if($router === null) {

            $this->routers[static::DEFAULT_ROUTER_NAME] = new Router();
        }
        else {
            $this->routers[static::DEFAULT_ROUTER_NAME] = $router;
        }

        return $this;
    }




    /**
     * @return Router
     */
    protected function getDefaultRouter()
    {

        if(!array_key_exists(static::DEFAULT_ROUTER_NAME, $this->routers)) {
            $this->setDefaultRouter();
        }
        return $this->routers[static::DEFAULT_ROUTER_NAME];
    }


    /**
     * @return Router[]
     */
    public function getRouters()
    {
        return $this->routers;
    }

    public function addRouter(Router $router, $name = null)
    {
        if($name === null) {
            $this->routers[] = $router;
        }
        else {
            $this->routers[$name] = $router;
        }
        return $this;

    }

    //=======================================================


    /**
     * @param $name
     * @param $method
     * @param $validator
     * @param $callback
     * @param array $headers
     * @return Route
     */
    public function addRoute($name, $method, $validator, $callback, $headers = array()) {
        $route = new Route($method, $validator, $callback, $headers, $name);
        $this->getDefaultRouter()->addRoute($route, $name);
        return $route;
    }






    /**
     * @return Container
     */
    protected function getDefaultContainer() {
        return new Container();
    }


}
