<?php

namespace JobsBundle\Context;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\Concrete;

interface ResolvedItemInterface
{
    /**
     * @return ConnectorContextItemInterface|null
     */
    public function getContextItem();

    /**
     * @return Concrete|null
     */
    public function getSubject();

    /**
     * @return array
     */
    public function getResolvedParams();

    /**
     * @param string $param
     *
     * @return mixed|null
     */
    public function getResolvedParam(string $param);
}
