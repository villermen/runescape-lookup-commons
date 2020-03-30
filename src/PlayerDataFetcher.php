<?php

namespace Villermen\RuneScape;

use Exception;
use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\Exception\DataConversionException;
use Villermen\RuneScape\Exception\FetchFailedException;
use Villermen\RuneScape\HighScore\ActivityHighScore;
use Villermen\RuneScape\HighScore\SkillHighScore;

/**
 * Fetches, converts and caches external API data to usable objects.
 */
class PlayerDataFetcher
{
    const INDEX_LITE_URL = "http://services.runescape.com/m=hiscore/index_lite.ws?player=%s";
    const OLD_SCHOOL_INDEX_LITE_URL = "https://secure.runescape.com/m=hiscore_oldschool/a=13/index_lite.ws?player=%s";
    const ADVENTURERS_LOG_URL = "http://services.runescape.com/m=adventurers-log/a=13/rssfeed?searchName=%s";
    const RUNEMETRICS_URL = "https://apps.runescape.com/runemetrics/profile/profile?user=%s&activities=20";

    /** @var mixed[] */
    private $cache;

    /** @var int */
    protected $timeout;

    protected $dataConverter;

    public function __construct(int $timeOut = 5, PlayerDataConverter $dataConverter = null)
    {
        $this->timeout = $timeOut;
        $this->dataConverter = $dataConverter ?: new PlayerDataConverter();
    }

    /**
     * @throws FetchFailedException
     */
    public function fetchRealName(string $playerName): string
    {
        return $this->fetch($playerName, PlayerDataConverter::KEY_REAL_NAME, [
            self::RUNEMETRICS_URL => "convertRuneMetrics",
            self::ADVENTURERS_LOG_URL => "convertAdventurersLog"
        ]);
    }

    /**
     * Returns an string from cache, or null if it isn't cached.
     */
    public function getCachedRealName(string $playerName): ?string
    {
        return $this->getCached($playerName, PlayerDataConverter::KEY_REAL_NAME);
    }

    /**
     * @throws FetchFailedException
     */
    public function fetchSkillHighScore(string $playerName): SkillHighScore
    {
        return $this->fetch($playerName, PlayerDataConverter::KEY_SKILL_HIGH_SCORE, [
            self::RUNEMETRICS_URL => "convertRuneMetrics",
            self::INDEX_LITE_URL => "convertIndexLite"
        ]);
    }

    public function getCachedSkillHighScore(string $playerName): ?SkillHighScore
    {
        return $this->getCached($playerName, PlayerDataConverter::KEY_SKILL_HIGH_SCORE);
    }

    /**
     * @throws FetchFailedException
     */
    public function fetchOldSchoolSkillHighScore(string $playerName): SkillHighScore
    {
        return $this->fetch($playerName, PlayerDataConverter::KEY_OLD_SCHOOL_SKILL_HIGH_SCORE, [
            self::OLD_SCHOOL_INDEX_LITE_URL => "convertOldSchoolIndexLite"
        ]);
    }

    public function getCachedOldSchoolSkillHighScore(string $playerName): ?SkillHighScore
    {
        return $this->getCached($playerName, PlayerDataConverter::KEY_OLD_SCHOOL_SKILL_HIGH_SCORE);
    }

    /**
     * @throws FetchFailedException
     */
    public function fetchActivityHighScore(string $playerName): ActivityHighScore
    {
        return $this->fetch($playerName, PlayerDataConverter::KEY_ACTIVITY_HIGH_SCORE, [
            self::INDEX_LITE_URL => "convertIndexLite"
        ]);
    }

    public function getCachedActivityHighScore(string $playerName): ?ActivityHighScore
    {
        return $this->getCached($playerName, PlayerDataConverter::KEY_ACTIVITY_HIGH_SCORE);
    }

