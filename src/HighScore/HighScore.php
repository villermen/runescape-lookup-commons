<?php

namespace Villermen\RuneScape\HighScore;

/**
 * @template TSkill of SkillInterface = SkillInterface
 * @template TActivity of ActivityInterface = ActivityInterface
 *
 * @phpstan-type WeakSkill array{id: int, rank: int|null, level: int|null, xp: int|null}
 * @phpstan-type WeakActivity array{id: int, rank: int|null, score: int|null}
 */
abstract class HighScore
{
    /**
     * Compatible with the output of {@see toArray()}. Data for unknown skills is retained.
     *
     * @param array{skills: WeakSkill[], activities: WeakActivity[]} $data
     * @return ($oldSchool is true ? OsrsHighScore : Rs3HighScore)
     */
    public static function fromArray(array $data, bool $oldSchool): OsrsHighScore|Rs3HighScore
    {
        // @phpstan-ignore function.alreadyNarrowedType, nullCoalesce.offset (Input may not match PHPDoc.)
        if (!is_array($data['skills'] ?? null)) {
            throw new \InvalidArgumentException('Invalid high score data: No entry for skills.');
        }
        // @phpstan-ignore function.alreadyNarrowedType, nullCoalesce.offset
        if (!is_array($data['activities'] ?? null)) {
            throw new \InvalidArgumentException('Invalid high score data: No entry for activities.');
        }

        $highScore = $oldSchool ? new OsrsHighScore([], []) : new Rs3HighScore([], []);

        $highScore->skills = array_values(array_map(function (array $skill) {
            // @phpstan-ignore isset.offset
            if (!isset($skill['id'])) {
                throw new \InvalidArgumentException('Invalid high score data: Skill without ID.');
            }

            $xp = self::correctValue($skill['xp'] ?? null);
            $level = self::correctValue($skill['level'] ?? null) ?: null;

            return [
                'id' => (int)$skill['id'],
                'rank' => self::correctValue($skill['rank'] ?? null),
                // Unknown level means unknown XP and vice versa, even when the APIs disagree.
                'level' => $xp === null ? null : $level,
                'xp' => $level === null ? null : $xp,
            ];
        }, $data['skills']));

        $highScore->activities = array_values(array_map(function (array $activity) {
            // @phpstan-ignore isset.offset
            if (!isset($activity['id'])) {
                throw new \InvalidArgumentException('Invalid high score data: Activity without ID.');
            }

            return [
                'id' => (int)$activity['id'],
                'rank' => self::correctValue($activity['rank'] ?? null),
                'score' => self::correctValue($activity['score'] ?? null),
            ];
        }, $data['activities']));

        $highScore->assertUniqueEntries();

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
     * Stored in weak type to retain data for unknown IDs.
     *
     * @var WeakSkill[] $skills
     */
    protected array $skills = [];

    /**
     * Stored in weak type to retain data for unknown IDs.
     *
     * @var WeakActivity[] $activities
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
            $this->skills[] = [
                'id' => $skill->skill->getId(),
                'rank' => $skill->rank,
                'level' => $skill->level,
                'xp' => $skill->xp,
            ];
        }

        foreach ($activities as $activity) {
            $this->activities[] = [
                'id' => $activity->activity->getId(),
                'rank' => $activity->rank,
                'score' => $activity->score,
            ];
        }

        $this->assertUniqueEntries();
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
        // PHP 8.4: array_find()
        $weakSkill = array_filter($this->skills, fn (array $weak): bool => (
            $weak['id'] === $skill->getId()
        ));
        $weakSkill = reset($weakSkill) ?: null;

        return new HighScoreSkill(
            $skill,
            $weakSkill['rank'] ?? null,
            $weakSkill['level'] ?? null,
            $weakSkill['xp'] ?? null,
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
        // PHP 8.4: array_find()
        $weakActivity = array_filter($this->activities, fn (array $weak): bool => (
            $weak['id'] === $activity->getId()
        ));
        $weakActivity = reset($weakActivity) ?: null;

        return new HighScoreActivity(
            $activity,
            $weakActivity['rank'] ?? null,
            $weakActivity['score'] ?? null,
        );
    }

    public function compareTo(HighScore $otherHighScore): HighScoreComparison
    {
        return new HighScoreComparison($this, $otherHighScore);
    }

    /**
     * @return array{skills: list<WeakSkill>, activities: list<WeakActivity>}
     */
    public function toArray(): array
    {
        $skills = [];
        foreach ($this->skills as $skill) {
            if ($skill['rank'] === null && $skill['level'] === null && $skill['xp'] === null) {
                continue;
            }

            $skills[] = [
                'id' => $skill['id'],
                'rank' => $skill['rank'],
                'level' => $skill['level'],
                'xp' => $skill['xp'],
            ];
        }

        $activities = [];
        foreach ($this->activities as $activity) {
            if ($activity['rank'] === null && $activity['score'] === null) {
                continue;
            }

            $activities[] = [
                'id' => $activity['id'],
                'rank' => $activity['rank'],
                'score' => $activity['score'],
            ];
        }

        return [
            'skills' => $skills,
            'activities' => $activities,
        ];
    }

    private function assertUniqueEntries(): void
    {
        $skillIds = array_map(fn (array $weakSkill) => $weakSkill['id'], $this->skills);
        if (count($skillIds) !== count(array_unique($skillIds))) {
            throw new \InvalidArgumentException('Invalid high score data: Multiple entries for same skill.');
        }

        $activityIds = array_map(fn (array $weakActivity) => $weakActivity['id'], $this->activities);
        if (count($activityIds) !== count(array_unique($activityIds))) {
            throw new \InvalidArgumentException('Invalid high score data: Multiple entries for same activity.');
        }
    }
}
