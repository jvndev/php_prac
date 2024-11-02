<?php
$phpsessid = '42';

$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_OFF;

$conn = mysqli_connect('localhost', 'jaco', 'password', 'jaco');

($stmt = $conn->prepare("select nr from numberz where phpsessid = ?"))
    || die("Statement failed");
$stmt->execute([$phpsessid]) || die("Execute failed");
$stmt->bind_result($nr) || die("Bind failed");

while ($stmt->fetch())
    echo "$nr\n";
