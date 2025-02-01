<?php

declare(strict_types=1);

namespace dobron\HighlightLite\Tests\Functional;

use dobron\HighlightLite\Configuration;
use dobron\HighlightLite\Internal\Search\Highlighter\Highlight;

use dobron\HighlightLite\HighlightFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HighlightTest extends TestCase
{
    protected function createHighlight(Configuration $configuration): Highlight
    {
        $factory = new HighlightFactory();

        return $factory->create($configuration);
    }

    public function testConfiguration(): void
    {
        $configuration = Configuration::create()
            ->setInsideWords(true)
            ->setFindAllOccurrences(true)
            ->setRequireMatchAll(true);

        $this->assertSame(true, $configuration->getInsideWords());
        $this->assertSame(true, $configuration->getFindAllOccurrences());
        $this->assertSame(true, $configuration->getRequireMatchAll());
    }

    public static function highlightingProvider(): \Generator
    {
        yield [
            'Résumé writing tips for professionals',
            'resume',
            '<em>Résumé</em> writing tips for professionals',
        ];

        yield [
            'Café culture in Paris explained',
            'cáfe',
            '<em>Café</em> culture in Paris explained',
        ];

        yield [
            'Top 10 Piñata party ideas',
            'pinata idea',
            'Top 10 <em>Piñata</em> party <em>idea</em>s',
        ];

        yield [
            'Über cool tech gadgets in 2025',
            'uber',
            '<em>Über</em> cool tech gadgets in 2025',
        ];

        yield [
            'Niño climate pattern explained',
            'nino',
            '<em>Niño</em> climate pattern explained',
        ];

        yield [
            'What is naïve Bayes algorithm?',
            'NAIVE BAY',
            'What is <em>naïve Bay</em>es algorithm?',
        ];

        yield [
            'Exploring São Paulo nightlife',
            'sao',
            'Exploring <em>São</em> Paulo nightlife',
        ];

        yield [
            '2025 résumé templates for creative jobs',
            'résumé 2025 creative jobs',
            '<em>2025 résumé</em> templates for <em>creative jobs</em>',
        ];

        yield [
            'françois hollande political legacy',
            'Francois',
            '<mark>françois</mark> hollande political legacy',
            null,
            '<mark>',
            '</mark>',
        ];

        yield [
            'garçon vs garçonne in French grammar',
            'garcon',
            '<div class="highlight">garçon</div> vs garçonne in French grammar',
            null,
            '<div class="highlight">',
            '</div>',
        ];

        yield [
            'Berliner Straße art gallery guide',
            'strasse',
            'Berliner <mark>Straße</mark> art gallery guide',
            null,
            '<mark>',
            '</mark>',
        ];

        yield [
            'Hotels near Friedrichstraße station',
            'friedrichstrasse',
            'Hotels near <mark>Friedrichstraße</mark> station',
            null,
            '<mark>',
            '</mark>',
        ];

        yield [
            'Cómo preparar una paella valenciana tradicional',
            'como',
            '<em>Cómo</em> preparar una paella valenciana tradicional'
        ];

        yield [
            'Understanding C++ and Python interoperability',
            'c++',
            'Understanding <em>C++</em> and Python interoperability',
        ];

        yield [
            'Searching for open-source software solutions',
            '',
            'Searching for open-source software solutions',
        ];

        yield [
            'Understanding supernatural forces',
            'natural forces',
            'Understanding supernatural <em>forces</em>',
        ];

        yield [
            'What is the best handbook for writing a book',
            'writing a book',
            'What is the best handbook for <em>writing a book</em>',
        ];

        yield [
            'Julius Caesar assassination plot',
            'asassination',
            'Julius Caesar assassination plot',
        ];

        yield [
            'Crème brûlée recipe with vanilla crème',
            'creme',
            '<em>Crème</em> brûlée recipe with vanilla <em>crème</em>',
            [false, true, true]
        ];

        yield [
            'Exploring Königstraße and Friedrichstraße',
            'strasse',
            'Exploring König<em>straße</em> and Friedrich<em>straße</em>',
            [true, true, true],
        ];

        yield [
            'Dvořák and Smetana: Czech classical legends',
            'dvorak czech',
            '<em>Dvořák</em> and Smetana: <em>Czech</em> classical legends',
            [false, true, true],
        ];

        yield [
            'Gdańsk and Kraków: Poland’s historic gems',
            'gdansk poland',
            '<em>Gdańsk</em> and Kraków: <em>Poland</em>’s historic gems',
            [false, true, true],
        ];

        yield [
            'Fjällräven jackets vs Patagonia for hiking',
            'fjallraven hiking',
            '<em>Fjällräven</em> jackets vs Patagonia for <em>hiking</em>',
            [false, true, true],
        ];

        yield [
            'naïve approach to machine learning explained',
            'naive learning',
            '<em>naïve</em> approach to machine <em>learning</em> explained',
            [false, true, true],
        ];

        yield [
            'München Oktoberfest beer tents guide',
            'munchen beer',
            '<em>München</em> Oktoberfest <em>beer</em> tents guide',
            [false, true, true],
        ];

        yield [
            'Visiting São Paulo: top food spots',
            'sao food',
            'Visiting <em>São</em> Paulo: top <em>food</em> spots',
            [false, true, true],
        ];

        yield [
            'Björk albums ranked: from Debut to Fossora',
            'bjork debut',
            '<em>Björk</em> albums ranked: from <em>Debut</em> to Fossora',
            [false, true, true],
        ];

        yield [
            'Björk discography ranked from best to worst',
            'bjork disko',
            'Björk discography ranked from best to worst',
            [false, false, true],
        ];
    }

    /**
     * @param array<int, bool>|null $options
     */
    #[DataProvider('highlightingProvider')]
    public function testHighlighting(
        string $query,
        string $text,
        string $expectedResults,
        ?array $options = null,
        string $highlightStartTag = '<em>',
        string $highlightEndTag = '</em>'
    ): void
    {
        $options ??= [false, false, false];

        $configuration = Configuration::create()
            ->setInsideWords($options[0])
            ->setFindAllOccurrences($options[1])
            ->setRequireMatchAll($options[2]);

        $highlight = $this->createHighlight($configuration);

        $highlightResult = $highlight->highlight(
            $query,
            $text,
            $highlightStartTag,
            $highlightEndTag,
        )->getHighlightedText();

        $this->assertSame(
            $expectedResults,
            $highlightResult,
        );
    }
}
