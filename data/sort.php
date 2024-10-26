<?php

function arrToStr(array $arr): string {
    $str =  array_reduce($arr, function($p, $c) {
        return ($p ? "$p, " : "") . $c;
    }, "");

    return $str ? $str : "empty";
}

function bubbleSort(array $arr): array {
    for ($i = count($arr) - 2; $i >= 0; $i--) {
        for ($j = 0; $j <= $i; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $tmp = $arr[$j + 1];
                $arr[$j + 1] = $arr[$j];
                $arr[$j] = $tmp;
            }
        }
    }

    return $arr;
}