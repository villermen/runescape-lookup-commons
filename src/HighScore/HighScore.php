<?php

namespace Villermen\RuneScape\HighScore;

/**
 * @template TSkill of SkillInterface = SkillInterface
 * @template TActivity of ActivityInterface = ActivityInterface
 */
abstract class HighScore
{
    /**
     * Compatible with both OSRS's JSON format and the output of {@see toArray()}.
     *
     * @param mixed[] $data
     * @return ($oldSchool is true ? OsrsHighScore : Rs3HighScore)
     */
    public static function fromArray(array $data, bool $oldSchool): OsrsHighScore|Rs3HighScore
    {
        if (!is_array($data['skills'] ?? null) || !is_array($data['activities'] ?? null)) {
            throw new \InvalidArgumentException('Invalid highscore array data provided.');
        }

        // Filter/correct keys and values.
        $skills = array_values(array_map(fn (array $skill) => [
            'rank' => self::correctValue($skill['rank'] ?? null),
            'level' => self::correctValue($skill['level'] ?? null),
            'xp' => self::correctValue($skill['xp'] ?? null),
        ], $data['skills']));

        $activities = array_values(array_map(fn (array $activity) => [
            'rank' => self::correctValue($activity['rank'] ?? null),
            'score' => self::correctValue($activity['score'] ?? null),
        ], $data['activities']));

        return $oldSchool ? new OsrsHighScore($skills, $activities) : new Rs3HighScore($skills, $activities);
    }

    /**
     * Corrects raw highscore value to a nullable positive integer.
     */
    public static function correctValue(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $value = (int)$value;
        if ($value < 0) {
            return null;
        }

        return $value;
    }

    /**
     * Skills/activities are stored as weakly-typed arrays indexed by ID to retain data for unknown IDs.
     *
     * @param array<int, array{rank: int|null, level: int|null, xp: int|null}> $skills
     * @param array<int, array{rank: int|null, score: int|null}> $activities
     */
    public function __construct(
        protected readonly array $skills,
        protected readonly array $activities,
    ) {
        if (!count($this->skills) && !count($this->activities)) {
            throw new \InvalidArgumentException('No high score information provided.');
        }
    }

    public abstract function isOldSchool(): bool;

    public abstract function getCombatLevel(): float;

    /**
     * @return array<HighScoreSkill<TSkill>>
     */
    public function getSkills(): array
    {
        return array_map(
            $this->getSkill(...),
            $this->isOldSchool() ? OsrsSkill::cases() : Rs3Skill::cases()
        );
    }

    /**
     * Note that you can pass a skill for the wrong version of the game. This will result in the stats for the skill
     * with the same ID in the correct version of the game.
     *
     * @param TSkill $skill
     * @return HighScoreSkill<TSkill>
     */
    public function getSkill(SkillInterface $skill): HighScoreSkill
    {
        $skillData = $this->skills[$skill->value] ?? null;

        return new HighScoreSkill(
            $skill,
            $skillData['rank'] ?? null,
            $skillData['level'] ?? null,
            $skillData['xp'] ?? null,
        );
    }

    /**
     * @return array<HighScoreActivity<TActivity>>
     */
    public function getActivities(): array
    {
        return array_map(
            $this->getActivity(...),
            $this->isOldSchool() ? OsrsActivity::cases() : Rs3Activity::cases()
        );
    }

    /**
     * Note that you can pass an activity for the wrong version of the game. This will result in the stats for the
     * activity with the same ID in the correct version of the game.
     *
     * @template T of ActivityInterface
     * @param T $activity
     * @return HighScoreActivity<T>
     */
    public function getActivity(ActivityInterface $activity): HighScoreActivity
    {
        $activityData = $this->activities[$activity->value] ?? null;

        return new HighScoreActivity(
            $activity,
            $activityData['rank'] ?? null,
            $activityData['score'] ?? null,
        );
    }

    public function compareTo(HighScore $otherHighScore): HighScoreComparison
    {
        return new HighScoreComparison($this, $otherHighScore);
    }

    /**
     * @return array{
     *     skills: list<array{rank: int|null, level: int|null, xp: int|null}>,
     *     activities: list<array{rank: int|null, score: int|null}>
     * }
     */
    public function toArray(): array
    {
        // Force arrays to be correctly-ordered lists.
        $maxSkillId = count($this->skills) ? max(array_keys($this->skills)) : 0;
        $skills = [];
        for ($i = 0; $i < $maxSkillId; $i++) {
            $skills[] = $this->skills[$i] ?? [
                'rank' => null,
                'level' => null,
                'xp' => null,
            ];
        }

        $maxActivityId = count($this->activities) ? max(array_keys($this->activities)) : 0;
        $activities = [];
        for ($i = 0; $i < $maxActivityId; $i++) {
            $activities[] = $this->activities[$i] ?? [
                'rank' => null,
                'score' => null,
            ];
        }

        return [
            'skills' => $skills,
            'activities' => $activities,
        ];
    }
}
