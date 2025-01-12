<?php

namespace Villermen\RuneScape\HighScore;

/**
 * @template TSkill of SkillInterface = SkillInterface
 * @template TActivity of ActivityInterface = ActivityInterface
 */
abstract class HighScore
{
    /**
     * Compatible with both OSRS's JSON format and the output of {@see toArray()}. Retains data for unknown skills and
     * activities.
     *
     * @param mixed[] $data
     * @return ($oldSchool is true ? OsrsHighScore : Rs3HighScore)
     */
    public static function fromArray(array $data, bool $oldSchool = false): OsrsHighScore|Rs3HighScore
    {
        if (!is_array($data['skills'] ?? null) || !is_array($data['activities'] ?? null)) {
            throw new \InvalidArgumentException('Invalid high score array data provided.');
        }

        $highScore = $oldSchool ? new OsrsHighScore([], []) : new Rs3HighScore([], []);

        $highScore->skills = array_values(array_map(function (array $skill) {
            $xp = self::correctValue($skill['xp'] ?? null);
            $level = self::correctValue($skill['level'] ?? null) ?: null;

            return [
                'rank' => self::correctValue($skill['rank'] ?? null),
                // Unknown level means unknown XP and vice versa, even when the APIs disagree.
                'level' => $xp === null ? null : $level,
                'xp' => $level === null ? null : $xp,
            ];
        }, $data['skills']));

        $highScore->activities = array_values(array_map(fn (array $activity) => [
            'rank' => self::correctValue($activity['rank'] ?? null),
            'score' => self::correctValue($activity['score'] ?? null),
        ], $data['activities']));

        return $highScore;
    }

    /**
     * Corrects raw high score value to a nullable positive integer.
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
     * Stored as weakly-typed array to retain data for unknown IDs.
     *
     * @var array<int, array{rank: int|null, level: int|null, xp: int|null}> $skills
     */
    protected array $skills = [];

    /**
     * Stored as weakly-typed array to retain data for unknown IDs.
     *
     * @var array<int, array{rank: int|null, score: int|null}> $activities
     */
    protected array $activities = [];

    /**
     * @param array<HighScoreSkill<TSkill>> $skills
     * @param array<HighScoreActivity<TActivity>> $activities
     */
    public function __construct(
        array $skills,
        array $activities,
    ) {
        foreach ($skills as $skill) {
            $this->skills[$skill->skill->getId()] = [
                'rank' => $skill->rank,
                'level' => $skill->level,
                'xp' => $skill->xp,
            ];
        }

        foreach ($activities as $activity) {
            $this->activities[$activity->activity->getId()] = [
                'rank' => $activity->rank,
                'score' => $activity->score,
            ];
        }
    }

    public abstract function getCombatLevel(): float;

    /**
     * @return array<HighScoreSkill<TSkill>>
     */
    public abstract function getSkills(): array;


    /**
     * @return array<HighScoreActivity<TActivity>>
     */
    public abstract function getActivities(): array;

    /**
     * Note that you can pass a skill for the wrong version of the game. This will result in the stats for the skill
     * with the same ID in the correct version of the game.
     *
     * @param TSkill $skill
     * @return HighScoreSkill<TSkill>
     */
    public function getSkill(SkillInterface $skill): HighScoreSkill
    {
        $skillData = $this->skills[$skill->getId()] ?? null;

        return new HighScoreSkill(
            $skill,
            $skillData['rank'] ?? null,
            $skillData['level'] ?? null,
            $skillData['xp'] ?? null,
        );
    }

    /**
     * Note that you can pass an activity for the wrong version of the game. This will result in the stats for the
     * activity with the same ID in the correct version of the game.
     *
     * @param TActivity $activity
     * @return HighScoreActivity<TActivity>
     */
    public function getActivity(ActivityInterface $activity): HighScoreActivity
    {
        $activityData = $this->activities[$activity->getId()] ?? null;

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
