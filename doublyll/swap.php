<?php

$swap = fn($a, $b): array => [$a + ($b - $a), $b - ($b - $a)];
$range = [0, 1000];

for ($i = 0; $i < 10; $i++) {
    $arr = [rand(...$range), rand(...$range)];
    $swapped = $swap(...$arr);
    printf(
        "%s\n%s\n\n",
        implode(",", $arr),
        implode(",", $swapped),
    );

    assert(implode($arr) === implode([$swapped[1], $swapped[0]]));
}