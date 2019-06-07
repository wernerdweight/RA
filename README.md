RA
==

PHP Array object with consistent, predictable and convenient API

[![Build Status](https://travis-ci.org/wernerdweight/RA.svg?branch=master)](https://travis-ci.org/wernerdweight/RA)
[![Latest Stable Version](https://poser.pugx.org/wernerdweight/ra/v/stable)](https://packagist.org/packages/wernerdweight/ra)
[![Total Downloads](https://poser.pugx.org/wernerdweight/ra/downloads)](https://packagist.org/packages/wernerdweight/ra)
[![License](https://poser.pugx.org/wernerdweight/ra/license)](https://packagist.org/packages/wernerdweight/ra)

Instalation
--

1) Download using composer

```bash
composer require wernerdweight/ra
```

2) Use in your project

```php
use WernerDweight\RA\RA;
 
// helper methods (extracted here to simplify the code below and emphasize the difference)

function filterFunction(string $god): bool {
    return false !== strpos($god, 's');
}
 
function mapFunction(string $godContainingTheLetterS): string {
    return strtoupper($godContainingTheLetterS);
}
 
function reduceFunction(string $carry, string $god): string {
    return $carry .= ($carry[-1] === ' ' ? '' : ', ') . $god;
}
 
// create new RA
$egyptianGods = new RA(['Ra', 'Osiris', 'Anubis', 'Horus']);
 
// use as object
$godsContainingTheLetterSInUppercase = $egyptianGods
    ->filter('filterFunction')
    ->map('mapFunction')
    ->reverse()
    ->reduce('reduceFunction', 'My favourite Egyptian Gods are ');
 
echo $godsContainingTheLetterSInUppercase . "\n";
 
// use as normal array
$godsContainingTheLetterSInUppercase = array_reduce(
    array_reverse(
        array_map(
            'mapFunction',
            array_filter(
                $egyptianGods->toArray(),
                'filterFunction'
            )
        )
    ),
    'reduceFunction',
    'My favourite Egyptian Gods are '
);
 
echo $godsContainingTheLetterSInUppercase . "\n";
 
// RA extends Iterator, ArrayAccess and Countable
foreach ($egyptianGods as $god) {
    echo sprintf("My favourite Egyptian God is %s\n", $god);
}
```

API
--

TODO:
