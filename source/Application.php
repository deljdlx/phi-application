<?php
namespace Phi\Application;

use Phi\Routing\Request;
use Phi\Routing\Router;


/**
 * Class Application
 *
 * @property Router $router
 *
 * @package Phi
 */
class Application
{


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
     * @param string $name
     * @return Application
     * @throws Exception
     */
    public static function getInstance($name = 'main')
    {

        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        } else {
            throw new Exception('Application instance with name ' . $name . ' does not exist');
        }
    }


    public function __construct($path, $name = 'main')
    {
        $this->path = $path;
        static::$instances[$name] = $this;
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
            $request=new Request();
        }


        if ($this->callback) {
            if (is_string($this->callback)) {
                $this->output = $this->callback;
            }
            else if(is_callable($this->callback)) {
                $this->output=call_user_func_array($this->callback, array($request));
            }
        } else if ($this->router) {

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


    public function enableRouter() {
        $this->setRouter($this->getDefaultRouter());
        return $this;
    }

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


    public function getDefaultRouter()
    {
        return new \Phi\Routing\Router();
    }


}
