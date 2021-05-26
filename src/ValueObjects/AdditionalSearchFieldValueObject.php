<?php

namespace Esites\KunstmaanSearchBundle\ValueObjects;

class AdditionalSearchFieldValueObject
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var int
     */
    private $fragmentSize;

    /**
     * @var int
     */
    private $numberOfFragments;


    public function __construct(
        string $field,
        int $fragmentSize,
        int $numberOfFragments
    ) {

        $this->field = $field;
        $this->fragmentSize = $fragmentSize;
        $this->numberOfFragments = $numberOfFragments;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getFragmentSize(): int
    {
        return $this->fragmentSize;
    }

    public function getNumberOfFragments(): int
    {
        return $this->numberOfFragments;
    }
}