    /**
     * @throws FetchFailedException
     */
    public function fetchOldSchoolActivityHighScore(string $playerName): ActivityHighScore
    {
        return $this->fetch($playerName, PlayerDataConverter::KEY_OLD_SCHOOL_ACTIVITY_HIGH_SCORE, [
            self::OLD_SCHOOL_INDEX_LITE_URL => "convertOldSchoolIndexLite"
        ]);
    }

    public function getCachedOldSchoolActivityHighScore(string $playerName): ?ActivityHighScore
    {
        return $this->getCached($playerName, PlayerDataConverter::KEY_OLD_SCHOOL_ACTIVITY_HIGH_SCORE);
    }

    /**
     * @throws FetchFailedException
     */
    public function fetchActivityFeed(string $playerName): ActivityFeed
    {
        return $this->fetch($playerName, PlayerDataConverter::KEY_ACTIVITY_FEED, [
            self::RUNEMETRICS_URL => "convertRuneMetrics",
            self::ADVENTURERS_LOG_URL => "convertAdventurersLog"
        ]);
    }

    public function getCachedActivityFeed(string $playerName): ?ActivityFeed
    {
        return $this->getCached($playerName, PlayerDataConverter::KEY_ACTIVITY_FEED);
    }

    /**
     * Set the timeout used for every external request by this instance.
     */
    public function setTimeout(int $seconds): void
    {
        $this->timeout = $seconds;
    }

    /**
     * @return mixed|null
     */
    protected function getCached(string $playerName, string $cacheKey)
    {
        $playerCacheKey = strtolower($playerName);

        if (!isset($this->cache[$cacheKey][$playerCacheKey])) {
            return null;
        }

        return $this->cache[$cacheKey][$playerCacheKey];
    }

    /**
     * Fetches an item from cache, or obtains it freshly using the given functions.
     *
     * @param string[] $fetchStrategies URL to data as key with $dataConverter conversion method as value.
     * @return mixed
     * @throws FetchFailedException
     */
    protected function fetch(string $playerName, string $cacheKey, array $fetchStrategies)
    {
        $playerCacheKey = strtolower($playerName);

        // Return immediately if the cache already contains the requested item
        if (isset($this->cache[$cacheKey][$playerCacheKey])) {
            return $this->cache[$cacheKey][$playerCacheKey];
        }

        // Try available strategies until the value is available
        $firstException = null;
        foreach($fetchStrategies as $url => $conversionMethod) {
            try {
                $url = sprintf($url, urlencode($playerName));

                $data = $this->fetchUrl($url);

                $result = call_user_func([$this->dataConverter, $conversionMethod], $data);

                // Cache resulting object(s) if they haven't been cached yet
                $targetObject = false;
                foreach($result as $resultCacheKey => $resultObject) {
                    if (!isset($this->cache[$resultCacheKey][$playerCacheKey])) {
                        $this->cache[$resultCacheKey][$playerCacheKey] = $resultObject;
                    }

                    if ($resultCacheKey === $cacheKey) {
                        $targetObject = $resultObject;
                    }
                }

                // Return if one of the objects contained our cacheKey
                if ($targetObject) {
                    return $targetObject;
                }
            } catch (Exception $exception) {
                if ($exception instanceof FetchFailedException || $exception instanceof DataConversionException) {
                    if (!$firstException) {
                        $firstException = $exception;
                    }
                } else {
                    throw $exception;
                }
            }
        }

        throw new FetchFailedException(sprintf("None of the fetch strategies (%s) succeeded in retrieving the player's %s.", implode(", ", $fetchStrategies), $cacheKey), 0, $firstException);
    }

    /**
     * Fetches data from the given URL.
     *
     * @throws FetchFailedException
     */
    protected function fetchUrl(string $url): string
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout
        ]);
        $data = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new FetchFailedException(sprintf("A cURL error occurred for \"%s\": %s", $url, $error));
        }

        if ($statusCode !== 200) {
            throw new FetchFailedException(sprintf("URL \"%s\" responded with status code %d.", $url, $statusCode));
        }

        if (!$data) {
            throw new FetchFailedException(sprintf("URL \"%s\" returned no data.", $url));
        }

        return $data;
    }
}
