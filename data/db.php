<?php

class DB {
    private const connStr = "mysql:host=localhost;dbname=jaco;charset=utf8;";
    private const user = 'jaco';
    private const pass = 'password';

    private readonly PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(DB::connStr, DB::user, DB::pass, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);
    }

    function get(): array {
        $query = "select id, first_name, last_name, comments from employees";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function insert(string $firstName, string $lastName, string $comments): void {
        $query = "
            insert into employees
            (first_name, last_name, comments)
            values
            (:first_name, :last_name, :comments)
        ";
        $stmt = $this->pdo->prepare($query);
        $success = $stmt->execute(
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'comments' => $comments,
            ]
        );

        if (!$success)
            throw new PDOException("Failed to insert");
    }
}
