<?php

namespace Phi\Application;



use Phi\Application\Exception\NoResponse;
use Phi\Container\Container;
use Phi\FileSystem\Path;
use Phi\Routing\Request;
use Phi\Routing\Response;
use Phi\Routing\Router;
use Phi\View\View;

class Application
{

    const DEFAULT_MODULE_NAME = 'application';
    const DEFAULT_CONTAINER_NAME = 'main';

    /**
     * @var Path
     */
    private $path;


    /**
     * @var Module[]
     */
    private $modules = [];


    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;


    /**
     * @var View
     */
    private $view;


    /**
     * @var Container[]
     */
    private $containers;


    /**
     * Application constructor.
     * @param null $path
     */
    public function __construct($path = null, Container $container = null)
    {
        if ($path === null) {
            $path = getcwd();
        }
        $this->path = new Path($path);
        $this->modules[self::DEFAULT_MODULE_NAME] = new Module();

        $this->view = new View();

        if($container) {
            $this->containers[self::DEFAULT_CONTAINER_NAME] = $container;
        }
        else {
            $this->containers[self::DEFAULT_CONTAINER_NAME] = new Container();
        }
    }

    public function setContainer(Container $container, $name = null)
    {
        if($name === null) {
            $name = self::DEFAULT_CONTAINER_NAME;
        }

        if(!array_key_exists($name, $this->containers)) {
            $this->containers[$name] = $container;
        }
        else {
            throw new Exception('A container named '.$name.' is already registered');
        }
        return $this;
    }

    public function getContainer($name = null)
    {
        if($name === null) {
            $name = self::DEFAULT_CONTAINER_NAME;
        }
        if(array_key_exists($name, $this->containers)) {
            return $this->containers[$name];
        }
        else {
            throw new Exception('No container named '.$name.' registered');
        }
    }

    public function get($variableName, array $parameters = array(), $containerName = null)
    {
        if($containerName === null) {
            $containerName = self::DEFAULT_CONTAINER_NAME;
        }

        $container = $this->getContainer($containerName);
        return $container->get($variableName, $parameters);
    }

    public function set($variableName, $callback, $isStatic = true, $containerName = null)
    {
        if($containerName === null) {
            $containerName = self::DEFAULT_CONTAINER_NAME;
        }

        $container = $this->getContainer($containerName);
        $container->set($variableName, $callback, $isStatic);

        return $this;
    }





    /**
     * @param View $view
     * @return $this
     */
    public function setView(View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }




    /**
     * @return string
     */
    public function getFilepathRoot()
    {
        return $this->path->normalize();
    }

    public function getPath()
    {
        return $this->path;
    }


    /**
     * @param null $moduleName
     * @return Module
     * @throws Exception
     */
    public function getModule($moduleName = null)
    {
        if($moduleName === null) {
            return $this->modules[self::DEFAULT_MODULE_NAME];
        }

        if(!array_key_exists($moduleName, $this->modules)) {
            throw new Exception('Module '.$moduleName.' does not exists');
        }

        return $this->modules[$moduleName];
    }

    /**
     * @param $moduleName
     * @param Module $module
     * @return $this
     * @throws Exception
     */
    public function registerModule($moduleName, Module $module,  $validator = null)
    {
        if(array_key_exists($moduleName, $this->modules)) {
            throw new Exception('Module '.$moduleName.' already exists');
        }

        if($validator !== null) {
            $module->setValidator($validator);
        }

        $this->modules[$moduleName] = $module;
        return $this;
    }


    /**
     * @param null $moduleName
     * @return Router
     */
    public function getRouter($moduleName = null) {
        $module = $this->getModule($moduleName);
        return $module->getRouter();
    }


    /**
     * @param Request|null $request
     * @return \Phi\Routing\Response
     * @throws NoResponse
     */
    public function run(Request $request = null)
    {
        if($request === null) {
            $request = new Request();
        }

        $this->request = $request;

        foreach ($this->modules as $module) {
            if($module->execute($request)) {
                $this->response = $module->getResponse();
                $this->view->setResponse($this->response);
                $this->view->setContent($this->response->getContent());
                return $this->response;
            }
        }

        throw new NoResponse();
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function flush()
    {
        $this->response->sendHeaders();
        echo $this->view->render();
    }


}




