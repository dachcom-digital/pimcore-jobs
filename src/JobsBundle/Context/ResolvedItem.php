<?php

namespace JobsBundle\Context;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\Concrete;

class ResolvedItem implements ResolvedItemInterface
{
    /**
     * @var ConnectorContextItemInterface
     */
    protected $contextItem;

    /**
     * @var Concrete
     */
    protected $subject;

    /**
     * @var array
     */
    protected $resolvedParams;

    /**
     * @param ConnectorContextItemInterface|null $contextItem
     * @param Concrete|null                      $subject
     * @param array                              $resolvedParams
     */
    public function __construct(?ConnectorContextItemInterface $contextItem, ?Concrete $subject, array $resolvedParams = [])
    {
        $this->contextItem = $contextItem;
        $this->subject = $subject;
        $this->resolvedParams = $resolvedParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextItem()
    {
        return $this->contextItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolvedParams()
    {
        return $this->resolvedParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolvedParam(string $param)
    {
        return isset($this->resolvedParams[$param]) ? $this->resolvedParams[$param] : null;
    }
}
