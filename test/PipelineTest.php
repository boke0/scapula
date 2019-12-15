<?php

namespace Boke0\Scapula\Test;
use \Boke0\Scapula\Pipeline;
use \PHPUnit\Framework\TestCase;
use \Mockery;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

class PipelineTest extends TestCase{
    public function testPipe(){
        $response=Mockery::mock(ResponseInterface::class);
        $middleware=Mockery::mock(MiddlewareInterface::class);
        $middleware->shouldReceive("process")
                   ->andReturn($response);
        $pipeline=new Pipeline();
        $pipeline->pipe($middleware);
        $request=Mockery::mock(ServerRequestInterface::class);
        $response_=$pipeline->handle($request);
        return $this->assertSame($response,$response_);
    }
}
