<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use Spatie\SchemaOrg\Graph;

class ItemTransformerDefinition implements ItemTransformerDefinitionInterface
{
    /**
     * @var Graph
     */
    protected $graph;

    public function setGraph(Graph $graph)
    {
        $this->graph = $graph;
    }

    public function getGraph()
    {
        return $this->graph;
    }
}
