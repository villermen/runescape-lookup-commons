<?php

namespace Villermen\RuneScape;

class Player
{
    protected const RUNEMETRICS_URL = 'https://apps.runescape.com/runemetrics/app/overview/player/%s';
    protected const CHAT_HEAD_URL = 'https://secure.runescape.com/m=avatar-rs/%s/chat.gif';
    protected const FULL_BODY_URL = 'https://secure.runescape.com/m=avatar-rs/%s/full.gif';

    /**
     * Returns whether the given name is a valid RuneScape player name.
     */
    public static function validateName(string $name): bool
    {
        return (bool)preg_match('/^[a-z0-9_ -]{1,12}$/i', $name);
    }

    /**
     * Use the same data fetcher instance for all players to have them share the same cache.
     */
    public function __construct(protected string $name)
    {
        $name = trim($name);

        // Validate that name adheres to RS policies
        if (!self::validateName($name)) {
            throw new \InvalidArgumentException(
                sprintf('Name "%s" does not conform to the RuneScape specifications.', $name)
            );
        }

        $this->name = $name;
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
}
