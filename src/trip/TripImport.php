<?php
namespace trip;

use core\db\DB;
use core\db\model\types\Point;
use core\file\FileImport;

class TripImport extends FileImport {

    static function onChange(string $path, callable $cb = null) {
        parent::onChange($path, function ($path) {
            $data = static::getData($path);
            self::store($data);
        });
    }

    static function store(array $data) {
        DB::beginTransaction();
        Trip::query()->delete();
        TripStop::query()->delete();

        $i = 0;
        $trips = [];
        $stops = [];
        foreach ($data as $element) {
            if (!isset($element->departDate) || !isset($element->departTime)) continue;
            $i++;
            $trips[] = [
                'id' => $i,
                'depart' => new \DateTime("$element->departDate $element->departTime"),
                'url' => $element->deeplink,
            ];
            foreach ($element->stops as $key => $stop) {
                $stops[] = [
                    'trip' => $i,
                    'type' => array_key_first($element->stops) === $key ? 'start' :
                        (array_key_last($element->stops) === $key ? 'end' : null),
                    'country' => static::getCountry($stop->country),
                    'city' => $stop->city ?? null,
                    'line1' => $stop->line1 ?? null,
                    'location' => new Point($stop->coordinates->lat, $stop->coordinates->lon, 4326),
                ];
            }
        }

        foreach (array_chunk($trips, 1000) as $chunk) Trip::query()->insert(...$chunk);
        foreach (array_chunk($stops, 1000) as $chunk) TripStop::query()->insert(...$chunk);
        DB::commit();
    }

    static function getData(string $path) {
        $filedata = file_get_contents($path);
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

            # check out all address values for city names (to shorten the text display)
            # move country information to new data structure (to allow display of country flags)
            # remove detailed location information for now
            $coveredcountries = array("DE", "Deutschland", "UA", "Ukraine", "AT", "Österreich", "PL", "Polen", "null");
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
                $stop->city = $city;
                $stop->line1 = join(',', $components);
            }
        }
        return $details;
    }

    static function getCountry(string $country): string|null {
        if (strlen($country) === 2) return strtoupper($country);
        $countries = [
            'Deutschland' => 'DE',
            'Ukraine' => 'UA',
            "Österreich" => 'AT',
            'Polen' => 'PL',
        ];
        return $countries[$country] ?? null;
    }

}
