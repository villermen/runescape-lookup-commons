# RuneScape lookup commons

#### A PHP library containing RuneScape lookup components.

[![CircleCI](https://circleci.com/gh/villermen/runescape-commons.svg?style=svg)](https://circleci.com/gh/villermen/runescape-commons)

## Features

Contains properly structured classes for looking up:
- Player highscores (including oldschool)
- Player activity feeds

## Usage

For looking up player related matters, simply construct a `Player` object and use its methods:

```php
use Villermen\RuneScape\Player;
use Villermen\RuneScape\Constants;

$player = new Player("Villermen");
$currentOldSchoolHighscore = $player->getHighscore(true);
$uncappedAttackLevel = $currentOldSchoolHighscore->getSkill(Constants::SKILL_ATTACK)->getLevel(true);
```

## Installation

_Will be added when released_
