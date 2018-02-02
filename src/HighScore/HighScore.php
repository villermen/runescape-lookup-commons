<?php

namespace Villermen\RuneScape\HighScore;

abstract class HighScore
{
    /** @var bool */
    protected $oldSchool;

    protected function __construct(bool $oldSchool)
    {
        $this->oldSchool = $oldSchool;
    }

    /**
     * @return bool
     */
    public function isOldSchool(): bool
    {
        return $this->oldSchool;
    }
}
