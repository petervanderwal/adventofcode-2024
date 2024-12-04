<?php

declare(strict_types=1);

namespace App\Model\Parallel;

use App\Utility\FileWriterUtility;

class Task
{
    /**
     * @var mixed[]
     */
    private array $arguments;
    private string $resultsFile;
    private ?string $resultKey = null;

    public function __construct(
        private readonly string $methodName,
        mixed ...$arguments
    ) {
        $this->arguments = $arguments;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setResultKey(?string $resultKey): static
    {
        $this->resultKey = $resultKey;
        return $this;
    }

    public function getResultKey(): ?string
    {
        return $this->resultKey;
    }

    public function setResultsFile(string $resultsFile): static
    {
        if (isset($this->resultsFile)) {
            throw new \BadMethodCallException('ResultsFile can be set only once');
        }
        $this->resultsFile = $resultsFile;
        return $this;
    }

    public function storeResult(mixed $result): static
    {
        FileWriterUtility::updateLocked(
            $this->resultsFile,
            fn (string $current) => serialize([
                ...unserialize($current),
                ...($this->resultKey === null ? [$result] : [$this->resultKey => $result])
            ])
        );
        return $this;
    }
}
