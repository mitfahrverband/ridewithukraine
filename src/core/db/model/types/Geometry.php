<?php
namespace core\db\model\types;

use core\db\query\RawValue;

abstract class Geometry implements RawValue {

    function __construct(
        public ?int $srid = null,
    ) {
    }

    abstract static function fromString(string $string): Geometry;

    abstract function getWkt(): string;

    function getSql(): string {
        $wkt = "'" . $this->getWkt() . "'";
        if (isset($this->srid)) $wkt .= ",$this->srid";
        return "st_geomfromtext($wkt)";
    }

    function __toString(): string {
        return $this->getSql();
    }

}
