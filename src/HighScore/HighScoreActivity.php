<?php

namespace Villermen\RuneScape\HighScore;

/**
 * @template-covariant TActivity of ActivityInterface = ActivityInterface
 */
class HighScoreActivity
{
    /**
     * @param TActivity $activity
     */
    public function __construct(
        public readonly ActivityInterface $activity,
        public readonly ?int $rank,
        public readonly ?int $score
    ) {
    }

    public function getName(): string
    {
        return $this->activity->getName();
    }
}
