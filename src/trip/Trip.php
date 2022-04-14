<?php
namespace trip;

use core\db\model\attributes\{Column, HasMany, Id};
use core\db\model\Model;

class Trip extends Model {

    const TABLE = 'trips';

    #[Id(autoIncrement: false)]
    public int $id;
    #[Column(index: true)]
    public \DateTime $depart;
    #[Column(1000)]
    public string $url;

    #[HasMany(TripStop::class)]
    public array $stops;

}
