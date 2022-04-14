<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Index {

    function __construct(
        public ?string $name = null,
        public ?int    $length = null,
        public ?bool   $fulltext = null,
    ) {
    }

}
