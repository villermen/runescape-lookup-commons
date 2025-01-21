<?php

namespace Villermen\RuneScape\HighScore;

interface ActivityInterface extends \BackedEnum
{
    public function getId(): int;

    public function getName(): string;
}
