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
use Planck\Helper\File;


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



    const EVENT_RUN_START = 'APPLICATION_EVENT_RUN_START';
    const EVENT_INITIALIZE = 'APPLICATION_EVENT_INITIALIZE';

    const EVENT_BEFORE_INITIALIZE = 'APPLICATION_EVENT_BEFORE_INITIALIZE';


    const EVENT_RUN_BEFORE_ROUTING = 'EVENT_RUN_BEFORE_ROUTING';
    const EVENT_RUN_AFTER_ROUTING = 'EVENT_RUN_AFTER_ROUTING';

    const EVENT_RUN_BEFORE_ROUTE_EXECUTION = 'EVENT_RUN_BEFORE_ROUTE_EXECUTION';
    const EVENT_RUN_AFTER_ROUTE_EXECUTION = 'EVENT_RUN_AFTER_ROUTE_EXECUTION';

    const EVENT_NO_RESPONSE = __CLASS__.'EVENT_RUN_AFTER_ROUTE_EXECUTION';
    const EVENT_SUCCESS = __CLASS__.'EVENT_SUCCESS';


    const DEFAULT_CONTAINER_NAME = __CLASS__.'_DEFAULT_CONTAINER';




    const DEFAULT_ROUTER_NAME = 'main';




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
    protected $headers = [];


    /**
     * @var Route[]
     */
    protected $executedRoutes;




    public function __construct($path = null)
    {

        if ($path === null) {
            $path = getcwd();
        }

        $this->path = File::normalize($path);
        $this->addContainer(
            new Container(),
           static::DEFAULT_CONTAINER_NAME
        );



    }


    public function getFilepathRoot()
    {
        return $this->path;
    }




    public function initialize()
    {
        $this->fireEvent(
            static::EVENT_BEFORE_INITIALIZE,
            array(
                'application' => $this
            )
        );
        $this->autobuild();
        return $this;
    }


    public function getExecutedRoutes()
    {
        return $this->executedRoutes;
    }




    /**
     * @return Container
     */
    public function getContainer($name = self::DEFAULT_CONTAINER_NAME)
    {

        if($name === null) {
            $name = static::DEFAULT_CONTAINER_NAME;
        }

        if(array_key_exists($name, $this->containers)) {
            return $this->containers[$name];
        }
        else {
            throw new \Exception('Application has no container named "'.$name.'"');
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
            $container = $this->getContainer($containerName);
            return $container->offsetExists($name);
        }
        else {
            foreach ($this->containers as $container) {
                if($container->offsetExists($name)) {
                    return true;
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

        foreach ($this->containers as $container) {
            if($container->offsetExists($name)) {
                return $container->get($name, $parameters);
            }
        }

        throw new Exception('Application has no item registered with name "'.$name.'"');


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

    public function executeRoute($routerName, $routeId)
    {
        return $this->getRouter($routerName)->executeRoute($routeId);
    }


    /**
     * @param $routerName
     * @return Router
     * @throws Exception
     */
    public function getRouter($routerName)
    {
        if(array_key_exists($routerName, $this->routers)) {
            return $this->routers[$routerName];
        }
        else {
            throw new Exception('No router with name "'.$routerName.'" registered');
        }
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



    public function hasResponse(Request $request = null, array $variables = array())
    {
        if ($request !== null || !$this->request) {
            $this->setRequest($request);
        }



        if ($this->callback) {
            if (is_string($this->callback)) {

                return true;
            }
            else if (is_callable($this->callback)) {
                return true;
            }
        }

        else if (!empty($this->routers)) {

            $this->runRouters($request, $variables);
        }

        return false;
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

    public function hasValidRoute($request = null, array $variables = array())
    {


        if ($request == null) {
            $request = Request::getInstance();
        }

        $this->responsesCollections = array();

        foreach ($this->routers as $router) {


            $collection = $router->route($request, $variables, $this->executedRoutes);

            if (!$collection->isEmpty()) {
                $this->responsesCollections[] = $collection;
            }
        }


        if(!empty($this->responsesCollections)) {
            return true;
        }

        return false;

    }



    public function addHeader(Header $header)
    {
        $this->headers[] = $header;
        return $this;
    }


    /**
     * @return Header[]
     */
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



    /**
     * @return $this
     */
    public function autobuild()
    {
        $this->setRequest();
        return $this;
    }


    //=======================================================
    /**


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
            $name = get_class($router);
        }

        $this->routers[$name] = $router;

        return $this;

    }

    //=======================================================





    /**
     * @return Container
     */
    protected function getDefaultContainer() {
        return new Container();
    }


}
