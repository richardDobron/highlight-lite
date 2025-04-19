<?php

declare(strict_types=1);

namespace dobron\HighlightLite;

use dobron\HighlightLite\Internal\DiacriticsUtil;
use dobron\HighlightLite\Internal\Search\Highlighter\Highlight;

final class HighlightFactory implements HighlightFactoryInterface
{
    public function create(Configuration $configuration): Highlight
    {
        return new Highlight(
            $configuration,
            new DiacriticsUtil()
        );
    }
}
