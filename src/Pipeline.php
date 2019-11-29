<?php

namespcae Boke0\Scapula;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Message\ServerRequestInterface;

class Pipeline implements RequestHandlerInterface{
    public function __construct(){
        $this->middlewares=[];
        $this->i=0;
    }
    public function pipe(MiddlewareInterface $middleware){
        array_push($this->middlewares,$middleware);
    }
    public function handler(ServerRequestInterface $serverRequest){
        if($this->i>=count($this->middlewares)){
            throw new Exception("No middleware was available");
        }
        return $this->middlwares[$this->i]->process($serverRequest,$this);
    }
}
