<?php

namespace Oro\Bundle\FeatureToggleBundle\Configuration;

use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class FeatureToggleConfiguration implements ConfigurationInterface
{
    const ROOT = 'features';

    /**
     * @var array|ConfigurationExtensionInterface[]
     */
    protected $extensions = [];

    /**
     * @param ConfigurationExtensionInterface $extension
     */
    public function addExtension(ConfigurationExtensionInterface $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root(self::ROOT);

        $children = $root->useAttributeAsKey('name')->prototype('array')->children();

        $root->end();

        $this->addFeatureConfiguration($children);
        foreach ($this->extensions as $extension) {
            $extension->extendConfigurationTree($children);
        }

        $children->end();

        return $builder;
    }

    /**
     * @param ArrayNode $node
     */
    protected function addFeatureConfiguration(NodeBuilder $node)
    {
        $node
            ->scalarNode('toggle')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->scalarNode('label')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->scalarNode('description')
            ->end()
            ->arrayNode('dependency')
                ->prototype('variable')
                ->end()
            ->end()
            ->arrayNode('route')
                ->prototype('variable')
                ->end()
            ->end()
            ->arrayNode('configuration')
                ->prototype('variable')
                ->end()
            ->end();
    }

    /**
     * @param array $configs
     * @return array
     */
    public function processConfiguration(array $configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration($this, [$configs]);
    }
}
