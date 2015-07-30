<?php
/**
 * Facebook SDK Helper
 */

namespace Redking\Bundle\OAuthBundle\Bridge;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;


class Facebook
{
    
    /**
     * Define facebook config
     * @param [type] $id     [description]
     * @param [type] $secret [description]
     */
    public function __construct($id, $secret)
    {
        if (!is_null($id) && !is_null($id)) {
            FacebookSession::setDefaultApplication($id, $secret);
        }
    }

    /**
     * Return session based on token
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public function getSession($token)
    {
        return new FacebookSession($token);
    }

    /**
     * Return user informations
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public function getUserInformations(FacebookSession $session)
    {
        $me = (new FacebookRequest($session, 'GET', '/me'))
            ->execute()
            ->getGraphObject(GraphUser::className());
        
        return $me;
    }

    /**
     * Retourne l'image principale de l'utilisateur
     * @param  FacebookSession       $session [description]
     * @param  AdvancedUserInterface $user    [description]
     * @return [type]                         [description]
     */
    public function getUserPicture(FacebookSession $session, AdvancedUserInterface $user)
    {
        if (!is_null($user->getFacebookId())) {
            $request = new FacebookRequest(
                $session,
                'GET',
                '/me/picture',
                [
                    'redirect' => false,
                    'type' => 'large'
                ]
            );
            $response = $request->execute();
            $graphObject = $response->getGraphObject();
            
            return $graphObject->getProperty('url');
        }
    }
}
