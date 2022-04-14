<?php
namespace trip;

use core\db\model\attributes\{BelongsTo, Column, Enum, Geometry, Id};
use core\db\model\Model;
use core\db\model\types\Point;

class TripStop extends Model {

    const TABLE = 'trip_stops';

    #[Id(Column::BIGINT)]
    public int $id;
    #[Column, BelongsTo]
    public int|Trip $trip;
    #[Column, Enum(['start', 'end'])]
    public ?string $type;

    #[Column(1000, index: true)]
    public ?string $line1;
    #[Column(250, index: true)]
    public ?string $city;
    #[Column(2, index: true)]
    public ?string $country;

    #[Column(index: true), Geometry(4326)]
    public Point $location;

}
