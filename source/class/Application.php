<?php

namespace Phi\Application;



use Phi\Container\FederatedContainer;
use Phi\Core\Exception;
use Phi\Event\Traits\Listenable;
use Phi\HTTP\Header;
use Phi\Routing\FederatedRouter;
use Phi\Routing\Request;


use Phi\Routing\ResponseCollection;


use Phi\Routing\Route;
use Phi\Routing\Router;

use Phi\Container\Interfaces\Container as IContainer;
use Phi\Core\Helper\File;


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


    /**
     * @var State
     */
    protected $executionState;

    protected $path;


    /**
     * @var Router[]
     */
    protected $routers = [];


    /**
     * @var Router
     */
    protected $masterRouter;

    /**
     * @var FederatedRouter
     */
    protected $federatedRouter;



    /**
     * @var Request
     */
    protected $request;


    protected $datasources;


    protected $output = '';

    protected $returnValue = 0;


    protected $callback = null;


    /**
     * @var FederatedContainer
     */
    protected $containerManager;


    /**
     * @var Header[]
     */
    protected $headers = [];


<<<<<<< HEAD
    /**
     * @var Route[]
     */
    protected $executedRoutes;


    /**
     * @var ResponseCollection
     */
    protected $responsesCollection;


=======
>>>>>>> dd71c84959499965981ab37c5aca986d4a2e17e6


    public function __construct($path = null, $autobuild = true)
    {

        if ($path === null) {
            $path = getcwd();
        }

        $this->path = File::normalize($path);

        $this->executionState = new State($this);

        $this->containerManager = new FederatedContainer();

        if($autobuild) {
            $this->initialize();
        }
    }


    public function set($name, $callback, $isStatic = true, $containerName = null)
    {
        $this->containerManager->set($name, $callback, $isStatic, $containerName);
        return $this;
    }


    public function get($name, $parameters =array(), $containerName = null)
    {
        return $this->containerManager->get($name, $parameters, $containerName);
    }


    /**
     * @return string
     */
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


<<<<<<< HEAD
    /**
     * @return Route[]
     */
    public function getExecutedRoutes()
    {
        return $this->executedRoutes;
    }

    /**
     * @param string $name
     * @return Container
     * @throws \Exception
=======

    //=======================================================

    /**
     * @return Route[]
>>>>>>> dd71c84959499965981ab37c5aca986d4a2e17e6
     */
    public function getExecutedRoutes()
    {
        return $this->federatedRouter->executedRoutes();
    }


    public function runRouters($request = null, array $variables = array())
    {
        $this->federatedRouter->runRouters($request, $variables);
        return $this;
    }


    //=======================================================

    /**
     * @return FederatedRouter
     */
    public function getRouter()
    {
        return $this->federatedRouter;
    }

    /**
<<<<<<< HEAD
     * @param IContainer $container
     * @param null $containerName
     * @return $this
=======
     * @return Router[]
>>>>>>> dd71c84959499965981ab37c5aca986d4a2e17e6
     */
    public function getRouters()
    {
        return $this->federatedRouter->getRouters();
    }


<<<<<<< HEAD
    /**
     * @param $name
     * @param null $containerName
     * @return bool
     */
    public function exists($name, $containerName = null)
=======
    public function executeRoute($routerName, $routeId)
>>>>>>> dd71c84959499965981ab37c5aca986d4a2e17e6
    {
        return $this->federatedRouter->executeRoute($routerName, $routeId);
    }




<<<<<<< HEAD

    /**
     * @param $name
     * @param $callback
     * @param bool $isStatic
     * @param null $containerName
     * @return $this
     */
    public function set($name, $callback, $isStatic = true, $containerName = null)
=======
    /**
     * @param $routerName
     * @return Router
     */
    public function getRouterByName($routerName)
>>>>>>> dd71c84959499965981ab37c5aca986d4a2e17e6
    {
        return $this->federatedRouter->getRouterByName($routerName);
    }

<<<<<<< HEAD

    /**
     * @param $name
     * @param array $parameters
     * @param null $containerName
     * @return mixed
     * @throws Exception
     */
    public function get($name, $parameters =array(), $containerName = null)
    {
        if($containerName !== null) {
            $container = $this->getContainer($containerName);
            return $container->get($name, $parameters);
        }
=======
>>>>>>> dd71c84959499965981ab37c5aca986d4a2e17e6



    //=======================================================









    /**
     * @param $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
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
        else if ($this->federatedRouter) {



            $this->federatedRouter->route($request, $variables);
            $this->output = $this->federatedRouter->getOutput();


            if ($flush) {
                $this->responsesCollection->send();
            }
        }

        return $this;
    }

    /**
     * @param bool $return
     * @return null|string
     */
    public function flush($return = false) {
        $this->sendHeaders();
        if($return) {
            return $this->getOutput();
        }
        else {
            echo $this->getOutput();
        }
        return null;
    }





    /**
     * @param null $request
     * @param array $variables
     * @return bool
     */
<<<<<<< HEAD
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


        $routers = $this->routers;
        if ($this->masterRouter instanceof Router) {
            array_unshift($routers, $this->masterRouter);
        }




        foreach ($routers as $router) {
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


    /**
     * @param null $request
     * @param array $variables
     * @return bool
     */
=======
>>>>>>> dd71c84959499965981ab37c5aca986d4a2e17e6
    public function hasValidRoute($request = null, array $variables = array())
    {

        return $this->federatedRouter->hasValidRoute($request, $variables);

    }


    /**
     * @param Header $header
     * @return $this
     */
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


    /**
     * @return bool
     */
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


    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * @return int
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }


    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }


    /**
     * @param string $buffer
     * @return $this
     */
    public function setOutput($buffer)
    {
        $this->output = $buffer;
        return $this;
    }


    /**
     * @return $this
     */
    public function sendHeaders()
    {
        foreach ($this->getHeaders() as $header) {
            $header->send();
        }
        return $this;

    }


    /**
     * @param bool $merge
     * @return ResponseCollection[]
     */
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
        $this->federatedRouter = new FederatedRouter();
        return $this;
    }



    //=======================================================


    /**
     * @return State
     */
    public function getExecutionState()
    {
        return $this->executionState;
    }



    /**
     * @return Container
     */
    protected function getDefaultContainer() {
        return new Container();
    }


}
