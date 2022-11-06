# Address Parser

> Can parse a physical address into its parts.

## Installation

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this package:

```console
$ composer require sbolch/address-parser
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of Composer documentation.

## Usage

```php
$locale = 'HU';
$parser = new \sbolch\AddressParser\Parser($locale);

$parser->parse('1152 Budapest, Szentmihályi út 167');

/*
This returns:
Array
(
    [zip] => 1152
    [city] => Budapest
    [street] => Szentmihályi
    [streetType] => út
    [houseNumber] => 167
    [houseNumberInfo] =>
)
*/
```

Note: Only Hungarian address format is supported yet. You can request another
locale for me to do or create a pull request by implementing
\sbolch\AddressParser\ParserInterface into \sbolch\AddressParser\Parser namespace
with your own locale's class. You can place your custom files into the
locales folder under your locale.
