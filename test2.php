<?php

define('CONN', mysqli_connect('localhost', 'jaco', 'password', 'jaco'));

function insert(): string
{
    //mysqli_report(MYSQLI_REPORT_OFF);
    $driver = new mysqli_driver();
    $driver->report_mode = MYSQLI_REPORT_OFF;
    $sql = "
        insert into numbers
        (phpsessid, nr)
        value
        (?, ?)
    ";
    $stmt = CONN->prepare($sql);
    return $stmt && $stmt->execute([99, 99]) ? "": CONN->error;
}

echo insert();