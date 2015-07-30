<?php

namespace Redking\Bundle\OAuthBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use FOS\OAuthServerBundle\Document\Client as BaseClient;
use OAuth2\OAuth2;
use JMS\Serializer\Annotation as Serializer;

/**
 * @MongoDB\Document(collection="oauthClient")
 * @Serializer\ExclusionPolicy("all")
 */
class Client extends BaseClient 
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $name;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sonata\UserBundle\Model\UserInterface", mappedBy="oauth_client")
     */
    protected $user;

    public function getName()  
    {  
        return $this->name;  
    }  
  
    public function setName($name)  
    {  
        $this->name = $name;  
    }   

    public static function getGrantChoices()
    {
        return array(
            OAuth2::GRANT_TYPE_AUTH_CODE          => OAuth2::GRANT_TYPE_AUTH_CODE,
            OAuth2::GRANT_TYPE_IMPLICIT           => OAuth2::GRANT_TYPE_IMPLICIT,
            OAuth2::GRANT_TYPE_USER_CREDENTIALS   => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
            OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS => OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
            OAuth2::GRANT_TYPE_REFRESH_TOKEN      => OAuth2::GRANT_TYPE_REFRESH_TOKEN,
            OAuth2::GRANT_TYPE_EXTENSIONS         => OAuth2::GRANT_TYPE_EXTENSIONS,
        );
    }

    public function __toString()
    {
        if (!is_null($this->getName())) {
            return $this->getName();
        }
        return '';
    }

    /**
     * [setUser description]
     * @param \Sonata\UserBundle\Model\UserInterface $user [description]
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * [getUser description]
     * @return [type] [description]
     */
    public function getUser()
    {
        return $this->user;
    }
}
