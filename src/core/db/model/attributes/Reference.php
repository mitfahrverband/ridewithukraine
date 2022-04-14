<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Reference {

    function __construct(
        public ?string $table = null,
        public ?string $column = 'id',
        public ?string $onDelete = null,
        public ?string $onUpdate = null,
    ) {
    }

    const CASCADE = "CASCADE";
    const SET_NULL = "SET NULL";

}
