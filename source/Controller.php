<?php
namespace Phi\Application;




class Controller
{

    /**
     * @var Application
     */
    protected $application;


    public function __construct(Application $application)
    {
        $this->application = $application;
    }

}