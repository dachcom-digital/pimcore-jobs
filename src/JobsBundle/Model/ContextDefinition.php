<?php

namespace JobsBundle\Model;

class ContextDefinition implements ContextDefinitionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var bool
     */
    protected $fromClone;

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function isFromClone()
    {
        return $this->fromClone;
    }

    public function __clone()
    {
        $this->fromClone = true;
    }
}
