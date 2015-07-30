<?php

namespace Redking\Bundle\OAuthBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\OAuthServerBundle\Document\AccessToken as BaseAccessToken;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;  


/**
 * @MongoDB\Document(collection="oauthAccessToken")
 */
class AccessToken extends BaseAccessToken 
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Client")
     */
    protected $client;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sonata\UserBundle\Model\UserInterface")
     */
    protected $user;

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        if (!is_null($this->client->getUser()) && !is_null($this->client->getUser()->getId())) {
            $this->setUser($this->client->getUser());
        }
    }

    public function getUser()  
    {  
        return $this->user;  
    }  
  
    public function setUser(UserInterface $user)  
    {  
        $this->user = $user;  
    }

}
