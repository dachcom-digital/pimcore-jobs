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

    /**
     * @param Graph $graph
     */
    public function setGraph(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }
}
