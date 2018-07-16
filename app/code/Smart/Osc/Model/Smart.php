<?php
/**
 * Created by PhpStorm.
 * User: dzung
 * Date: 16/07/2018
 * Time: 10:00
 */
namespace Smart\Osc\Model;

class Smart implements \Smart\Osc\Api\SmartInterface
{
    /**
     * {@inheritDoc}
     */
    public function helloWorld(){
        return 'Hello world';
    }
}