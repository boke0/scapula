<?php

namespace Boke0\Scapula;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

class Pipeline implements RequestHandlerInterface{
    private $middlewares;
    public function __construct(){
        $this->middlewares=[];
        $this->i=0;
    }
    public function pipe(MiddlewareInterface $middleware){
        array_push($this->middlewares,$middleware);
    }
    public function handle(ServerRequestInterface $serverRequest): ResponseInterface{
        if($this->i>=count($this->middlewares)){
            throw new Exception("No middleware was available");
        }
        $i=$this->i;
        $this->i++;
        return $this->middlewares[$i]->process($serverRequest,$this);
    }
}
