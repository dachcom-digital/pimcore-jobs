<?php

namespace JobsBundle\Connector\Facebook\Session;

use Facebook\PersistentData\PersistentDataInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FacebookDataHandler implements PersistentDataInterface
{
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function get($key)
    {
        return $this->session->get('FBRLH_' . $key);
    }

    public function set($key, $value)
    {
        $this->session->set('FBRLH_' . $key, $value);
    }
}
