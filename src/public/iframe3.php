<html>
<head>
    <title>iframe</title>
    <link rel="stylesheet" type="text/css" href="/css/style.min.css">
    <meta name="iframe" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div id="iframe-container">
    <?php
    use core\Log;
    use page\TripList;
    use trip\{Trip, TripImport, TripStop};

    require_once "../core/Autoload.php";

    try {
        TripImport::onChange('../data/json_data.json');
    } catch (Exception $e) {
        Log::error($e);
    }

    $trips = Trip::query()->orderBy('depart')->list();
    TripStop::load($trips, 'stops');

    TripList::render($trips);
    ?>
</div>
</body>
</html>
