<?php
namespace core\http\client;

class HttpClientResponse {

    public int $status;
    public array $headers;

    function __construct(
        public string $body,
        array         $headers,
    ) {
        if ($headers) {
            $this->status = (int)explode(' ', $headers[0])[1] ?? 0;
            $headers = array_slice($headers, 1);
            foreach ($headers as $header) {
                $elements = explode(': ', $header, 2);
                $this->headers[$elements[0]] = $elements[1];
            }
        }
    }

}
