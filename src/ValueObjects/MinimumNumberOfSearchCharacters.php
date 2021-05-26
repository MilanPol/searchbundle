<?php

namespace Esites\KunstmaanSearchBundle\ValueObjects;

class MinimumNumberOfSearchCharacters
{
    /**
     * @var int
     */
    private $minimumNumberOfCharacters;

    public function __construct(int $minimumNumberOfCharacters)
    {
        $this->minimumNumberOfCharacters = $minimumNumberOfCharacters;
    }

    public function getMinimumNumberOfCharacters(): int
    {
        return $this->minimumNumberOfCharacters;
    }
}
