<?php
/**
 * Service permettant la liaison entre un utilisateur de la base et un utilisateur venant d'un sdk
 */

namespace Redking\Bundle\OAuthBundle\Util;

use FOS\UserBundle\Doctrine\UserManager;
use Facebook\GraphUser;
use OAuth2\OAuth2;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRegistrator
{
    /**
     * @var UserManager
     */
    protected $user_manager;

    /**
     * @var OAuth2
     */
    protected $oauth2;

    /**
     * @var OAuth2
     */
    protected $oauth_manipulator;

    /**
     * @var string
     */
    protected $user_role;

    /**
     * [__construct description]
     * @param UserManager $user_manager [description]
     * @param OAuth2      $oauth2       [description]
     */
    public function __construct(UserManager $user_manager, OAuth2 $oauth2, OAuthManipulator $oauth_manipulator, $user_role)
    {
        $this->user_manager      = $user_manager;
        $this->oauth2            = $oauth2;
        $this->oauth_manipulator = $oauth_manipulator;
        $this->user_role         = $user_role;
    }

    /**
     * Retourne un utilisateur de la base en fonction de l'utilisateur facebook
     * Si il n'existe pas, on en créée un
     * @param  GraphUser $user [description]
     * @return [type]          [description]
     */
    public function getUserFromFacebook(GraphUser $user)
    {
        $email = $user->getEmail();
        if (is_null($email)) {
            throw new \Exception('Facebook user does not share email', 400);
        }
        
        // Get user from email
        $fos_user = $this->user_manager->findUserByEmail($email);

        // Create user if necessary
        if (is_null($fos_user)) {
            $fos_user = $this->user_manager->createUser();
        }
        
        $fos_user->setEnabled(true);
        $fos_user->setEmail($email);
        $fos_user->setFirstName($user->getFirstName());
        $fos_user->setLastName($user->getLastName());
        $fos_user->setRoles([$this->user_role]);

        $fos_user->setFacebookId($user->getId());
        $fos_user->setLocale(substr($user->getProperty('locale'), 0, 2));
        $fos_user->setTimezone($user->getTimezone());

        $this->user_manager->updateCanonicalFields($fos_user);
        $this->user_manager->updateUser($fos_user);

        return $fos_user;
    }

    /**
     * Creates and returns access token for a user
     * @param  AdvancedUserInterface $user [description]
     * @return [type]                      [description]
     */
    public function generateAccessToken(AdvancedUserInterface $user)
    {
        if (is_null($user->getOAuthClient()->getId())) {
            throw new \Exception('User must have an OAuth Client', 500);
        }

        // Search valid token
        $oauth_access_token = $this->oauth_manipulator->getValidTokenForClient($user->getOAuthClient());

        if (!is_null($oauth_access_token)) {
            return $oauth_access_token->getToken();
        }

        // Or else, creates a new one
        
        // Forge request to satisfy OAuth2 server
        $request = new Request();
        $request->query->add([
            'client_id'     => $user->getOAuthClient()->getPublicId(),
            'response_type' => OAuth2::RESPONSE_TYPE_ACCESS_TOKEN,
            'redirect_uri'  => $user->getOAuthClient()->getRedirectUris()[0],
            ]);

        $response = $this->oauth2->finishClientAuthorization(true, $user, $request, null);

        if ($response instanceof Response) {
            
            $location = str_replace('#', '?', $response->headers->get('location'));
            
            $query_string = parse_url($location, PHP_URL_QUERY);
            parse_str($query_string, $queries);
            
            if (isset($queries['access_token'])) {
                $access_token = $queries['access_token'];
                
                return $access_token;
            }
            
        } else {
            throw new Exception("Token creation ; unknown response type : ".get_class($response), 500);
        }
    }
}
