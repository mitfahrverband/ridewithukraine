<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class HasMany {

    function __construct(
        public string $modelClass,
    ) {
    }

}
