<?php

declare(strict_types=1);

namespace dobron\HighlightLite\Internal\Search\Highlighter;

use dobron\HighlightLite\Configuration;
use dobron\HighlightLite\Internal\DiacriticsUtil;

final class Highlight
{
    private Configuration $configuration;
    private DiacriticsUtil $diacriticsUtil;

    public function __construct(
        Configuration $configuration,
        DiacriticsUtil $diacriticsUtil
    ) {
        $this->diacriticsUtil = $diacriticsUtil;
        $this->configuration = $configuration;
    }

    public function highlight(
        string $text,
        string $query,
        string $startTag = '<em>',
        string $endTag = '</em>'
    ): HighlightResult {
        if ($text === '') {
            return new HighlightResult($text, []);
        }

        $normalizedText = $this->diacriticsUtil->normalizeDiacritics($text);
        $normalizedTextArray = mb_str_split($normalizedText);

        $queryTokens = $this->tokenizeQuery($query);

        $matches = [];

        foreach ($queryTokens as $queryToken) {
            $occurrence = null;
            $prefix = '';
            if (! $this->configuration->getInsideWords()
                && preg_match('/[a-z0-9_]/i', mb_substr($queryToken, 0, 1))) {
                $prefix = '\\b';
            }

            $pattern = '/' . $prefix . $this->diacriticsUtil->generateMatchingPattern($queryToken) . '/iu';

            if (! preg_match($pattern, $normalizedText, $occurrence, PREG_OFFSET_CAPTURE)) {
                if ($this->configuration->getMatchShortcuts()) {
                    $firstLetter = mb_substr($queryToken, 0, 1);
                    $shortcutPattern = '/\\b' . preg_quote($firstLetter, '/') . '\\w*\\./iu';

                    if (preg_match($shortcutPattern, $normalizedText, $occurrence, PREG_OFFSET_CAPTURE)) {
                        $pattern = $shortcutPattern;
                    } else {
                        if ($this->configuration->getRequireMatchAll()) {
                            return new HighlightResult($text, []);
                        }

                        continue;
                    }
                } else {
                    if ($this->configuration->getRequireMatchAll()) {
                        return new HighlightResult($text, []);
                    }

                    continue;
                }
            }

            while (preg_match($pattern, $normalizedText, $occurrence, PREG_OFFSET_CAPTURE)) {
                [$wordMatch, $index] = $occurrence[0];
                $index = mb_strlen(substr($normalizedText, 0, $index));
                $wordLen = mb_strlen($wordMatch, 'UTF-8');

                $cleanedSlice = implode('', array_slice($normalizedTextArray, $index, $wordLen));
                $cleanedLength = mb_strlen($cleanedSlice, 'UTF-8');
                $offset = $wordLen - $cleanedLength;

                $start = $index;
                $end = $start + $wordLen + $offset;

                if ($start !== $end) {
                    $matches[] = ['start' => $start, 'length' => $wordLen];
                }

                // Replace the found match with spaces to avoid duplicate matches.
                $normalizedText = mb_substr($normalizedText, 0, $index)
                    . str_repeat(' ', $wordLen)
                    . mb_substr($normalizedText, $index + $wordLen);

                if (! $this->configuration->getFindAllOccurrences()) {
                    break;
                }
            }
        }

        if ($matches === []) {
            return new HighlightResult($text, []);
        }

        // Sort matches by start
        uasort($matches, function (array $a, array $b) {
            return $a['start'] <=> $b['start'];
        });

        $pos = 0;
        $highlightedText = '';
        $spans = $this->extractSpansFromMatches($matches);

        foreach (mb_str_split($text, 1, 'UTF-8') as $pos => $char) {
            if (in_array($pos, $spans['starts'], true)) {
                $highlightedText .= $startTag;
            }
            if (in_array($pos, $spans['ends'], true)) {
                $highlightedText .= $endTag;
            }

            $highlightedText .= $char;
        }

        // Match at the end of the $text
        if (in_array($pos + 1, $spans['ends'], true)) {
            $highlightedText .= $endTag;
        }

        return new HighlightResult($highlightedText, $matches);
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @param array<array{start:int, length:int}> $matches
     * @return array{starts: array<int>, ends: array<int>}
     */
    private function extractSpansFromMatches(array $matches): array
    {
        $spans = [
            'starts' => [],
            'ends' => [],
        ];
        $lastEnd = null;

        foreach ($matches as $match) {
            $end = $match['start'] + $match['length'];

            // Merge matches that are exactly after one another
            if ($lastEnd === $match['start'] || $lastEnd === $match['start'] - 1) {
                $highestEnd = max($spans['ends']);
                unset($spans['ends'][array_search($highestEnd, $spans['ends'], true)]);
            } else {
                $spans['starts'][] = $match['start'];
            }

            $spans['ends'][] = $end;
            $lastEnd = $end;
        }

        return $spans;
    }

    /**
     * @return array<int, string>
     */
    private function tokenizeQuery(string $query): array
    {
        $query = $this->diacriticsUtil->normalizeDiacritics($query);
        /**
         * @var array<int, string> $queryTokens
         */
        $queryTokens = (array)preg_split('/\s+/u', $query);

        return array_filter($queryTokens, fn ($term) => mb_strlen($term) > 0);
    }
}
