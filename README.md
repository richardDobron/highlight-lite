<div align="center">
  <img src="./logo/logo.svg" width="355px" alt="HIGHLIGHT your search">
  <p>Lightweight PHP library designed to <u>highlight</u> search terms in text.</p>
</div>

## 📖 Requirements
* PHP 7.4 or higher
* [Composer](https://getcomposer.org) is required for installation
* PHP Extensions: `ext-mbstring`

## 📦 Installation

Install the library using Composer:

```shell
$ composer require richarddobron/highlight-lite
```

## ⚡️ Quick Start

Here’s how to use the library to highlight search terms:

```php
use dobron\HighlightLite\Configuration;
use dobron\HighlightLite\HighlightFactory;

$configuration = Configuration::create()
    ->setInsideWords(true)
    ->setFindAllOccurrences(true)
    ->setRequireMatchAll(true);

$highlightResult = (new HighlightFactory())
    ->create($configuration)
    ->highlight('hawking history of time', 'Stephen Hawking: A Brief History of Time');

echo $highlightResult->getHighlightedText(); // Stephen <em>Hawking</em>: A Brief <em>History of Time</em>
```

## ⚙️ Configuration Options

You can customize the library with the following methods:

| Method                                            | Description                                                     | Default |
|---------------------------------------------------|-----------------------------------------------------------------|---------|
| `setInsideWords(bool $insideWords)`               | Enables or disables highlighting inside words.                  | `false` |
| `setFindAllOccurrences(bool $findAllOccurrences)` | Enables or disables finding all occurrences of the search term. | `false` |
| `setRequireMatchAll(bool $requireMatchAll)`       | Enables or disables requiring all search terms to match.        | `false` |
| `setMatchShortcuts(bool $matchShortcuts)`         | Enables or disables matching shortcuts of the search terms.     | `false` |
## 📅 Change Log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🧪 Testing

```shell
$ composer tests
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## ⚖️ License
This repository is MIT licensed, as found in the [LICENSE](LICENSE) file.
