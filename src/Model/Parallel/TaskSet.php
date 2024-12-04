<?php

declare(strict_types=1);

namespace App\Model\Parallel;

use App\Utility\FileWriterUtility;

class TaskSet
{
    private string $resultsFile;
    private array $tasks;

    public function __construct(Task ...$tasks)
    {
        $this->tasks = $tasks;
    }

    public function getResults(): array
    {
        return unserialize(file_get_contents($this->resultsFile));
    }

    public function setResultsFile(string $resultsFile): static
    {
        if (isset($this->resultsFile)) {
            throw new \BadMethodCallException('ResultsFile can be set only once');
        }

        $this->resultsFile = $resultsFile;
        FileWriterUtility::updateLocked($resultsFile, fn (): string => serialize([]));
        foreach ($this->tasks as $task) {
            $task->setResultsFile($resultsFile);
        }

        return $this;
    }

    /**
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function getTask(int|string $taskIndex): Task
    {
        return $this->tasks[$taskIndex];
    }
}
