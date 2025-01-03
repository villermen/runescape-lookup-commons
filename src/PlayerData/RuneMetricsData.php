<?php

namespace Villermen\RuneScape\PlayerData;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\HighScore\Rs3HighScore;
use Villermen\RuneScape\Player;

class RuneMetricsData
{
    public function __construct(
        public readonly Player $player,
        public readonly string $realName,
        public readonly Rs3HighScore $highScore,
        public readonly ActivityFeed $activityFeed,
    ) {
    }
}
