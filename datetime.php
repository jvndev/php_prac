<?php
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    "current_time" => date("Y-m-d h:i:s"),
]);
