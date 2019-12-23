<?php

namespace Boke0\Scapula;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ServerRequestFactoryInterface;
use \Psr\Http\Message\UploadedFileFactoryInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Container\ContainerInterface;

class App implements RequestHandlerInterface{
    /**
     * @param ServerRequestFactoryInterface $serverRequestFactory PSR17のサーバーリクエストファクトリ
     * @param UploadedFileFactoryInterface $uploadedFileFactory PSR17のアップロードファイルファクトリ
     */
    public function __construct(
        ServerRequestFactoryInterface $serverRequestFactory,
        UploadedFileFactoryInterface $uploadedFileFactory
    ){
        $this->serverRequestFactory=$serverRequestFactory;
        $this->uploadedFileFactory=$uploadedFileFactory;
        $this->pipeline=new Pipeline();
    }
    public function pipe(MiddlewareInterface $middleware){
        $this->pipeline->pipe($middleware);
    }
    public function handle(ServerRequestInterface $request): ResponseInterface{
        return $this->pipeline->handle($req);
    }
    public function requestFromGlobals(){
        $req=$this->serverRequestFactory->createServerRequest(
            $_SERVER["REQUEST_METHOD"],
            $_SERVER["REQUEST_URI"],
            $_SERVER
        );
        $serverParams=$req->getServerParams();
        if($serverParams["REQUEST_METHOD"]!="GET"){
            switch($serverParams["Content-Type"]){
                case "application/json":
                    $post=json_decode($req->getBody()->getContents());
                    break;
                case "application/x-www-form-urlencoded":
                    $post=parse_str($req->getBody()->getContents());
                    break;
            }
        }else{
            $post=NULL;
        }
        $files=array();
        foreach((array)$_FILES as $name=>$value){
            $files[$name]=$this->uploadedFileFactory->createUploadedFile($value);
        }
        return $req->withCookieParams($_COOKIE)
                    ->withQueryParams($_GET)
                    ->withParsedBody($post)
                    ->withUploadedFiles($files);
    }
    public function run(){
        $res=$this->handle();
        $headers=$res->getHeaders();
        $body=$res->getBody()->getContents();
        foreach($headers as $key=>$value){
            header($key.":".implode(",",$value));
        }
        echo $body;
    }
}
