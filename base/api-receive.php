<?php

require_once __DIR__ . '/../models/Data.php';

header('Content-Type: application/json');

function response($status, $message) 
{
    http_response_code($status);
    echo json_encode(['message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, 'Method Not Allowed');
}

if (!hash_equals(DB_API_KEY, (string)($_POST['api_key'] ?? ''))) {
    response(403, 'Forbidden: Invalid API Key');
}

$temp = filter_input(INPUT_POST, 'temp', FILTER_VALIDATE_FLOAT);
$humid = filter_input(INPUT_POST, 'humid', FILTER_VALIDATE_FLOAT);
$press = filter_input(INPUT_POST, 'press', FILTER_VALIDATE_FLOAT);
$ppm = filter_input(INPUT_POST, 'ppm', FILTER_VALIDATE_FLOAT);

foreach ([$temp, $humid, $press, $ppm] as $value) {
    if ($value === null || $value === false) {
        response(400, 'Bad Request: Missing required parameters');
    }
}

$data = new Data($temp, $humid, $press, $ppm);

$err = $data ->valid();
if ($err !== "") {
    response(400, 'Bad Request: ' . $err);
}

$err = $data->save();
if ($err !== "") {
    error_log("Error saving data: " . $err);
    response(500, 'Internal Server Error: Failed to save data');
}

response(200, 'Data saved successfully');