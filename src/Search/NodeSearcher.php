<?php

namespace Esites\KunstmaanSearchBundle\Search;

use Elastica\Exception\InvalidException;
use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\QueryString;
use Elastica\Query\Term;
use Elastica\Query\Wildcard;
use Elastica\Rescore\Query;
use Elastica\Util;
use Esites\KunstmaanSearchBundle\Exceptions\InvalidRangesException;
use Esites\KunstmaanSearchBundle\ValueObjects\AdditionalSearchFieldValueObject;
use Esites\KunstmaanSearchBundle\ValueObjects\MatchPercentagesConfiguration;
use Esites\KunstmaanSearchBundle\ValueObjects\MinimumNumberOfSearchCharacters;
use Esites\KunstmaanSearchBundle\ValueObjects\TierBoostValuesConfiguration;
use Esites\KunstmaanSearchBundle\ValueObjects\WildCardConfiguration;
use Esites\KunstmaanSearchBundle\Constant\ConfigConstants;
use Kunstmaan\NodeSearchBundle\Search\NodeSearcher as BaseNodeSearcher;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NodeSearcher extends BaseNodeSearcher
{
    /**
     * @var WildCardConfiguration
     */
    private $wildcardConfiguration;

    /**
     * @var MatchPercentagesConfiguration
     */
    private $matchPercentageConfiguration;

    /**
     * @var TierBoostValuesConfiguration
     */
    private $tierBoostValuesConfiguration;

    /**
     * @var MinimumNumberOfSearchCharacters
     */
    private $minimumNumberOfSearchCharacters;

    /**
     * @var AdditionalSearchFieldValueObject[]
     */
    private $additionalFields = [];

    /**
     * @throws InvalidRangesException
     */
    public function setConfigValues(ContainerInterface $container): void
    {
        $this->wildcardConfiguration = new WildCardConfiguration(
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::USE_WILDCARD_PREFIX
                )
            ),
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::USE_WILDCARD_SUFFIX
                )
            )
        );

        $this->matchPercentageConfiguration = new MatchPercentagesConfiguration(
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_TITLE
                )
            ),
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_CONTENT
                )
            )
        );

        $additionalFields = $container->getParameter(
            ConfigConstants::getParameterKeyName(
                ConfigConstants::ADDITIONAL_FIELDS
            )
        );

        if (is_array($additionalFields)) {
            foreach ($additionalFields as $additionalField) {
                $this->additionalFields[] = new AdditionalSearchFieldValueObject(
                    $additionalField[ConfigConstants::ADDITIONAL_FIELDS_NAME],
                    $additionalField[ConfigConstants::ADDITIONAL_FIELDS_FRAGMENT_SIZE],
                    $additionalField[ConfigConstants::ADDITIONAL_FIELDS_NUMBER_OF_FRAGMENTS]
                );
            }
        }

        $this->tierBoostValuesConfiguration = new TierBoostValuesConfiguration(
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::BOOST_TIER_CONTENT_QUERY_WITH_WILDCARD
                )
            ),
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::BOOST_TIER_CONTENT_QUERY_WITHOUT_WILDCARD
                )
            ),
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::BOOST_TIER_TITLE_QUERY_WITH_WILDCARD
                )
            ),
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::BOOST_TIER_TITLE_QUERY_WITHOUT_WILDCARD
                )
            )
        );

        $this->minimumNumberOfSearchCharacters = new MinimumNumberOfSearchCharacters(
            $container->getParameter(
                ConfigConstants::getParameterKeyName(
                    ConfigConstants::MINIMUM_NUMBER_OF_CHARACTERS_TO_START_SEARCH
                )
            )
        );
    }

    /**
     * Like queries should be less boosted -> orderin displayment:
     * 1. Exact match in title
     * 2. Like match in title
     * 3. Exact match in content
     * 4. Like match in content
     *
     * @throws InvalidException
     */
    public function defineSearch($query, $type): void
    {
        $elasticaQueryOnTitle = $this->getTitleMatch($query);
        $elasticaQueryOnContent = $this->getContentQuery($query);

        $elasticaQueryWildcardOnTitle = $this->getWildcardTitleQuery($query);
        $elasticaQueryWildcardOnContent = $this->getWildcardContentQuery($query);

        $elasticaQueryBool = new BoolQuery();
        $elasticaQueryBool
            ->addShould($elasticaQueryOnTitle)
            ->addShould($elasticaQueryOnContent)
            ->setMinimumShouldMatch(1)
        ;

        if ($this->hasEnoughSearchCharacters($query)) {
            $elasticaQueryBool->addShould($elasticaQueryWildcardOnTitle);
            $elasticaQueryBool->addShould($elasticaQueryWildcardOnContent);
        }

        $this->applySecurityFilter($elasticaQueryBool);

        if ($type !== null) {
            $elasticaQueryType = new Term();
            $elasticaQueryType->setTerm('type', $type);
            $elasticaQueryBool->addMust($elasticaQueryType);
        }

        $rootNode = $this->domainConfiguration->getRootNode();

        if ($rootNode !== null) {
            $elasticaQueryRoot = new Term();
            $elasticaQueryRoot->setTerm('root_id', $rootNode->getId());
            $elasticaQueryBool->addMust($elasticaQueryRoot);
        }

        $highlightFields = $this->setAdditionalSearchFields(
            $elasticaQueryBool,
            $query
        );

        $rescore = new Query();
        $rescore->setRescoreQuery($this->getPageBoosts());

        $this->query->setQuery($elasticaQueryBool);
        $this->query->setRescore($rescore);
        $this->query->setHighlight(
            [
                'pre_tags' => ['<strong>'],
                'post_tags' => ['</strong>'],
                'fields' => $highlightFields
            ]
        );
    }

    private function getTitleMatch(string $nonEscapedTerm): AbstractQuery
    {
        $term = Util::escapeTerm($nonEscapedTerm);

        if ($this->useMatchQueryForTitle) {
            $elasticaQueryTitle = new Match();
            $elasticaQueryTitle
                ->setFieldQuery('title', $term)
                ->setFieldBoost($this->tierBoostValuesConfiguration->getTierBoostTitleWithoutWildcard())
                ->setFieldMinimumShouldMatch(
                    'title',
                    $this->matchPercentageConfiguration->getPercentageShouldMatchWordInTitle().'%'
                )
            ;

            return $elasticaQueryTitle;
        }

        $elasticaQueryTitle = new QueryString();
        $elasticaQueryTitle
            ->setDefaultField('title')
            ->setBoost($this->tierBoostValuesConfiguration->getTierBoostTitleWithoutWildcard())
            ->setQuery($term)
        ;

        return $elasticaQueryTitle;
    }

    private function getContentQuery(string $nonEscapedTerm): AbstractQuery
    {
        $term = Util::escapeTerm($nonEscapedTerm);

        $elasticaQueryStringOnContent = new Match();
        $elasticaQueryStringOnContent
            ->setFieldMinimumShouldMatch(
                'content',
                $this->matchPercentageConfiguration->getPercentageShouldMatchWordInContent().'%'
            )
            ->setFieldBoost('content', $this->tierBoostValuesConfiguration->getTierBoostContentWithoutWildcard())
            ->setFieldQuery('content', $term)
        ;

        return $elasticaQueryStringOnContent;
    }

    private function getWildcardTitleQuery(string $nonEscapedTerm): Wildcard
    {
        $likeQueryParam = $this->getWildcardTerm($nonEscapedTerm);

        return new Wildcard(
            'title', $likeQueryParam,
            $this->tierBoostValuesConfiguration->getTierBoostTitleWithWildcard()
        );
    }

    private function getWildcardContentQuery(string $nonEscapedTerm): Wildcard
    {
        $likeQueryParam = $this->getWildcardTerm($nonEscapedTerm);

        return new Wildcard(
            'content', $likeQueryParam,
            $this->tierBoostValuesConfiguration->getTierBoostContentWithWildcard()
        );
    }

    private function getWildcardTerm(string $nonEscapedTerm): string
    {
        $orginQuery = str_replace('"', '', $nonEscapedTerm);

        $prefix = $this->wildcardConfiguration->isUseWildcardPrefix() ? '*' : '';
        $suffix = $this->wildcardConfiguration->isUseWildcardSuffix() ? '*' : '';

        return $prefix.strtolower(Util::escapeTerm($orginQuery)).$suffix;
    }

    private function hasEnoughSearchCharacters(string $query): bool
    {
        $stringToCount = str_replace('""', '', $query);

        if (empty($stringToCount)) {
            return false;
        }

        if ($this->minimumNumberOfSearchCharacters->getMinimumNumberOfCharacters() > strlen($stringToCount)) {
            return false;
        }

        return true;
    }

    private function setAdditionalSearchFields(BoolQuery $elasticaQueryBool, string $query): array
    {
        $highlightFields = [
            'content' => [
                'fragment_size' => 100,
                'number_of_fragments' => 3,
            ]
        ];

        foreach ($this->additionalFields as $additionalSearchField) {
            if (!$additionalSearchField instanceof AdditionalSearchFieldValueObject) {
                continue;
            }

            $queryString = new QueryString();
            $queryString
                ->setDefaultField($additionalSearchField->getField())
                ->setQuery($query)
            ;

            $elasticaQueryBool
                ->addShould($queryString)
            ;

            $highlightFields[$additionalSearchField->getField()] = [
                'fragment_size' => $additionalSearchField->getFragmentSize(),
                'number_of_fragments' => $additionalSearchField->getNumberOfFragments(),
            ];
        }

        return $highlightFields;
    }
}
