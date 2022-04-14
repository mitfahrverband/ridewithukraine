<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Enum {

    function __construct(
        public array $values
    ) {
    }

}
