<?php

namespace Redking\Bundle\OAuthBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\OAuthServerBundle\Document\RefreshToken as BaseRefreshToken;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;  


/**
 * @MongoDB\Document(collection="oauthRefreshToken")
 */
class RefreshToken extends BaseRefreshToken 
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
