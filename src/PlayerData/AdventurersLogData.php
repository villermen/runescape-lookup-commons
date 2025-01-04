<?php

namespace Villermen\RuneScape\PlayerData;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;

class AdventurersLogData
{
    public function __construct(
        public readonly string $displayName,
        public readonly ActivityFeed $activityFeed,
    ) {
    }
}
