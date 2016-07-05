# php-fn-sortBy

Prepares a sort function to be used in combination with [usort](https://php.net/) to sort an array of any shape in a 
functional way.

``` php
sortBy(..)->thenBy(..)->thenBy(.., \SORT_DESC)
```

[![Minimum PHP Version](https://img.shields.io/badge/PHP-7-green.svg?style=flat-square)](https://php.net/)

## Installation

You may use [Composer](https://getcomposer.org/) to download and install the function 
as well as its dependencies.

Simply add a dependency on techworker/php-fn-sortby to your project's composer.json file. Here is 
a minimal example of a composer.json file that defines a dependency on php-fn-sortby version 1.*:

``` json
{
    "require": {
        "techworker/php-fn-sortby": "~1.0"
    }
}
```

Small drawback I think you should know about, but I would say OpCache will work around this:

The library uses composers `autoload->files` to make the function available to you. Autoloading for 
functions does not work in PHP. So the file is always loaded, no matter if it used or not.

You can, of course, download the lib and require it as you wish.

## Definition and usage

First import the function so you can easily use or alias it.

``` php

// import
use function techworker\fn\sortBy;

// usage
sortBy($criteria1)->thenBy($criteria2)->thenBy($criteria3);
```

The only visible function for you is `sortBy`. Everything else are dynamically created functions 
(or better objects implementing the `techworker\fn\ThenByInterface`) that you can use in the 
same way as `sortBy`. 

The definition looks like this:

`sortBy(int|string|callable $comparator[, int $direction = \SORT_ASC[, callable $decorator = null]]) : ThenByInterface`

 - *string | int | callable* **$criteria**
   This can either be a string or a callable function that returns the value to sort by. 
   
 - *int* **$direction**
   The direction to sort by. This can be one of the two predefined `SORT_*` constants in PHP:
   [`\SORT_ASC`](http://php.net/manual/en/array.constants.php#constant.sort-asc) AND 
   [`\SORT_DESC`](http://php.net/manual/de/array.constants.php#constant.sort-desc).
    
 - *callable* **$decorator**
   A decorator that can be used to apply custom sort logic depending on your `$criteria`. The built-
   in decorator should be powerful enough for 99% of the cases, so just ignoring it will bring you
   good results. If you ever need another one, go and find out what a decorator does and how to 
   use it. We will omit this in the documentation.
    
### Using a string as sort criteria

Imagine we have an array of cities and want to `sortBy` country ascending and `thenBy` population
descending. 

``` php
$cities = [
    ['name' => 'Shanghai', 'population' => 24256800, 'country' => 'China'],
    ['name' => 'Karachi',  'population' => 23500000, 'country' => 'Pakistan'],
    ['name' => 'Beijing',  'population' => 21516000, 'country' => 'China']
];
```

This is the most simple use-case and we can do it like this:

``` php
use function techworker\fn\sortBy;

// create a sort callback used for the builtin PHP usort function.
$sorter = sortBy('name')->thenBy('population', \SORT_DESC);
usort($cities, $sorter);
```

The cities array is now sorted. You can even use an array of objects. The lib will try to 
determine the value either by an array key `[$criteria]`or by an object property `->{$criteria}`.

### Using a callback as sort criteria

If you need some deeper login to retrieve a sort criterial, you can provide a function.

Imagine you have an array of departments with its employees and an avg salary nested like this:

```
$departments = [
    'devops' => [
        'employees' => ['ben', 'peter', 'james', 'lisa', 'tequila'], // 5
        'salary' => 4000
    ],
    'sysop' => [
        'employees' => ['titus', 'raffy', 'ramanda', 'kathleen', 'rufus'], // 5
        'salary' => 3000
    ],
    'mgmt' => [
        'employees' => ['zoey', 'tiny', 'raphael', 'christian', 'michael', 'rene', 'benny', 
                        'johannes', 'sebastian', 'pedro', 'christoph'], // 11
        'salary' => 10000
    }
];
```

Now you want to sort this array by the number of employees DESC and then by the salary DESC. 
The expected result should be something like this:

 - mgmt -> 11 employees, 10000 salary
 - devop -> 5 employees, 4000 salary
 - sysop -> 5 employees, 3000 salary

Here is how it can work:

``` php
use function techworker\fn\sortBy;

// I declare the callback in variables for better readability
$fnNumberEmployees = function($department) {
    return count($department['employees']);
}

// create a sort callback used for the builtin PHP usort function.
$sorter = sortBy($fnNumberEmployees, \SORT_DESC)->thenBy('salary', \SORT_DESC);
usort($departments, $sorter);
```

As you might know, a comparer function provided to `usort` is expecting 2 parameters and returns
an integer to tell the sorter if the value is `bigger` (>0), `lower` (<0) or `equal` (0).

The wrapping of your callback with just one parameter is done by the decorator (3. parameter of 
the inital `sortBy` call). 

So if you provide a callback with only one parameter (unary), the decorator will automatically wrap
the function and will do the comparism for you using the spaceship operator `<=>`.
 
If you provide a callback with two parameters, you can do the comparism by yourself.

See the following example for a callback that uses 2 parameters:
 
```
use function techworker\fn\sortBy;

// I declare the callback in variables for better readability
$fnNumberEmployees = function($department1, $department2) {
    return count($department1['employees']) - count($department2['employees']);
    // or 
    // return count($department1['employees']) <=> count($department2['employees']);
}

// create a sort callback used for the builtin PHP usort function.
$sorter = sortBy($fnNumberEmployees, \SORT_DESC)->thenBy('salary', \SORT_DESC);
usort($departments, $sorter);
```

This should give you the same results as above, but you gain more control about the comparing
functionality itself.

### ThenByInterface

The `sortBy` and `thenBy` function all return a dynamic class instance of the `ThenByInterface`, so
it is easier for the IDE to autocomplete the calls.

Take a look at the implementation src, it's fairly simple compared to various other solutions. It
just looks complicated. If you have any bugs, suggestions, whatever, get in touch with me through 
the GitHub issues.

I hope you find this useful!

## Credits

This function is roughly ported from https://github.com/Teun/thenBy.js - a javascript function with 
more or less the same set of features. Thanks a lot for the idea!

The kicker to port and enhance it was the following issue:

https://github.com/Teun/thenBy.js/issues/10 :-)