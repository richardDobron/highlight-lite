<?php

declare(strict_types=1);

namespace dobron\HighlightLite;

use dobron\HighlightLite\Internal\Search\Highlighter\Highlight;

interface HighlightFactoryInterface
{
    public function create(Configuration $configuration): Highlight;
}
