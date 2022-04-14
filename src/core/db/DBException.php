<?php
namespace core\db;

class DBException extends \RuntimeException {

    function __construct(
        public ?string     $sql = null,
        public ?array      $params = null,
        public ?\Exception $exception = null,
        private mixed      $retryFunction = null,
    ) {
        $message = $exception?->getMessage() ?? '';
        if ($this->sql) $message .= ".\n$sql\n" . json_encode($params);
        parent::__construct($message);
    }

    function isTableNotFound(): bool {
        if ((DB::$type & (DB::MySql | DB::MariaDB)) && str_starts_with($this->message, 'SQLSTATE[42S02]'))
            return true;
        return false;
    }

    function retry(): mixed {
        return ($this->retryFunction)();
    }

}
