<?php
namespace core\db\model\types;

class Polygon extends Geometry {

    function __construct(
        public array $points = [],
        int          $srid = null,
    ) {
        foreach ($this->points as &$p) $p = is_array($p) ? new Point($p[0], $p[1], $srid) : $p;
        parent::__construct($srid);
    }

    function addPoint(float $x, float $y) {
        $this->points[] = new Point($x, $y);
    }

    static function fromString(string $string): Polygon {
        $string = substr($string, 9, strlen($string) - 11);
        $pairs = explode(',', $string);
        $polygon = new Polygon();
        foreach ($pairs as $pair) {
            $xy = explode(' ', $pair);
            $polygon->addPoint($xy[0], $xy[1]);
        }
        return $polygon;
    }

    function getWkt(): string {
        // Finish Polygon if not yet
        if ($this->points[array_key_last($this->points)] != $this->points[0]) {
            $this->points[] = $this->points[0];
        }
        $points = array_map(fn($p) => join(' ', $p->getAxisValues()), $this->points);
        $points = join(',', $points);
        return "POLYGON(($points))";
    }

    function transform(float $x, float $y, int $srid = null) {
        foreach ($this->points as $point) $point->transform($x, $y, $srid);
        $this->srid = $srid;
        return $this;
    }

    function changeAxisOrder() {
        foreach ($this->points as $point) $point->changeAxisOrder();
        return $this;
    }

}
