<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @extends AbstractIterator<int, string>
 */
class StringCharacterIterator extends AbstractIterator
{
    public readonly string $string;

    public function __construct(string|\Stringable $string)
    {
        $this->string = (string)$string;
    }

    public function getIterator(): Traversable
    {
        for ($i = 0; $i < strlen($this->string); $i++) {
            yield $i => $this->string[$i];
        }
    }
}