<?php

/*
    git
    json & datetime & json & setInterval & custom sleep
    checkboxes
    ascii char values
    serialization
    perl regex
*/

require "./data/db.php";

function resolveURL()
{
    $uri = $_SERVER['REQUEST_URI'];
    $uri = preg_replace('/(?<=\/)[^\/]*$/', '', $uri);

    return sprintf(
        "%s://%s:%s%s",
        $_SERVER['REQUEST_SCHEME'],
        $_SERVER['HTTP_HOST'],
        $_SERVER['SERVER_PORT'],
        $uri,
    );
}

$posts = "";
$db = new DB();

if (isset($_POST['first_name']) && isset($_POST['last_name'])) {
    $db->insert($_POST['first_name'], $_POST['last_name'], $_POST['comments']);
}

$_ = 'htmlentities';
foreach ($db->get() as $employee) {
    $firstName = $_($employee->first_name);
    $lastName = $_($employee->last_name);
    $comments = $_($employee->comments);
    $posts .= "
        <p class='post'>
            $firstName $lastName : $comments
        </p>
    ";
}

$url = resolveURL();
$html = file_get_contents("./view/index.html");
$html = str_replace('{{url}}', $url, $html);
$html = str_replace('{{posts}}', $posts, $html);

printf("%s", $html);