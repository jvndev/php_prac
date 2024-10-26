<?php
/*
        js sleep
    */
session_start();

if (!isset($_SESSION['rnd'])) {
    $rnd = 'S' . rand(0, 1000);
    $someVar = "<i>$rnd</i>";

    $_SESSION['rnd'] = $rnd;
} else {
    $someVar = $_SESSION['rnd'];
}

if (!isset($_COOKIE['acook'])) setcookie("acook", 42);
?>


<!DOCTYPE html>
<html>

<head>
    <script src='http://localhost/php_prac/js/datetime.js'></script>
</head>

<body>
    <b><?= htmlspecialchars($someVar) ?></b>
    <div id='divDateTime'>
    </div>
</body>

</html>