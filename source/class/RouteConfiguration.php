<?php

namespace Phi\Application;


use Phi\Routing\Route;

abstract class RouteConfiguration
{

    /**
     * @return Route[]
     */
    public abstract function getRoutes();
}

