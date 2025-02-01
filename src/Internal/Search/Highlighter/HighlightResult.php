<?php

declare(strict_types=1);

namespace dobron\HighlightLite\Internal\Search\Highlighter;

class HighlightResult
{
    private string $highlightedText;
    /**
     * @var array<int, array{start: int, length: int}>
     */
    private array $matches;

    /**
     * @param array<int, array{start: int, length: int}> $matches
     */
    public function __construct(
        string $highlightedText,
        array $matches
    ) {
        $this->matches = $matches;
        $this->highlightedText = $highlightedText;
    }

    public function getHighlightedText(): string
    {
        return $this->highlightedText;
    }

    /**
     * @return array<int, array{start: int, length: int}>
     */
    public function getMatches(): array
    {
        return $this->matches;
    }
}
