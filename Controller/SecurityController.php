<?php

namespace Redking\Bundle\OAuthBundle\Controller;  
  
use Symfony\Bundle\FrameworkBundle\Controller\Controller;  
use Symfony\Component\HttpFoundation\Request;  
use Symfony\Component\Security\Core\SecurityContext;  
  
class SecurityController extends Controller  
{  
    public function loginAction(Request $request)  
    {  
        $session = $request->getSession();  
          
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {  
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);  
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {  
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);  
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);  
        } else {  
            $error = '';  
        }  
  
        if ($error) {  
            $error = $error->getMessage(); // WARNING! Symfony source code identifies this line as a potential security threat.  
        }  
          
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);  
  
        return $this->render('RedkingOAuthBundle:Security:login.html.twig', array(  
            'last_username' => $lastUsername,  
            'error'         => $error,  
        ));  
    }  
      
    public function loginCheckAction(Request $request)  
    {  
          
    }

    /**
     * Get Authorization code
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getCodeAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        if (!$request->query->has('code'))
        {
            throw $this->createNotFoundException('No code param');
        }
        $code = $this
            ->get('doctrine_mongodb.odm.document_manager')
            ->getRepository('RedkingOAuthBundle:AuthCode')
            ->findOneByToken($request->query->get('code'));

        if (is_null($code)) {
            throw $this->createNotFoundException('Non existing code');
        }

        if ($user->getId() !== $code->getUser()->getId())
        {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        // redirect to token
        return $this->redirect($this->generateUrl('fos_oauth_server_token', array(
            'client_id'     => $code->getClient()->getPublicId(),
            'client_secret' => $code->getClient()->getSecret(),
            'redirect_uri'  => $code->getClient()->getRedirectUris()[0],
            'code'          => $code->getToken(),
            'grant_type'    => 'authorization_code',
            )));
    }
}  
