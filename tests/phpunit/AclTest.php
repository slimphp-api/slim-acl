<?php
namespace SlimApi\AclTest;

use SlimApi\Acl\Acl;

/**
 *
 */
class AclTest extends \PHPUnit_Framework_TestCase
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
            'resources' => [
                'banana' => null,
                'orange' => null,
            ],
            'guards' => [
                'resources' => [
                    ['banana', ['user'], ['peel']],
                    ['banana', ['admin']],
                    ['orange', ['guest'], ['peel']],
                    ['orange', ['user'], ['eat']],
                ],
                'callables' => [
                    ['CallableFunction', ['user']],
                ],
                'routes' => [
                    ['/foo',       ['user'],  ['get']],
                    ['/bar',       ['guest'], ['get']],
                ],
            ]
        ], 'guest');
    }

    public function testExceptionFromUnexpectedGuardType()
    {
        $this->setExpectedException('Exception', 'Error Processing Request');
        new Acl([
            'default_role' => 'guest',
            'roles' => [
                'guest' => [],
                'user'  => ['guest'],
                'admin' => ['user']
            ],
            'guards' => [
                'foo' => [
                    ['CallableFunction', ['user']],
                ],
            ]
        ]);
    }

    public function testExceptionFromCallablesArgCount()
    {
        $this->setExpectedException('Exception', 'Error Processing Request');
        new Acl([
            'default_role' => 'guest',
            'roles' => [
                'guest' => [],
                'user'  => ['guest'],
                'admin' => ['user']
            ],
            'guards' => [
                'callables' => [
                    ['CallableFunction', ['user'], 'bar'],
                ],
            ]
        ]);
    }

    public function testExceptionFromRoutesArgCount()
    {
        $this->setExpectedException('Exception', 'Error Processing Request');
        new Acl([
            'default_role' => 'guest',
            'roles' => [
                'guest' => [],
                'user'  => ['guest'],
                'admin' => ['user']
            ],
            'guards' => [
                'routes' => [
                    ['/foo', ['user']],
                ],
            ]
        ]);
    }

    public function testResourcePermissionFail()
    {
        $this->assertFalse($this->acl->isAllowed('guest', 'banana'));
    }


    public function testRoutePermissionFail()
    {
        $this->assertFalse($this->acl->isAllowed('guest', 'route/foo'));
    }


    public function testCallablePermissionFail()
    {
        $this->assertFalse($this->acl->isAllowed('guest', 'callable/CallableFunction'));
    }

    public function testResourcePermissionSuccess()
    {
        $this->assertTrue($this->acl->isAllowed('admin', 'banana'));
    }

    public function testResourcePrivilegePermissionSuccess()
    {
        $this->assertTrue($this->acl->isAllowed('user', 'banana', 'peel'));
    }

    public function testRoutePermissionSuccess()
    {
        $this->assertTrue($this->acl->isAllowed('user', 'route/foo', 'get'));
    }

    public function testCallablePermissionSuccess()
    {
        $this->assertTrue($this->acl->isAllowed('user', 'callable/CallableFunction'));
    }

}
