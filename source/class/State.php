<?php
namespace Phi\Application;



use Phi\State\Dimension;

class State extends \Phi\State\State
{

    const DIMENSION_HTTP = 'STATE_HTTP';


    public function __construct(Application $application)
    {
        parent::__construct($application);

        $this->addDimension(
            new Dimension('forbidden')
        );

        $this->addDimension(
            new Dimension('notFound')
        );

        $this->addDimension(
            new Dimension('ok')
        );
    }

    public function ok($value = null)
    {
        if($value !== null) {
            $this->setValue('ok', $value);
            return $this;
        }
        else {
            return $this->getValue('ok');
        }
    }

    public function notFound($value = null)
    {
        if($value !== null) {
            $this->setValue('notFound', $value);
            return $this;
        }
        else {
            return $this->getValue('notFound');
        }
    }



    public function forbidden($value = null)
    {
        if($value !== null) {
            $this->setValue('forbidden', $value);
            return $this;
        }
        else {
            return $this->getValue('forbidden');
        }
    }




}