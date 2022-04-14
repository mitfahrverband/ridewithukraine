<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class HasOne {

    function __construct(
        public ?string $modelClass = null,
    ) {
    }

}
