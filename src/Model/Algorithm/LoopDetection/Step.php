<?php

declare(strict_types=1);

namespace App\Model\Algorithm\LoopDetection;

use App\Model\Algorithm\LoopDetection;

class Step
{
    private mixed $state = null;
    private ?string $stateStringRepresentation = null;

    /**
     * @var array<string, int>
     */
    private array $scores = [];

    public function __construct(
        private readonly int $step,
    ) {}

    public function getStep(): int
    {
        return $this->step;
    }

    public function getState(): mixed
    {
        return $this->state;
    }

    public function setState(mixed $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function getStateStringRepresentation(): string
    {
        return $this->stateStringRepresentation ?? json_encode($this->state, JSON_THROW_ON_ERROR);
    }

    public function setStateStringRepresentation(?string $stateStringRepresentation): static
    {
        $this->stateStringRepresentation = $stateStringRepresentation;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->getScoreForField(LoopDetection::DEFAULT_FIELD);
    }

    public function setScore(int $score): static
    {
        return $this->setScoreForField(LoopDetection::DEFAULT_FIELD, $score);
    }

    public function getScoreForField(string $field): ?int
    {
        return $this->scores[$field] ?? null;
    }

    public function setScoreForField(string $field, int $score): static
    {
        $this->scores[$field] = $score;
        return $this;
    }
}
