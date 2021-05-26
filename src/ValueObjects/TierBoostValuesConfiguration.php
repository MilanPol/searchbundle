<?php

namespace Esites\KunstmaanSearchBundle\ValueObjects;

use Esites\KunstmaanSearchBundle\Exceptions\InvalidRangesException;

class TierBoostValuesConfiguration
{
    /**
     * @var int
     */
    private $tierBoostContentWithWildcard;

    /**
     * @var int
     */
    private $tierBoostContentWithoutWildcard;

    /**
     * @var int
     */
    private $tierBoostTitleWithWildcard;

    /**
     * @var int
     */
    private $tierBoostTitleWithoutWildcard;

    /**
     * @throws InvalidRangesException
     */
    public function __construct(
        int $tierBoostContentWithWildcard,
        int $tierBoostContentWithoutWildcard,
        int $tierBoostTitleWithWildcard,
        int $tierBoostTitleWithoutWildcard
    ) {
        if ($tierBoostContentWithWildcard > $tierBoostContentWithoutWildcard) {
            throw new InvalidRangesException(
                'Value provided for the query with wildcard is higher than without (content)'
            );
        }

        if ($tierBoostTitleWithWildcard > $tierBoostTitleWithoutWildcard) {
            throw new InvalidRangesException(
                'Value provided for the query with wildcard is higher than without (title)'
            );
        }

        $this->tierBoostContentWithWildcard = $tierBoostContentWithWildcard;
        $this->tierBoostContentWithoutWildcard = $tierBoostContentWithoutWildcard;
        $this->tierBoostTitleWithWildcard = $tierBoostTitleWithWildcard;
        $this->tierBoostTitleWithoutWildcard = $tierBoostTitleWithoutWildcard;
    }

    public function getTierBoostContentWithWildcard(): int
    {
        return $this->tierBoostContentWithWildcard;
    }

    public function getTierBoostContentWithoutWildcard(): int
    {
        return $this->tierBoostContentWithoutWildcard;
    }

    public function getTierBoostTitleWithWildcard(): int
    {
        return $this->tierBoostTitleWithWildcard;
    }

    public function getTierBoostTitleWithoutWildcard(): int
    {
        return $this->tierBoostTitleWithoutWildcard;
    }
}
