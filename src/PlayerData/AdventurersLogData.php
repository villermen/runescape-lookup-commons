<?php

namespace Villermen\RuneScape\PlayerData;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\Player;

class AdventurersLogData
{
    public function __construct(
        public readonly Player $player,
        public readonly string $displayName,
        public readonly ActivityFeed $activityFeed,
    ) {
    }
}
