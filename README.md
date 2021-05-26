# E-sites Kunstmaan Search Bundle 

Symfony bundle for use in Kunstmaan 5.x projects. 

## Installation

1. Add repository to your composer.json. [Make sure you added your SSH key](https://git.e-sites.nl/user/settings/ssh) to git.e-sites.nl.

    ```json
    ...
    "repositories": [
        {
          "type": "vcs",
          "url": "https://gitlab.e-sites.nl/e-sites/kunstmaan/searchbundle.git"
        }
      ]
    ...
    ```

2. Require the bundle
    
    ```bash
    $ composer require esites/kunstmaan-search-bundle
    ```    


### SearchBundle

This bundle extends the default search bundle provided by Kunstmaan. It adds wildcard search support and a few configurable options.

1. Enable this bundle in your AppKernel

2. The following options are currently available for configuration (set with their current default values)

    ```yaml
    esites_kunstmaan_search:
        use_prefix_wildcard: true
        use_suffix_wildcard: true
        minimum_percentage_should_match_word_in_title: 80
        minimum_percentage_should_match_word_in_content: 80
        boost_tier_query_content_with_wildcard: 1
        boost_tier_query_content_without_wildcard: 2
        boost_tier_query_title_with_wildcard: 3
        boost_tier_query_title_without_wildcard: 4
        minimum_amount_of_characters_to_start_search: 3
        additional_fields:
           -
              name: intro (required if additional_fields is defined)
              fragment_size: 100 (not required, default = 100)
              number_of_fragments: 3 (not required, default = 3)
           -
              name: description
              fragment_size: 200
              number_of_fragments: 2
    ```