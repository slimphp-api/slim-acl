<?php
namespace SlimApi\Acl;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Permissions\Acl\AclInterface;

class Guard
{
    /**
     * @param  Array $acl The preconfigured ACL service
     */
    public function __construct(AclInterface $acl, $currentUserRole)
    {
        $this->acl             = $acl;
        $this->currentUserRole = $currentUserRole;
    }

    /**
     * Invoke middleware
     *
     * @param  RequestInterface  $request  PSR7 request object
     * @param  ResponseInterface $response PSR7 response object
     * @param  callable          $next     Next middleware callable
     *
     * @return ResponseInterface PSR7 response object
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $isAllowed = false;

        if ($this->acl->hasResource('route'.$request->getAttribute('route')->getPattern())) {
            $isAllowed = $isAllowed || $this->acl->isAllowed($this->currentUserRole, 'route'.$request->getAttribute('route')->getPattern(), strtolower($request->getMethod()));
        }

        if ($this->acl->hasResource('callable/'.$request->getAttribute('route')->getCallable())) {
            $isAllowed = $isAllowed || $this->acl->isAllowed($this->currentUserRole, 'callable/'.$request->getAttribute('route')->getCallable());
        }

        if (!$isAllowed) {
            return $response->withStatus(403, $this->currentUserRole.' is not allowed access to this location.');
        }
        return $next($request, $response);
    }

}
