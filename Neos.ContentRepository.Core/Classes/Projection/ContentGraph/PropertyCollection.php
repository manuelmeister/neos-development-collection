<?php

/*
 * This file is part of the Neos.ContentRepository package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

declare(strict_types=1);

namespace Neos\ContentRepository\Core\Projection\ContentGraph;

use Neos\ContentRepository\Core\Feature\NodeModification\Dto\SerializedPropertyValues;
use Neos\ContentRepository\Core\Infrastructure\Property\PropertyConverter;

/**
 * The property collection implementation
 *
 * @implements \IteratorAggregate<string,mixed>
 * @api
 */
final class PropertyCollection implements \IteratorAggregate, \Countable
{
    /**
     * Properties from Nodes
     */
    private SerializedPropertyValues $serializedPropertyValues;

    /**
     * @var array<string,mixed>
     */
    private array $deserializedPropertyValuesRuntimeCache = [];

    private PropertyConverter $propertyConverter;

    /**
     * @internal do not create from userspace
     */
    public function __construct(
        SerializedPropertyValues $serializedPropertyValues,
        PropertyConverter $propertyConverter
    ) {
        $this->serializedPropertyValues = $serializedPropertyValues;
        $this->propertyConverter = $propertyConverter;
    }

    public function has(string $propertyName): bool
    {
        return $this->serializedPropertyValues->propertyExists($propertyName);
    }

    public function get(string $propertyName): mixed
    {
        if (array_key_exists($propertyName, $this->deserializedPropertyValuesRuntimeCache)) {
            return $this->deserializedPropertyValuesRuntimeCache[$propertyName];
        }

        $serializedProperty = $this->serializedPropertyValues->getProperty($propertyName);
        if ($serializedProperty === null) {
            return null;
        }
        return $this->deserializedPropertyValuesRuntimeCache[$propertyName] =
            $this->propertyConverter->deserializePropertyValue($serializedProperty);
    }

    /**
     * @return \Generator<string,mixed>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->serializedPropertyValues as $propertyName => $_) {
            yield $propertyName => $this->get($propertyName);
        }
    }

    public function serialized(): SerializedPropertyValues
    {
        return $this->serializedPropertyValues;
    }

    public function count(): int
    {
        return count($this->serializedPropertyValues);
    }
}
