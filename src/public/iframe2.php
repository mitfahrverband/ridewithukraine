<html>
<head>
    <title>iframe</title>
    <link rel="stylesheet" type="text/css" href="/css/style.min.css">
    <meta name="iframe" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div id="iframe-container">
    <?php
    use page\TripList;

    require_once "../core/Autoload.php";

    $filedata = file_get_contents('../data/json_data.json');
    $details = json_decode($filedata);

    # store dates of next 7 weekdays once as $nextdays
    $thisday = strtotime("today");
    for ($i = 0; $i <= 6; $i++) {
        $nextdays[strtolower(date("l", $thisday))] = date("Y-m-d", $thisday);
        $thisday += 60 * 60 * 24; # go to next day
    }

    foreach ($details as $item) {
        # if departDate is not set then fill it with the next available trip date as defined in weekdays data
        if (!isset($item->departDate)) {
            foreach ($nextdays as $dayname => $daydate) {
                if (isset($item->weekdays)) {
                    if (in_array($dayname, $item->weekdays)) {
                        $item->departDate = $daydate;
                        break;
                    }
                }
            }
        }
        # if (!isset($item->departDate)) { $item->departDate = "2022-12-32"; }

        # check out all address values for city names (to shorten the text display)
        # move country information to new data structure (to allow display of country flags)
        # remove detailed location information for now
        $coveredcountries = array("DE", "Deutschland", "UA", "Ukraine", "AT", "Österreich", "PL", "Polen");
        $address = '';
        foreach ($item->stops ?? [] as $stop) {
            $components = explode(", ", $stop->address ?? '');
            $last = array_pop($components);
            if (in_array($last, $coveredcountries)) {
                $stop->country = $last;
                $city = array_pop($components);
            } else {
                $stop->country = "Unknown";
                $city = $last;
            }
            # if previous address matches the city of this stop then do not repeat the same city again.
//            if (strcmp($address, $city) == 0) {
//                $stop->address = '-----';
//            } else {
//            }
            $address = $city;
            $stop->address = $address;
            # adding the remaining information:
            # . ", " . implode(", ", $components);
        }
    }
    # var_dump($details);
    # array_multisort($details, $details->departDate);

    ?>
    <?php
    TripList::render($details);
    ?>
</div>
</body>
</html>
