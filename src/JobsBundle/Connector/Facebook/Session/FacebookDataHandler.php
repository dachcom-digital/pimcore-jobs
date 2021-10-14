<?php

namespace JobsBundle\Connector\Facebook\Session;

use Facebook\PersistentData\PersistentDataInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FacebookDataHandler implements PersistentDataInterface
{
    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function get(string $key): mixed
    {
        return $this->session->get('FBRLH_' . $key);
    }

    public function set(string $key, mixed $value): void
    {
        $this->session->set('FBRLH_' . $key, $value);
    }
}
