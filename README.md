# php-fn-sortBy

Hey Hypoport, I don't know how to jvm but this is my approach to functional programming in PHP.

Not enough tests though.

Have fun, Ben

```
$cities = [
    ['name' => 'Shanghai',  'population' => 24256800, 'country' => 'China'],
    ['name' => 'Karachi',   'population' => 23500000, 'country' => 'Pakistan'],
    ['name' => 'Beijing',   'population' => 21516000, 'country' => 'China']
];

$fnCountry = function($v) { return $v['country']; };
$fnPop     = function($v) { return $v['population']; };
$fn = \techworker\fn\sortBy($fnCountry, \SORT_ASC)
    ->thenBy($fnPop);

usort($cities, $fn);
print_r($cities);
```