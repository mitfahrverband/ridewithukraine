<?php
use core\http\client\HttpClient;
use core\language\Language;

require_once "../core/Autoload.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $data = json_decode($body, flags: JSON_THROW_ON_ERROR);
    $url = 'https://ride2go.com/api/v1/trip?lang=' . Language::get();
    $response = HttpClient::post($url, $body, [
        'X-API-Key' => '8GMCF50WN9rqVNxIqAtVc8rS9wCiLDdM',
        'Content-Type' => 'application/json',
    ]);
    http_response_code($response->status);
    exit;
}
http_response_code(404);
