<?php
namespace Neos\ContentRepository\NodeAccess\FlowQueryOperations;

/*
 * This file is part of the Neos.ContentRepository package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Core\Projection\ContentGraph\ContentSubgraphInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindBackReferencesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Reference;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Eel\FlowQuery\OperationInterface;
use Neos\Flow\Annotations as Flow;

/**
 * "backReferenceNodes" operation working on Nodes
 *
 * This operation can be used to find the nodes that are referencing a given node:
 *
 *     ${q(node).backReferenceNodes().get()}
 *
 * A referenceName can be specified as argument
 *
 *     ${q(node).backReferenceNodes("someReferenceName")}
 *
 * @see ReferenceNodesOperation
 * @api To be used in Fusion, for PHP code {@see ContentSubgraphInterface::findBackReferences()} should be used instead
 */
final class BackReferenceNodesOperation implements OperationInterface
{

    /**
     * @Flow\Inject
     * @var ContentRepositoryRegistry
     */
    protected $contentRepositoryRegistry;

    public function canEvaluate($context): bool
    {
        return count($context) === 0 || (isset($context[0]) && ($context[0] instanceof Node));
    }

    public function evaluate(FlowQuery $flowQuery, array $arguments): void
    {
        $output = [];
        $filter = FindBackReferencesFilter::create();
        if (isset($arguments[0])) {
            $filter = $filter->with(referenceName: $arguments[0]);
        }
        /** @var Node $contextNode */
        foreach ($flowQuery->getContext() as $contextNode) {
            $subgraph = $this->contentRepositoryRegistry->subgraphForNode($contextNode);
            $output[] = iterator_to_array($subgraph->findBackReferences($contextNode->nodeAggregateId, $filter));
        }
        $flowQuery->setContext(array_map(fn(Reference $reference) => $reference->node, array_merge(...$output)));
    }

    public static function getShortName(): string
    {
        return 'backReferenceNodes';
    }

    public static function getPriority(): int
    {
        return 100;
    }

    public static function isFinal(): bool
    {
        return false;
    }
}
