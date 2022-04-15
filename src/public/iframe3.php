<html>
<head>
    <title>iframe</title>
    <link rel="stylesheet" type="text/css" href="/css/style.min.css">
    <meta name="iframe" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div id="iframe-container">
    <?php
    use core\language\Label;
    use core\db\model\{types\Point, types\Polygon};
    use core\http\request\Request;
    use core\Log;
    use page\TripList;
    use trip\{Trip, TripImport, TripStop};

    require_once "../core/Autoload.php";

    Label::addFile(__DIR__ . '/../labels/labels_en.properties');

    try {
        TripImport::onChange('../data/json_data.json');
    } catch (Exception $e) {
        Log::error($e);
    }

    $query = Trip::query()->orderBy('depart')->limit(60);

    $lat = Request::paramUrl('lat')->float()->clamp(-90.0, 90.0)->value;
    $lon = Request::paramUrl('lon')->float()->clamp(-180.0, 180.0)->value;
    if ($lat && $lon) {
        $r = Request::paramUrl('r')->int()->clamp(1, 1000)->value ?? 50;
        $r *= 1000;
        $point = new Point($lat, $lon, 4326);
        $polygon = new Polygon([[$r, $r], [-$r, $r], [-$r, -$r], [$r, -$r]]);
        $polygon->transform($point->x, $point->y, 4326);

        $query->join(TripStop::TABLE . " ts ON ts.trip = model.id AND ts.type = 'start'");
        $query->where("st_contains($polygon, location) AND st_distance_sphere($point, location) <= ?", $r);
    }
    $depart = Request::paramUrl('depart')->int()->clamp(0, PHP_INT_MAX)->value;
    if ($depart) {
        $dt = (new DateTime())->setTimestamp($depart);
        $query->where('depart >= ?', $dt);
    }

    $trips = $query->list();
    TripStop::load($trips, 'stops');

    if ($trips) {
        TripList::render($trips);
    } else {
        ?>
        <div class="text-center"><?= Label::get('results.none') ?></div>
        <?php
    }
    ?>
</div>
</body>
</html>
