<?php

namespace Esites\KunstmaanSearchBundle\ValueObjects;

class MatchPercentagesConfiguration
{
    /**
     * @var int
     */
    private $percentageShouldMatchWordInTitle;

    /**
     * @var int
     */
    private $percentageShouldMatchWordInContent;

    public function __construct(
        int $percentageShouldMatchWordInTitle,
        int $percentageShouldMatchWordInContent
    ) {
        $this->percentageShouldMatchWordInTitle = $percentageShouldMatchWordInTitle;
        $this->percentageShouldMatchWordInContent = $percentageShouldMatchWordInContent;
    }

    public function getPercentageShouldMatchWordInTitle(): int
    {
        return $this->percentageShouldMatchWordInTitle;
    }

    public function getPercentageShouldMatchWordInContent(): int
    {
        return $this->percentageShouldMatchWordInContent;
    }

}
