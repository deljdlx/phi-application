<?php
namespace Phi\Application;

use Phi\Routing\Interfaces\Router;


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


    protected $router;
    protected $datasources;


    protected $output = '';

    protected $returnValue = 0;


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

    public function run($flush = false)
    {

        if ($this->router) {

            ob_start();
            $this->returnValue = $this->router->run();
            $this->output = ob_get_clean();
            if ($flush) {
                echo $this->getOutput();
            }
            return $this->returnValue;
        } else {
            throw new Exception('Application does not have an instance of "Phi\Interfaces\Router" defined');
        }
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


    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }


    public function getDefaultRouter()
    {
        return new \Phi\Routing\Router();
    }


    public function get($route, $callback)
    {
        if (!$this->router instanceof Router) {
            $this->setRouter($this->getDefaultRouter());
        }

        $this->router->get($route, $callback);
        return $this;

    }


}
