# RuneScape lookup commons
A PHP library containing RuneScape lookup components.

## Features
- Fetch and compare RS3 and OSRS high scores.
- Alternatively for RS3, RuneMetrics can be used to obtain high scores and activity feed together. It may contain high
  score data even for non-member players, but lacks activity high scores.
- Fetch player activity feed via RuneMetrics or Adventurer's log.
- Calculate combat levels and virtual skill levels.

## Installation

`composer require villermen/runescape-lookup-commons`

## Usage
Create or obtain an instance of `Player` and use it with the `PlayerDataFetcher` service to obtain high scores and other
information.

```php
use Villermen\RuneScape\HighScore\HighScoreSkill;
use Villermen\RuneScape\HighScore\OsrsSkill;
use Villermen\RuneScape\HighScore\Rs3HighScore;
use Villermen\RuneScape\HighScore\Rs3Skill;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\Service\PlayerDataFetcher;

$fetcher = new PlayerDataFetcher(); // You can optionally provide your own HTTP client.
$player = new Player('playername');

$rs3HighScore = $fetcher->fetchIndexLite($player);
$mining = $rs3HighScore->getSkill(Rs3Skill::MINING);
$mining->level; // 110
$mining->getVirtualLevel(); // 123
$mining->getXpToNextLevel(); // 1231231
$rs3HighScore->getSkill(Rs3Skill::NECROMANCY)->xp // null
$rs3HighScore->getSkill(Rs3Skill::TOTAL)->rank; // 1200000
$rs3HighScore->getCombatLevel(includeSummoning: true); // 138

$osrsHighScore = $fetcher->fetchIndexLite($player, oldSchool: true);
$osrsHighScore->getSkill(OsrsSkill::HITPOINTS)->getLevelOrMinimum(); // 10

$highScore1 = new Rs3HighScore(skills: [new HighScoreSkill(
    skill: Rs3Skill::ATTACK,
    rank: null,
    level: 24,
    xp: 7420,
)], activities: []);
$highScore2 = new Rs3HighScore(skills: [new HighScoreSkill(
    skill: Rs3Skill::ATTACK,
    rank: 2000000,
    level: 25,
    xp: 7840,
)], activities: []);
$comparison = $highScore1->compareTo($highScore2);
$comparison->getLevelDifference(Rs3Skill::ATTACK); // -1
$comparison->getRankDifference(Rs3Skill::ATTACK); // null
$comparison->getXpDifference(Rs3Skill::ATTACK); // 420

$runeMetrics = $fetcher->fetchRuneMetrics($player);
$runeMetrics->displayName; // "PlayerName"
$runeMetrics->highScore; // Rs3HighScore
$runeMetrics->activityFeed->items[0]->title; // "Quest complete: Evil Dave's big day out"

$adventurersLog = $fetcher->fetchAdventurersLog($player);
$adventurersLog->displayName; // "PlayerName"
$adventurersLog->activityFeed->items[0]->description; // "I somehow switched bodies with Evil Dave and survived enough chores and shouting from Doris to find a way to swap back!"
```
