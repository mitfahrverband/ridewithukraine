<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column {

    function __construct(
        public ?int    $length = null,
        public ?string $type = null,
        public ?bool   $unsigned = null,
        public ?bool   $notNull = null,
        public mixed   $default = null,
        public bool    $primaryKey = false,
        public mixed   $index = null,
        public bool    $autoIncrement = false,
    ) {
    }

    // Types
    const DATETIME = "datetime";
    const JSON = "json";
    const TEXT = "text";
    const BIGINT = "BIGINT";

    // Defaults
    const NOW = 'CURRENT_TIMESTAMP';

}
