<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Id extends Column {

    public function __construct(string $type = null, bool $autoIncrement = true) {
        parent::__construct(type: $type, unsigned: true, primaryKey: true, autoIncrement: $autoIncrement);
    }

}
