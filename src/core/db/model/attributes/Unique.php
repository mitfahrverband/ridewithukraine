<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Unique {

    function __construct(
        public ?string $name = null,
    ) {
    }

}
