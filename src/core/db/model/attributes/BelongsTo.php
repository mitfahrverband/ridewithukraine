<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class BelongsTo extends Reference {

    public function __construct(?string $table = null, ?string $column = 'id', ?string $onUpdate = null) {
        parent::__construct($table, $column, self::CASCADE, $onUpdate);
    }

}
