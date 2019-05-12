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



    //=======================================================

    /**
     * @return Route[]
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
     * @return Router[]
     */
    public function getRouters()
    {
        return $this->federatedRouter->getRouters();
    }


    public function executeRoute($routerName, $routeId)
    {
        return $this->federatedRouter->executeRoute($routerName, $routeId);
    }




    /**
     * @param $routerName
     * @return Router
     */
    public function getRouterByName($routerName)
    {
        return $this->federatedRouter->getRouterByName($routerName);
    }




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
