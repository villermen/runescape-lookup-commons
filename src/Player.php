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
     *
     * @param string $name
     * @return bool
     */
    public static function validateName(string $name): bool
    {
        return preg_match("/^[a-z0-9 -_]{1,12}$/i", $name);
    }

    /**
     * @param string $name
     * @param PlayerDataFetcher|null $dataFetcher Use the same data fetcher instance for all players to have them share the same cache.
     * @throws RuneScapeException
     */
    public function __construct(string $name, PlayerDataFetcher $dataFetcher = null)
    {
        $this->dataFetcher = $dataFetcher ?: new PlayerDataFetcher();

        $name = trim($name);

        // Validate that name adheres to RS policies
        if (!self::validateName($name)) {
            throw new RuneScapeException("Name does not conform to the RuneScape specifications.");
        }

        $this->name = $name;
    }

    /**
     * @throws FetchFailedException
     *
     * @return Player
     */
    public function fixName(): Player
    {
        $this->name = $this->getDataFetcher()->fetchRealName($this->getName());

        return $this;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @return Player
     */
    public function fixNameIfCached(): Player
    {
        if ($this->getDataFetcher()->getCachedRealName($this->getName())) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->fixName();
        }

        return $this;
    }

    /**
     * @return SkillHighScore
     * @throws FetchFailedException
     */
    public function getSkillHighScore(): SkillHighScore
    {
        return $this->getDataFetcher()->fetchSkillHighScore($this->getName());
    }

    /**
     * @return SkillHighScore
     * @throws FetchFailedException
     */
    public function getOldSchoolSkillHighScore(): SkillHighScore
    {
        return $this->getDataFetcher()->fetchOldSchoolSkillHighScore($this->getName());
    }

    /**
     * @return ActivityHighScore
     * @throws FetchFailedException
     */
    public function getActivityHighScore(): ActivityHighScore
    {
        return $this->getDataFetcher()->fetchActivityHighScore($this->getName());
    }

    /**
     * @return ActivityHighScore
     * @throws FetchFailedException
     */
    public function getOldSchoolActivityHighScore(): ActivityHighScore
    {
        return $this->getDataFetcher()->fetchOldSchoolActivityHighScore($this->getName());
    }

    /**
     * Returns the activities currently displayed on the player's activity feed.
     *
     * @return ActivityFeed
     * @throws FetchFailedException
     */
    public function getActivityFeed(): ActivityFeed
    {
        return $this->getDataFetcher()->fetchActivityFeed($this->getName());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getChatHeadUrl(): string
    {
        return sprintf(self::CHAT_HEAD_URL, $this->getName());
    }

    /**
     * @return string
     */
    public function getFullBodyUrl(): string
    {
        return sprintf(self::FULL_BODY_URL, $this->getName());
    }

    /**
     * @return string
     */
    public function getRuneMetricsUrl(): string
    {
        return sprintf(self::RUNEMETRICS_URL, $this->getName());
    }

    /**
     * @return PlayerDataFetcher
     */
    public function getDataFetcher(): PlayerDataFetcher
    {
        return $this->dataFetcher;
    }

    /**
     * @param PlayerDataFetcher $dataFetcher
     *
     * @return Player
     */
    public function setDataFetcher(PlayerDataFetcher $dataFetcher): Player
    {
        $this->dataFetcher = $dataFetcher;

        return $this;
    }
}
