<?php

namespace Boke0\Scapula;
use \Psr\Http\Server\RequestHandlerInterface;

class Http implements RequestHandlerInterface{
    public function __construct(
        ServerRequestFactory $serverRequestFactory,
        UploadedFileFactory $uploadedFileFactory
    ){
        $this->serverRequestFactory=$serverRequestFactory;
        $this->uploadedFileFactory=$uploadedFileFactory;
        $this->pipeline=new Pipeline();
    }
    public function pipe(MiddlewareInterface $middleware){
        $this->pipeline->pipe($middleware);
    }
    public function dispatch(){
        $req=$this->requestFromGlobals();
        while(!$this->pipeline->isOver()){
            $req=$this->pipeline->handler($req);
        }
        return $req;
    }
    public function requestFromGlobals(){
        $req=$this->serverRequestFactory->createServerRequest()->withServerParams($_SERVER);
        switch($req->getServerParams()["Content-Type"]){
            case "application/json":
                $post=json_decode($req->getBody()->getContents());
                break;
            case "application/x-www-form-urlencoded":
                $post=parse_str($req->getBody()->getContents());
                break;
        }
        $files=array();
        foreach((array)$_FILES as $name=>$value){
            $files[$name]=$this->uploadedFileFactory->createUploadedFile($value);
        }
        return $res->withCookieParams($_COOKIE)
                    ->withQueryParams($_GET)
                    ->withParsedBody($post)
                    ->withUploadedFiles($files);
    }
}
