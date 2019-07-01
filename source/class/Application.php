<?php

namespace Phi\Application;



use Phi\Application\Exception\NoResponse;
use Phi\FileSystem\Path;
use Phi\Routing\Request;

class Application
{

    const DEFAULT_MODULE_NAME = 'application';

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


    public function __construct($path = null)
    {
        if ($path === null) {
            $path = getcwd();
        }
        $this->path = new Path($path);
        $this->modules[self::DEFAULT_MODULE_NAME] = new Module();
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
     * @return Module
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
                $response = $module->getResponse();
                return $response;
            }
        }

        throw new NoResponse();

    }

}




