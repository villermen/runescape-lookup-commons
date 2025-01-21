<?php

namespace Villermen\RuneScape\PlayerData;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\HighScore\Rs3HighScore;

class RuneMetricsData
{
    public function __construct(
        public readonly string $displayName,
        public readonly Rs3HighScore $highScore,
        public readonly ActivityFeed $activityFeed,
    ) {
    }
}
