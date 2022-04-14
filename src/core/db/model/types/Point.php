<?php
namespace core\db\model\types;

use core\db\DB;

class Point extends Geometry {

    function __construct(
        public float $x,
        public float $y,
        int          $srid = null
    ) {
        parent::__construct($srid);
    }

    static function fromString(string $string): Point {
        $pair = substr($string, 6, strlen($string) - 7);
        $xy = explode(' ', $pair);
        return new Point($xy[0], $xy[1]);
    }

    static function fromBinary(string $binary) {
        $data = unpack('Vsrid/CbyteOrder/Vtype/ex/ey', $binary);
        $srid = $data['srid'];
        if ($srid === 4326) return new Point($data['y'], $data['x'], $srid);
        return new Point($data['x'], $data['y'], $srid);
    }

    function getAxisValues(): array {
        if ($this->srid === 4326 && (DB::$type & DB::MariaDB)) return [$this->y, $this->x];
        return [$this->x, $this->y];
    }

    function getWkt(): string {
        $values = $this->getAxisValues();
        return "POINT($values[0] $values[1])";
    }

    function transform(float $x, float $y, int $srid = null) {
        if ($this->srid != $srid && $srid === 4326) {
            $this->srid = 4326;
            $xt = $this->x;
            $this->x = $this->y / 110574;
            $this->y = $xt / 111320;
            $this->y = $this->y / cos(deg2rad($this->x + $x));
        }
        $this->x += $x;
        $this->y += $y;
    }

    function changeAxisOrder() {
        $xt = $this->x;
        $this->x = $this->y;
        $this->y = $xt;
    }

}
