<?php

namespace Villermen\RuneScape;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\Exception\RuneScapeException;
use Villermen\RuneScape\HighScore\ActivityHighScore;
use Villermen\RuneScape\HighScore\SkillHighScore;
use Villermen\RuneScape\Exception\FetchFailedException;

class Player
{
    const RUNEMETRICS_URL = "https://apps.runescape.com/runemetrics/app/overview/player/%s";
    const CHAT_HEAD_URL = "https://secure.runescape.com/m=avatar-rs/%s/chat.gif";
    const FULL_BODY_URL = "https://secure.runescape.com/m=avatar-rs/%s/full.gif";

    /** @var PlayerDataFetcher */
    protected $dataFetcher;

    /** @var string */
    protected $name;

    /**
     * Returns whether the given name is a valid RuneScape player name.
     */
    public static function validateName(string $name): bool
    {
        return preg_match("/^[a-z0-9 -_]{1,12}$/i", $name);
    }

    /**
     * Use the same data fetcher instance for all players to have them share the same cache.
     */
    public function __construct(string $name, PlayerDataFetcher $dataFetcher = null)
    {
        $this->dataFetcher = $dataFetcher ?: new PlayerDataFetcher();

        $name = trim($name);

        // Validate that name adheres to RS policies
        if (!self::validateName($name)) {
            throw new \InvalidArgumentException("Name does not conform to the RuneScape specifications.");
        }

        $this->name = $name;
    }

    /**
     * @throws FetchFailedException
     */
    public function fixName(): void
    {
        $this->name = $this->getDataFetcher()->fetchRealName($this->getName());
    }

    /**
     * Fixes the player's name only if it can be done without performing an additional request.
     */
    public function fixNameIfCached(): void
    {
        if ($this->getDataFetcher()->getCachedRealName($this->getName())) {
            $this->fixName();
        }
    }

    /**
     * @throws FetchFailedException
     */
    public function getSkillHighScore(): SkillHighScore
    {
        return $this->getDataFetcher()->fetchSkillHighScore($this->getName());
    }

    /**
     * @throws FetchFailedException
     */
    public function getOldSchoolSkillHighScore(): SkillHighScore
    {
        return $this->getDataFetcher()->fetchOldSchoolSkillHighScore($this->getName());
    }

    /**
     * @throws FetchFailedException
     */
    public function getActivityHighScore(): ActivityHighScore
    {
        return $this->getDataFetcher()->fetchActivityHighScore($this->getName());
    }

    /**
     * @throws FetchFailedException
     */
    public function getOldSchoolActivityHighScore(): ActivityHighScore
    {
        return $this->getDataFetcher()->fetchOldSchoolActivityHighScore($this->getName());
    }

    /**
     * Returns the activities currently displayed on the player's activity feed.
     *
     * @throws FetchFailedException
     */
    public function getActivityFeed(): ActivityFeed
    {
        return $this->getDataFetcher()->fetchActivityFeed($this->getName());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getChatHeadUrl(): string
    {
        return sprintf(self::CHAT_HEAD_URL, $this->getName());
    }

    public function getFullBodyUrl(): string
    {
        return sprintf(self::FULL_BODY_URL, $this->getName());
    }

    public function getRuneMetricsUrl(): string
    {
        return sprintf(self::RUNEMETRICS_URL, $this->getName());
    }

    public function getDataFetcher(): PlayerDataFetcher
    {
        return $this->dataFetcher;
    }

    public function setDataFetcher(PlayerDataFetcher $dataFetcher): void
    {
        $this->dataFetcher = $dataFetcher;
    }
}
