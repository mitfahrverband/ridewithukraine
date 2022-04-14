<?php
namespace core\db\model\attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Geometry {

    function __construct(
        public ?int $srid = null,
    ) {
    }

    const srid4326 = 4326;

}
