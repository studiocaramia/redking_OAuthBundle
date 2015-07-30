<?php

namespace Redking\Bundle\OAuthBundle\Redirection;
 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
 
class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;
 
    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
 
    /**
     * Redirige une authentification OAuth réussie vers le formulaire de demande d'authorisation
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if (!is_null($token->getUser()->getOauthClient())) {
            $client = $token->getUser()->getOauthClient();
            $redirection = new RedirectResponse($this->router->generate('fos_oauth_server_authorize', array(
                'client_id'     => $client->getPublicId(),
                'response_type' => 'code',
                'redirect_uri'  => $client->getRedirectUris()[0]
                )
            ));
        } else {
            $redirection = new RedirectResponse('/');
        }
 
        return $redirection;
    }
}
