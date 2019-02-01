<?php
namespace Phi\Application;




class Controller
{

    /**
     * @var Application
     */
    protected $application;


    public function __construct(Application $application = null)
    {
        if($application) {
            $this->application = $application;
        }

    }

}