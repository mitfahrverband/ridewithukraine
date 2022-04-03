<?php
namespace core\http\client;

use RuntimeException;

class HttpClient {

    static function get(string $url, array $headers = []): HttpClientResponse {
        return static::request('GET', $url, null, $headers);
    }

    static function post(string $url, string $body = null, array $headers = []): HttpClientResponse {
        return static::request('POST', $url, $body, $headers);
    }

    static function delete(string $url, array $headers = []): HttpClientResponse {
        return static::request('DELETE', $url, null, $headers);
    }

    static function request(string $method, string $url, string $body = null, array $headers = []): HttpClientResponse {
        $httpOpts = [
            'method' => $method,
            'ignore_errors' => true
        ];
        if (isset($body)) {
            $httpOpts += ['content' => $body];
        }
        $headerString = '';
        foreach ($headers as $name => $value) {
            $headerString .= "$name: $value\r\n";
        }
        if ($headerString) {
            $httpOpts += ['header' => $headerString];
        }
        $opts = ['http' => $httpOpts];
        $context = stream_context_create($opts);

        set_error_handler(fn($severity, $message) => throw new RuntimeException($message));
        $response = @file_get_contents($url, false, $context);
        restore_error_handler();

        return new HttpClientResponse($response, $http_response_header ?? []);
    }

}
