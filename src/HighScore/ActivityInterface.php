<?php

namespace Villermen\RuneScape\HighScore;

interface ActivityInterface extends \BackedEnum
{
    public function getName(): string;
}
