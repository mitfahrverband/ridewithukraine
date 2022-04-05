<?php
use core\http\client\HttpClient;

require_once "../core/Autoload.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $data = json_decode($body, flags: JSON_THROW_ON_ERROR);
    $response = HttpClient::post('https://ride2go.info/api/v1/trip', $body, [
        'X-API-Key' => '8GMCF50WN9rqVNxIqAtVc8rS9wCiLDdM',
        'Content-Type' => 'application/json',
    ]);
    http_response_code($response->status);
    exit;
}
http_response_code(404);
