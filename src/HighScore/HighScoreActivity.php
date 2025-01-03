<?php

namespace Villermen\RuneScape\HighScore;

/**
 * @template-covariant TActivity of ActivityInterface
 */
class HighScoreActivity
{
    /**
     * @param TActivity $activity
     */
    public function __construct(
        protected readonly ActivityInterface $activity,
        protected readonly ?int $rank,
        protected readonly ?int $score
    ) {
    }

    /**
     * @return TActivity
     */
    public function getActivity(): ActivityInterface
    {
        return $this->activity;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function getName(): string
    {
        return $this->getActivity()->getName();
    }
}
