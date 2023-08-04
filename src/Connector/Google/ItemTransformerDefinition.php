<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use Spatie\SchemaOrg\Graph;

class ItemTransformerDefinition implements ItemTransformerDefinitionInterface
{
    protected Graph $graph;

    public function setGraph(Graph $graph): void
    {
        $this->graph = $graph;
    }

    public function getGraph(): Graph
    {
        return $this->graph;
    }
}
