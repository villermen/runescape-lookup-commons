<?php

namespace Villermen\RuneScape;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\Highscore\Highscore;

class Player
{
    const HIGHSCORE_URL = "http://services.runescape.com/m=hiscore/index_lite.ws?player=%s";
    const HIGHSCORE_URL_OLD_SCHOOL = "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player=%s";
    const ADVENTURERS_LOG_URL = "https://apps.runescape.com/runemetrics/app/overview/player/%s";
    const ACTIVITY_FEED_URL = "http://services.runescape.com/m=adventurers-log/a=13/rssfeed?searchName=%s";
    const CHAT_HEAD_URL = "https://secure.runescape.com/m=avatar-rs/%s/chat.gif";
    const FULL_BODY_URL = "https://secure.runescape.com/m=avatar-rs/%s/full.gif";

    /** @var Highscore */
    private $cachedHighscores;

    /** @var string */
    private $name;

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

    public function __construct(string $name)
    {
        $name = trim($name);

        // Validate that name adheres to RS policies
        if (!self::validateName($name)) {
            throw new RuneScapeException("Name does not conform to the RuneScape specifications.");
        }

        $this->name = $name;
    }

    /**
     * Returns the live highscore of the player from the official highscores (expect a delay).
     * Subsequent calls will not cause a request.
     *
     * @param bool $oldSchool If true, oldschool stats will be queried.
     * @param int $timeout Timeout of the highscore request in seconds.
     * @return Highscore
     * @throws RuneScapeException
     */
    public function getHighscore(bool $oldSchool = false, int $timeout = 5): Highscore
    {
        if ($this->cachedHighscores) {
            return $this->cachedHighscores;
        }

        $requestUrl = sprintf($oldSchool ? self::HIGHSCORE_URL_OLD_SCHOOL : self::HIGHSCORE_URL, urlencode($this->getName()));

        $context = stream_context_create([
            "http" => [
                "timeout" => $timeout,
            ]
        ]);

        $data = @file_get_contents($requestUrl, false, $context);

        if (!$data) {
            throw new RuneScapeException("Could not obtain player stats from RuneScape high scores.");
        }

        return $this->cachedHighscores = new Highscore($this, $data);
    }

    /**
     * Returns the activities currently displayed on the player's activity feed.
     *
     * @param int $timeout
     * @return ActivityFeed
     * @throws RuneScapeException If the player's activity feed is inaccessible or unparsable.
     */
    public function getActivityFeed(int $timeout = 5): ActivityFeed
    {
        $curl = curl_init($this->getActivityFeedUrl());
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout
        ]);
        $feedData = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode !== 200) {
            throw new RuneScapeException("Activity feed page returned with status " . $statusCode);
        }

        return new ActivityFeed($this, $feedData);
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
    public function getAdventurersLogUrl(): string
    {
        return sprintf(self::ADVENTURERS_LOG_URL, $this->getName());
    }

    /**
     * @return string
     */
    public function getActivityFeedUrl(): string
    {
        return sprintf(self::ACTIVITY_FEED_URL, $this->getName());
    }
}
