<?php

namespace Villermen\RuneScape;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\Exception\RuneScapeException;
use Villermen\RuneScape\HighScore\HighScore;
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
     * @param PlayerDataFetcher|null $dataFetcher
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
     */
    public function fixName()
    {
        $this->name = $this->getDataFetcher()->fetchRealName($this);
    }

    /**
     * @return HighScore
     * @throws FetchFailedException
     */
    public function getHighScore(): HighScore
    {
        return $this->getDataFetcher()->fetchHighScore($this);
    }

    /**
     * @return HighScore
     * @throws FetchFailedException
     */
    public function getOldSchoolHighScore(): HighScore
    {
        return $this->getDataFetcher()->fetchOldSchoolHighScore($this);
    }

    /**
     * Returns the activities currently displayed on the player's activity feed.
     *
     * @return ActivityFeed
     * @throws FetchFailedException
     */
    public function getActivityFeed(): ActivityFeed
    {
        return $this->getDataFetcher()->fetchActivityFeed($this);
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
}
