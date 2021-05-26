<?php

namespace Esites\KunstmaanSearchBundle\ValueObjects;

class WildCardConfiguration
{
    /**
     * @var bool
     */
    private $useWildcardPrefix;

    /**
     * @var bool
     */
    private $useWildcardSuffix;

    public function __construct(
        bool $useWildcardPrefix,
        bool $useWildcardSuffix
    ) {
        $this->useWildcardPrefix = $useWildcardPrefix;
        $this->useWildcardSuffix = $useWildcardSuffix;
    }

    public function isUseWildcardPrefix(): bool
    {
        return $this->useWildcardPrefix;
    }

    public function isUseWildcardSuffix(): bool
    {
        return $this->useWildcardSuffix;
    }
}
