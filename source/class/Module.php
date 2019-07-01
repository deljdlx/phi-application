<?php


namespace Phi\Application;


use Phi\Routing\Exception\NotFound;
use Phi\Routing\Request;
use Phi\Routing\Response;
use Phi\Routing\Router;

class Module
{

    /**
     * @var Router;
     */
    private $router;


    /**
     * @var Response
     */
    private $response;

    private $validator = true;



    public function __construct()
    {
        $this->router = new Router();
    }


    public function getRouter()
    {
        return $this->router;
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }


    /**
     * @param Request $request
     * @return bool|Response
     */
    public function execute(Request $request)
    {
        if(!$this->validate($request)) {
            return false;
        }

        try {
            $response = $this->router->route($request);
            $this->response = $response;
            return $response;
        } catch(NotFound $exception) {
            return false;
        }
    }


    /**
     * @param Request $request
     * @return bool|int
     */
    public function validate(Request $request)
    {
        if(is_bool($this->validator)) {
            return $this->validator;
        }
        else if(is_string($this->validator)) {
            return preg_match_all('`'.$this->validator.'`', $request->getURI());
        }
        else if(is_callable($this->validator)) {
            return call_user_func_array($this->validator, array($request));
        }

        return false;
    }




    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }


}
