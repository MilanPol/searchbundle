<?php

namespace Esites\KunstmaanSearchBundle\Constant;

class ConfigConstants
{
    public const PREFIX_KEY = 'esites_kunstmaan_search';

    public const USE_WILDCARD_PREFIX = 'use_prefix_wildcard';
    public const USE_WILDCARD_SUFFIX = 'use_suffix_wildcard';
    public const MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_TITLE = 'minimum_percentage_should_match_word_in_title';
    public const MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_CONTENT = 'minimum_percentage_should_match_word_in_content';

    public const MINIMUM_NUMBER_OF_CHARACTERS_TO_START_SEARCH = 'minimum_number_of_characters_to_start_search';

    /**
     * The higher the better. So the highest in this setup would be BOOST_TIER_TITLE_QUERY_WITHOUT_WILDCARD: Since it is the title field and that (sub)query does not make use of a wildcard
     */
    public const BOOST_TIER_CONTENT_QUERY_WITH_WILDCARD = 'boost_tier_query_content_with_wildcard';
    public const BOOST_TIER_CONTENT_QUERY_WITHOUT_WILDCARD = 'boost_tier_query_content_without_wildcard';
    public const BOOST_TIER_TITLE_QUERY_WITH_WILDCARD = 'boost_tier_query_title_with_wildcard';
    public const BOOST_TIER_TITLE_QUERY_WITHOUT_WILDCARD = 'boost_tier_query_title_without_wildcard';

    public const ADDITIONAL_FIELDS = 'additional_fields';
    public const ADDITIONAL_FIELDS_NAME = 'name';
    public const ADDITIONAL_FIELDS_FRAGMENT_SIZE = 'fragment_size';
    public const ADDITIONAL_FIELDS_NUMBER_OF_FRAGMENTS = 'number_of_fragments';

    public static function getParameterKeyName(string $key): string
    {
        return static::PREFIX_KEY . '.' . $key;
    }

    public static function getConfiguration(): array
    {
        return [
            static::USE_WILDCARD_PREFIX,
            static::USE_WILDCARD_SUFFIX,
            static::MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_TITLE,
            static::MINIMUM_PERCENTAGE_SHOULD_MATCH_WORD_IN_CONTENT,
            static::MINIMUM_NUMBER_OF_CHARACTERS_TO_START_SEARCH,
            static::BOOST_TIER_CONTENT_QUERY_WITH_WILDCARD,
            static::BOOST_TIER_CONTENT_QUERY_WITHOUT_WILDCARD,
            static::BOOST_TIER_TITLE_QUERY_WITH_WILDCARD,
            static::BOOST_TIER_TITLE_QUERY_WITHOUT_WILDCARD,
            static::ADDITIONAL_FIELDS,
        ];
    }
}
