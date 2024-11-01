<?php
$strs = ['phpsessid', 'action-flush', 'nr'];

function getPostAction(array $post): string
{
    return array_reduce(
        $post,
        fn(string $p, string $e)
        => preg_match('/^action-\w+$/', $e, $matches) !== 0
            ? $matches[0]
            : $p,
        ''
    );
}

echo getPostAction($strs);