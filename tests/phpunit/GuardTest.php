<?php
namespace SlimApi\AclTest;

use SlimApi\Acl\Acl;
use SlimApi\Acl\Guard;

use Slim\Route;
use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

/**
 *
 */
class GuardTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->acl = new Acl([
            'default_role' => 'guest',
            'roles' => [
                'guest' => [],
                'user'  => ['guest'],
                'admin' => ['user']
            ],
            'guards' => [
                'callables' => [
                    ['CallableFunction', ['user']],
                ],
                'routes' => [
                    ['/foo', ['user'],  ['get']],
                    ['/bar', ['guest'], ['get']],
                ],
            ]
        ]);
    }

    public function testGuestSuccess()
    {
        // Request
        $uri = Uri::createFromString('https://example.com:443/bar');
        $headers = new Headers();
        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $request = new Request('GET', $uri, $headers, $cookies, $serverParams, $body);
        $request = $request->withAttribute('route', new Route(['get'], '/bar', 'CallableFunction'));

        // Response
        $response = new Response();

        $guard = new Guard($this->acl, 'guest');

        $next = function ($req, $res) {
            return $res;
        };
        $newResponse = $guard($request, $response, $next);
        echo $newResponse->getBody();
        $this->assertEquals(200, $newResponse->getStatusCode());
    }

    public function testGuestNotAllowedByRoute()
    {
        // Request
        $uri = Uri::createFromString('https://example.com:443/foo');
        $headers = new Headers();
        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $request = new Request('GET', $uri, $headers, $cookies, $serverParams, $body);
        $request = $request->withAttribute('route', new Route(['get'], '/foo', 'CallableFunction'));

        // Response
        $response = new Response();

        $guard = new Guard($this->acl, 'guest');

        $next = function ($req, $res) {
            return $res;
        };
        $newResponse = $guard($request, $response, $next);
        $this->assertEquals(403, $newResponse->getStatusCode());
    }

    public function testGuestNotAllowedByCallable()
    {
        // Request
        $uri = Uri::createFromString('https://example.com:443/foo');
        $headers = new Headers();
        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $request = new Request('GET', $uri, $headers, $cookies, $serverParams, $body);
        $request = $request->withAttribute('route', new Route(['get'], '/foo', 'CallableFunction'));

        // Response
        $response = new Response();

        $guard = new Guard($this->acl, 'guest');

        $next = function ($req, $res) {
            return $res;
        };
        $newResponse = $guard($request, $response, $next);
        $this->assertEquals(403, $newResponse->getStatusCode());
    }

    public function testGuestNotAllowedByDefaultCallable()
    {
        // Request
        $uri = Uri::createFromString('https://example.com:443/foo');
        $headers = new Headers();
        $cookies = [];
        $serverParams = [];
        $body = new Body(fopen('php://temp', 'r+'));
        $request = new Request('GET', $uri, $headers, $cookies, $serverParams, $body);
        $request = $request->withAttribute('route', new Route(['get'], '/foo', 'CallableFunction'));

        // Response
        $response = new Response();

        $guard = new Guard($this->acl, 'guest');

        $next = function ($req, $res) {
            return $res;
        };
        $newResponse = $guard($request, $response, $next);
        $this->assertEquals(403, $newResponse->getStatusCode());
    }
}
