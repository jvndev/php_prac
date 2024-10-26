<?php

class Person
{
    protected function __construct(public string $first_name, public string $last_name) {}

    #[\Override]
    public function __toString()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

interface IDataSource
{
    public function getData(): array;
}

class DB implements IDataSource
{
    private readonly PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=jaco;',
            'jaco',
            'password',
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    #[\Override]
    public function getData(): array
    {
        return $this->pdo
            ->query("select first_name, last_name from employees")
            ->fetchAll();
    }
}

class Text implements IDataSource
{
    private const FILE = __DIR__ . "/people";

    #[\Override]
    public function getData(): array
    {
        $lines = explode("\n", file_get_contents(Text::FILE));

        return array_map(function ($line) {
            preg_match_all("/[^;]+/", $line, $matches);
            $matches = $matches[0];

            return [
                "first_name" => $matches[0],
                "last_name" => $matches[1],
            ];
        }, $lines);
    }
}

class Data extends Person
{
    private readonly array $people;

    public function __construct(IDataSource $dataSource)
    {
        $this->people = array_map(function ($assoc) {
            return new Person($assoc['first_name'], $assoc['last_name']);
        }, $dataSource->getData());
    }

    public function getPeople(): array
    {
        return $this->people;
    }
}

function arrToStr(array $arr): string
{
    if (!count($arr)) return "empty";

    return array_reduce($arr, function ($p, $c) {
        return ($p ? "$p, " : "") . $c;
    }, "");
}

echo arrToStr((new Data(new Text()))->getPeople());
echo "\n";
echo arrToStr((new Data(new DB()))->getPeople());
echo "\n";
