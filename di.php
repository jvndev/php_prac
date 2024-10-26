<?php

namespace DI;

class Person {
    public function __construct(public string $first_name, public string $last_name) {}

    #[\Override]
    public function __toString()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

interface IDataSource {
    public function getData(): array;
}

class DB implements IDataSource {
    #[\Override]
    public function getData(): array {
        return [];
    }
}

class Text implements IDataSource {
    private const FILE = __DIR__."/people";

    #[\Override]
    public function getData(): array {
        $lines = explode("\n", file_get_contents(Text::FILE));

        return array_map(function ($line) {
            preg_match_all("/[^;]+/", $line, $matches);
            $matches = $matches[0];

            return new Person($matches[0], $matches[1]);
        }, $lines);
    }
}

class Data {
    private readonly array $people;

    public function __construct(IDataSource $dataSource)
    {
       $this->people = $dataSource->getData(); 
    }

    public function getPeople(): array {
        return $this->people;
    }
}

function arrToStr(array $arr): string {
    if (!count($arr)) return "empty";

    return array_reduce($arr, function($p, $c) {
        return ($p ? "$p, " : "") . $c;
    }, "");
}

echo arrToStr((new Data(new Text()))->getPeople());
echo "\n";
echo arrToStr((new Data(new DB()))->getPeople());
echo "\n";