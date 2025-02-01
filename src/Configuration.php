<?php

declare(strict_types=1);

namespace dobron\HighlightLite;

final class Configuration
{
    /** @var bool */
    private $insideWords = false;
    /** @var bool */
    private $findAllOccurrences = false;
    /** @var bool */
    private $requireMatchAll = false;

    public static function create(): self
    {
        return new self();
    }

    public function getInsideWords(): bool
    {
        return $this->insideWords;
    }

    public function setInsideWords(bool $insideWords): self
    {
        $this->insideWords = $insideWords;

        return $this;
    }

    public function getFindAllOccurrences(): bool
    {
        return $this->findAllOccurrences;
    }

    public function setFindAllOccurrences(bool $findAllOccurrences): self
    {
        $this->findAllOccurrences = $findAllOccurrences;

        return $this;
    }

    public function getRequireMatchAll(): bool
    {
        return $this->requireMatchAll;
    }

    public function setRequireMatchAll(bool $requireMatchAll): self
    {
        $this->requireMatchAll = $requireMatchAll;

        return $this;
    }
}
