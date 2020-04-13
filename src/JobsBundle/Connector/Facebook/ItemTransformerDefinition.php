<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Transformer\ItemTransformerDefinitionInterface;

class ItemTransformerDefinition implements ItemTransformerDefinitionInterface
{
    protected $title;

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->getTitle()
        ];
    }
}
