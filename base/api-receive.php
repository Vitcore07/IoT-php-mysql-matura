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

if (!hash_equals(API_KEY, string($_POST['api_key'] ?? ''))) {
    response(403, 'Forbidden: Invalid API Key');
}

$temp = floatval($_POST['temp'] ?? 0);
$humid = floatval($_POST['humid'] ?? 0);
$press = floatval($_POST['press'] ?? 0);
$ppm = floatval($_POST['PPM'] ?? 0);

foreach ([$temp, $humid, $press, $ppm] as $value) {
    if ($value === null || $value === '') {
        response(400, 'Bad Request: Missing required parameters');
    }
}

$data = new Data(0, date('Y-m-d H:i:s'), $temp, $humid, $press, $ppm);

$err = $data ->valid();
if ($err !== "") {
    response(400, 'Bad Request: ' . $err);
}

$err = $data->save();
if ($err !== "") {
    response(500, 'Internal Server Error: ' . $err);
}

response(200, 'Data saved successfully');