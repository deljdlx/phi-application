<?php

namespace Phi\Application;

use Phi\Container\Container;
use Phi\Event\Traits\Listenable;
use Phi\Routing\Request;
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

    const DEFAULT_APPLICATION_NAME = 'main';

    static protected $instances = array();

    protected $path;


    /**
     * @var Router
     */
    protected $router;


    protected $datasources;


    protected $output = '';

    protected $returnValue = 0;


    protected $callback = null;

    /**
     * @var Container
     */
    protected $container;


    /**
     * @param string $name
     * @return Application
     * @throws Exception
     */
    public static function getInstance($name = self::DEFAULT_APPLICATION_NAME)
    {

        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }
        else {
            throw new Exception('Application instance with name ' . $name . ' does not exist');
        }
    }


    public function __construct($path = null, $instanceName = 'main', $autobuild = true)
    {

        if ($path === null) {
            $path = getcwd();
        }

        $this->path = $path;
        static::$instances[$instanceName] = $this;

        if($autobuild) {
            $this->autobuild();
        }

    }


    /**
     * @return Container
     */
    public function getContainer()
    {
        if ($this->container === null) {
            $this->container = $this->getDefaultContainer();
        }
        return $this->container;
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


    public function set($name, $callback, $isStatic = true)
    {
        $this->getContainer()->set($name, $callback, $isStatic);
        return $this;
    }

    public function get($name, $parameters =array())
    {
        return $this->getContainer()->get($name, $parameters);
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

    public function run(Request $request = null, $flush = false)
    {


        if ($request == null) {
            $request = new Request();
        }


        if ($this->callback) {
            if (is_string($this->callback)) {
                $this->output = $this->callback;
            }
            else if (is_callable($this->callback)) {
                $this->output = call_user_func_array($this->callback, array($request));
            }
        }
        else if ($this->router) {

            $responseCollection = $this->router->route($request);
            $output = $responseCollection->__toString();

            $this->output = $output;
        }

        if ($flush) {
            echo $this->getOutput();
        }

        return $this;
    }


    public function getReturnValue()
    {
        return $this->returnValue;
    }


    public function getOutput()
    {
        return $this->output;
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
        $this->enableRouter();
        return $this;
    }


    /**
     * @return $this
     */
    public function enableRouter()
    {
        $this->setRouter($this->getDefaultRouter());
        return $this;
    }

    /**
     * @param Router $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }


    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

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
        $this->router->addRoute($route, $name);
        return $route;
    }

    public function loadRouteConfiguration(RouteConfiguration $configuration)
    {
        foreach ($configuration->getRoutes() as $routeName => $route) {
            $this->router->addRoute($route, $routeName);
        }
    }





    /**
     * @return Router
     */
    protected function getDefaultRouter()
    {
        return new \Phi\Routing\Router();
    }



    /**
     * @return Container
     */
    protected function getDefaultContainer() {
        return new Container();
    }


}
