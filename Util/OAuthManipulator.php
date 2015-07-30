<?php

namespace Redking\Bundle\OAuthBundle\Util;

use FOS\OAuthServerBundle\Model\ClientInterface;

class OAuthManipulator
{
    protected $dm;


    public function __construct($doctrine)
    {
        $this->dm = $doctrine->getManager();
    }


    /**
     * Find valid oauth access token for client
     * @param  ClientInterface $client [description]
     * @return [type]                  [description]
     */
    public function getValidTokenForClient(ClientInterface $client)
    {
        return $this->dm
                ->getRepository('RedkingOAuthBundle:AccessToken')
                ->createQueryBuilder()
                ->field('client')->references($client)
                ->field('expiresAt')->gte(time())
                ->getQuery()
                ->getSingleResult()
        ;
    }

}
