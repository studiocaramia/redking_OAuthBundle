<?php

namespace Redking\Bundle\OAuthBundle\Controller;  

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Facebook\FacebookRequestException;
use JMS\Serializer\SerializationContext;

use Redking\Bundle\OAuthBundle\Event\Event,
    Redking\Bundle\OAuthBundle\Event\Events;

class FacebookController extends Controller
{
    /**
     * Get facebook token session
     * User creation 
     * Returns app OAuth token
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getTokenAction(Request $request)
    {
        if (!$request->request->has("token")) {
            throw new \Symfony\Component\Routing\Exception\MissingMandatoryParametersException();
        }

        // Récupération du user facebook
        $token = $request->request->get("token");

        // Facebook id
        $token = $request->request->get("token");
        $id = $request->request->get("id");

        $facebook = $this->get('redking_oauth.facebook');

        $session = $facebook->getSession($token);

        try {
            $user = $facebook->getUserInformations($session);

            // Vérification du facebook id fourni en param
            if (!is_null($id) && $id != $user->getId()) {
                return $this->handleResponse($request, ['message' => 'Id mismatch user facebook id'], 400);
            }
        } catch (FacebookRequestException  $e) {
            return $this->handleResponse($request, ['code' => $e->getHttpStatusCode(), 'message' => $e->getMessage()], $e->getHttpStatusCode());
        }

        // Association du user facebook avec un user en base
        $user_registrator = $this->get('redking_oauth.user.registration');
        try {
            $fos_user = $user_registrator->getUserFromFacebook($user);

            // Si l'objet fos user contient la méthode pour gérer la photo facebook, on la récupère
            if (method_exists($fos_user, 'handleFacebookPicture')) {
                $cache_dir = $this->get('kernel')->getCacheDir();
                $fos_user->handleFacebookPicture($facebook->getUserPicture($session, $fos_user), $cache_dir);

                // Resave user
                $this->get('fos_user.user_manager')->updateUser($fos_user);
            }
            
            $this->get('event_dispatcher')->dispatch(
                Events::USER_CREATED_BY_BRIDGE,
                new Event($fos_user, 'facebook')
            );

        } catch (\Exception $e) {
            return $this->handleResponse($request, ['code' => $e->getCode(), 'message' => $e->getMessage()], $e->getCode());
        }

        // Renvoi du user
        return $this->handleResponse($request, $fos_user);
        
    }

    /**
     * Returns response based on request content type
     * @param  Request $request       [description]
     * @param  [type]  $response_data [description]
     * @param  integer $response_code [description]
     * @return [type]                 [description]
     */
    protected function handleResponse(Request $request, $response_data, $response_code = 200)
    {
        $response = new Response();
        
        $contentType = $request->headers->get('Content-Type');
        $format = null === $contentType
                ? $request->getRequestFormat()
                : $request->getFormat($contentType);

        if ($format == 'json') {
            $serializer = $this->get('serializer');
            $serialization_context = SerializationContext::create()->enableMaxDepthChecks()->setGroups(array('Default', 'detail', 'from_user', 'from_oauth'));

            $response->setContent($serializer->serialize($response_data, 'json', $serialization_context));
            $response->headers->set('Content-Type', 'application/json');
        } else {
            if (is_array($response_data)) {
                if (isset($response_data['message'])) {
                    $response->setContent($response_data['message']);
                } else {
                    $response->setContent(json_encode($response_data));
                }
            }
        }
        if ($response_code == 0) {
            $response->setStatusCode(500);
        } else {
            $response->setStatusCode($response_code);
        }
        
        return $response;
    }
}
