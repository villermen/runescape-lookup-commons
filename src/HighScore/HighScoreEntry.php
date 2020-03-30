<?php

namespace Villermen\RuneScape\HighScore;

/**
 * Represents a skill or activity in a high score.
 */
abstract class HighScoreEntry
{
    /** @var int|null */
    protected $rank;

    protected function __construct(int $rank)
    {
        if ($rank < 1) {
            $rank = null;
        }

        $this->rank = $rank;
    }

    /**
     * Returns the name of the stat for display purposes.
     */
    abstract public function getName(): string;

    public function getRank(): ?int
    {
        return $this->rank;
    }
}
