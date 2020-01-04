<?php
namespace Phi\Application;




class Controller
{

    /**
     * @var Application
     */
    private $application;

    private $content;





    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }


}