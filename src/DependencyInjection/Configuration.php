<?php

namespace Esites\KunstmaanSearchBundle\DependencyInjection;

use Esites\KunstmaanSearchBundle\Constant\ConfigConstants;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(ConfigConstants::PREFIX_KEY);

        $rootNode
            ->children()
            ->booleanNode(ConfigConstants::USE_WILDCARD_PREFIX)
                ->defaultTrue()
                ->end()
            ->booleanNode(ConfigConstants::USE_WILDCARD_SUFFIX)
                ->defaultTrue()
                ->end()
            ->integerNode(ConfigConstants::MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_TITLE)
                ->min(1)
                ->max(100)
                ->defaultValue(80)
                ->end()
            ->integerNode(ConfigConstants::MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_CONTENT)
                ->min(1)
                ->max(100)
                ->defaultValue(80)
                ->end()
            // look at it as a multiplier -> the higher the better!
            ->integerNode(ConfigConstants::BOOST_TIER_CONTENT_QUERY_WITH_WILDCARD)
                ->min(1)
                ->max(100)
                ->defaultValue(1)
                ->end()
            ->integerNode(ConfigConstants::BOOST_TIER_CONTENT_QUERY_WITHOUT_WILDCARD)
                ->min(1)
                ->max(100)
                ->defaultValue(2)
                ->end()
            ->integerNode(ConfigConstants::BOOST_TIER_TITLE_QUERY_WITH_WILDCARD)
                ->min(1)
                ->max(100)
                ->defaultValue(3)
                ->end()
            ->integerNode(ConfigConstants::BOOST_TIER_TITLE_QUERY_WITHOUT_WILDCARD)
                ->min(1)
                ->max(100)
                ->defaultValue(4)
                ->end()
            ->integerNode(ConfigConstants::MINIMUM_NUMBER_OF_CHARACTERS_TO_START_SEARCH)
                ->min(1)
                ->max(20)
                ->defaultValue(3)
                ->end()
            ->arrayNode(ConfigConstants::ADDITIONAL_FIELDS)
                ->prototype('array')
                    ->children()
                        ->scalarNode(ConfigConstants::ADDITIONAL_FIELDS_NAME)
                            ->end()
                        ->integerNode(ConfigConstants::ADDITIONAL_FIELDS_FRAGMENT_SIZE)
                            ->defaultValue(100)
                            ->end()
                        ->integerNode(ConfigConstants::ADDITIONAL_FIELDS_NUMBER_OF_FRAGMENTS)
                            ->defaultValue(3)
                            ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
