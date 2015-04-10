<?php

namespace STMS\STMSBundle\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    private $router;
    private $session;

    /**
     * Constructor
     *
     * @param 	RouterInterface $router
     * @param 	Session $session
     */
    public function __construct( RouterInterface $router, Session $session )
    {
        $this->router  = $router;
        $this->session = $session;
    }

    /**
     * onAuthenticationSuccess
     *
     * @param 	Request $request
     * @param 	TokenInterface $token
     * @return 	Response
     */
    public function onAuthenticationSuccess( Request $request, TokenInterface $token )
    {
        return new JsonResponse(array("result" => "success"));
    }

    /**
     * onAuthenticationFailure
     *
     * @param 	Request $request
     * @param 	AuthenticationException $exception
     * @return 	Response
     */
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception )
    {
        return new JsonResponse(array(
            "result" => "error",
            "messages" => array("Invalid email or password. Please try again.")));
    }
}