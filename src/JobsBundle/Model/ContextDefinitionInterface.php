<?php

namespace JobsBundle\Model;

interface ContextDefinitionInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $host
     */
    public function setHost(string $host);

    /**
     * @return string
     */
    public function getHost();

    /**
     * @param string $locale
     */
    public function setLocale(string $locale);

    /**
     * @return string
     */
    public function getLocale();
}
