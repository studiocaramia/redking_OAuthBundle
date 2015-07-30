<?php

namespace Redking\Bundle\OAuthBundle\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Sonata\UserBundle\Model\UserInterface;

/**
 * Base class for Redking Core Rest Events
 */
class Event extends BaseEvent
{
    protected $user;
    
    protected $origin;


    /**
     * [__construct description]
     * @param UserInterface     $user        [description]
     * @param string            $origin    [description]
     */
    public function __construct(UserInterface $user, $origin)
    {
        $this->user   = $user;
        $this->origin = $origin;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

}
