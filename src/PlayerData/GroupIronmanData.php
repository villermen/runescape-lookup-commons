<?php

namespace Villermen\RuneScape\PlayerData;

use Villermen\RuneScape\Player;

class GroupIronmanData
{
    /**
     * @param Player[] $players
     */
    public function __construct(
        public readonly string $displayName,
        public readonly array $players,
    ) {
    }
}
