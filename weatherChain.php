<?php

// Calls the Open-Meteo geocoding API and returns the top result as an array,
// or null if the request failed or no results were found.
function geocode_lookup(string $query): ?array {
    // $url is built once here and used for the HTTP request below
    $url = 'https://geocoding-api.open-meteo.com/v1/search?name=' . urlencode($query) . '&count=1&language=en&format=json';

    // $response holds the raw JSON string returned by the API, or false on failure
    $response = file_get_contents($url);
    if ($response === false) return null;

    // $data is the decoded JSON as a PHP array; $data['results'] is an array of place matches
    $data = json_decode($response, true);

    // Return the first (best) match, or null if results are empty
    return $data['results'][0] ?? null;
}

/*
// When the file is requested by a browser with ?name=..., act as a JSON API endpoint
if (php_sapi_name() !== 'cli' && isset($_GET['name'])) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    // $r holds the top result array (keys: name, admin1, country, latitude, longitude, etc.)
    // or null if nothing was found
    $r = geocode_lookup($_GET['name']);
    echo $r
        ? json_encode(['name' => $r['name'], 'admin1' => $r['admin1'] ?? null, 'country' => $r['country'] ?? null, 'latitude' => $r['latitude'], 'longitude' => $r['longitude']])
        : json_encode(['error' => 'No results found']);
    exit;
}
    */

// Takes a place name and logs its name, state/province, country, and coordinates to the PHP error log
/*
function geocode(string $query): void {
    // $r holds the top result array, or null if the lookup failed
    $r = geocode_lookup($query);
    if (!$r) {
        error_log("geocode: no results for \"$query\"");
        return;
    }

    // $admin1 is the state or province (e.g. "Colorado"); falls back to 'unknown' if not provided
    $admin1  = $r['admin1']  ?? 'unknown';
    // $country is the full country name (e.g. "United States"); falls back to 'unknown' if not provided
    $country = $r['country'] ?? 'unknown';

    error_log("geocode: \"{$r['name']}\", $admin1, $country -> latitude={$r['latitude']}, longitude={$r['longitude']}");
}
*/





//fetches weather data from Open-Meteo and returns a summary array, if the
//$lat and $lon are coordinates, $start and $end are dates in 'YYYY-MM-DD' format.
function fetch_weather(float $lat, float $lon, string $start, string $end): ?array {
    // Determine whether to use forecast or historical API
    $forecast_cutoff = (new DateTime())->modify('+15 days');
    $trip_start = new DateTime($start);
    $use_historical = $trip_start > $forecast_cutoff;

    if (!$use_historical) {
        // FORECAST PATH
        $url = 'https://api.open-meteo.com/v1/forecast?'
            . 'latitude='  . $lat
            . '&longitude=' . $lon
            . '&hourly=temperature_2m,precipitation_probability,precipitation,rain,snowfall,wind_speed_10m'
            . '&start_date=' . urlencode($start)
            . '&end_date='   . urlencode($end);

        $response = file_get_contents($url);
        if ($response === false) return null;

        $data = json_decode($response, true);
        $hourly = $data['hourly'] ?? null;
        if (!$hourly) return null;

        // Parse and return forecast data directly
        return array_merge(['source' => 'forecast'], parse_weather($hourly));

    } else {
        // HISTORICAL PATH

        // Calculate the length of the trip in days
        $end_dt   = new DateTime($end);
        $start_dt = new DateTime($start);
        $trip_duration = $start_dt->diff($end_dt)->days; // number of days between start and end

        // Get the current year to anchor historical lookups
        $current_year = (int)(new DateTime())->format('Y');

        // Start from last year to ensure all historical dates are in the past
        $base_end_year = $current_year - 1;

        // Recreate the end date using the same month and day but anchored to last year
        $base_end = (new DateTime())->setDate($base_end_year, (int)$end_dt->format('m'), (int)$end_dt->format('d'));

        // Calculate the matching start date by going back the same number of days as the original trip duration
        $base_start = clone $base_end;
        $base_start->modify("-{$trip_duration} days");

        // Array to collect the parsed weather result from each historical year
        $historical_results = [];

        for ($i = 0; $i < 5; $i++) {
            // $i=0 is last year, $i=1 is 2 years ago, etc.

            // Shift the base end date back by $i years
            $iter_end = clone $base_end;
            $iter_end->modify("-{$i} years");

            // Shift the base start date back by the same amount to preserve trip duration
            $iter_start = clone $base_start;
            $iter_start->modify("-{$i} years");

            // Format dates as strings for the API URL
            $iter_start_str = $iter_start->format('Y-m-d');
            $iter_end_str   = $iter_end->format('Y-m-d');

            // Build the historical API URL for this year's iteration
            $url = 'https://archive-api.open-meteo.com/v1/archive?'
                . 'latitude='   . $lat
                . '&longitude=' . $lon
                . '&hourly=temperature_2m,precipitation,rain,snowfall,wind_speed_10m'
                . '&start_date=' . urlencode($iter_start_str)
                . '&end_date='   . urlencode($iter_end_str);

            // Make the API request
            $response = file_get_contents($url);
            if ($response === false) continue; // skip this year if request fails, try the rest

            // Decode the JSON response
            $data = json_decode($response, true);
            $hourly = $data['hourly'] ?? null;
            if (!$hourly) continue; // skip this year if no data came back

            // Parse this year's data and add to results array
            $historical_results[] = parse_weather($hourly);

        }

        // If none of the 5 years returned valid data, give up
        if (empty($historical_results)) return null;

        // Average all successful yearly results together
        // Start with the keys from the first result, then average each key across all years
        $averaged = [];
        $keys = array_keys($historical_results[0]); // e.g. temp_min, temp_max, etc.
        $count = count($historical_results); // how many years actually returned data



        foreach ($keys as $key) {
            // Sum this key across all years then divide by count to get the mean
            $averaged[$key] = array_sum(array_column($historical_results, $key)) / $count;
        }

         $averaged['source'] = 'historical';
        return $averaged;
    }
}

// Parses a raw hourly data array from either the forecast or historical API
// into a summary array of min/max/averages. Both APIs return the same structure
// so this function works for both.
function parse_weather(array $hourly): array {
$temps  = array_filter($hourly['temperature_2m'], function($v) { return $v !== null; });
$winds  = array_filter($hourly['wind_speed_10m'], function($v) { return $v !== null; });
$precip = array_filter($hourly['precipitation'], function($v) { return $v !== null; });
$rain   = array_filter($hourly['rain'], function($v) { return $v !== null; });
$snow   = array_filter($hourly['snowfall'], function($v) { return $v !== null; });
$precip_prob_raw = isset($hourly['precipitation_probability']) ? $hourly['precipitation_probability'] : [];
$precip_prob = array_filter($precip_prob_raw, function($v) { return $v !== null; });

$temp_min = !empty($temps) ? min($temps) : null;
    // Filter out any null values before calculating statistics
$temp_min        = !empty($temps) ? min($temps) : null;
$temp_max        = !empty($temps) ? max($temps) : null;
$temp_avg        = !empty($temps) ? array_sum($temps) / count($temps) : null;
$wind_max        = !empty($winds) ? max($winds) : null;
$wind_avg        = !empty($winds) ? array_sum($winds) / count($winds) : null;
$total_precip_mm = !empty($precip) ? array_sum($precip) : 0;
$total_rain_mm   = !empty($rain) ? array_sum($rain) : 0;
$total_snow_cm   = !empty($snow) ? array_sum($snow) : 0;
$avg_precip_prob = !empty($precip_prob) ? array_sum($precip_prob) / count($precip_prob) : null;


return [
    'temp_min'        => $temp_min,
    'temp_max'        => $temp_max,
    'temp_avg'        => $temp_avg,
    'wind_max'        => $wind_max,
    'wind_avg'        => $wind_avg,
    'total_precip_mm' => $total_precip_mm,
    'total_rain_mm'   => $total_rain_mm,
    'total_snow_cm'   => $total_snow_cm,
    'avg_precip_prob' => $avg_precip_prob,
];
}

if (php_sapi_name() !== 'cli' && isset($_GET['lat'], $_GET['lon'], $_GET['start'], $_GET['end'])) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    $w = fetch_weather((float)$_GET['lat'], (float)$_GET['lon'], $_GET['start'], $_GET['end']);
    echo $w
        ? json_encode($w)
        : json_encode(['error' => 'Request failed']);
    exit;
}


// Fetches weather for Tallahassee over the test date range and logs the summary
//change this later so that the coords can be input
//function testWeather(): void {
//    $w = fetch_weather(30.4382, -84.2806, '2026-03-15', '2026-03-22');
//    if (!$w) {
//        error_log("testWeather: request failed");
//        return;
//    }
//
//    error_log(sprintf(
//        "Weather summary: temp min=%.1f°C max=%.1f°C avg=%.1f°C | " .
//        "wind max=%.1f avg=%.1f km/h | " .
//        "precip total=%.2fmm rain=%.2fmm snow=%.2fcm avg_prob=%.1f%%",
//        $w['temp_min'], $w['temp_max'], $w['temp_avg'],
//        $w['wind_max'], $w['wind_avg'],
//        $w['total_precip_mm'], $w['total_rain_mm'], $w['total_snow_cm'],
//        $w['avg_precip_prob']
//    ));
//}
//
//testWeather();



//input location, receive tags (full chain)
function locationToTags(string $query, string $start, string $end): ?array {
    // Get coordinates from city name
    $results = geocode_lookup($query, 1);
    $place = $results[0] ?? null;
    if (!$place) return null;

    // Pass coordinates into weather fetch
    $weather = fetch_weather((float)$place['latitude'], (float)$place['longitude'], $start, $end);
    if (!$weather) return null;

    /* $weather array structure:
    $weather['temp_max']
    $weather['temp_min']
    $weather['total_rain_mm']
    $weather['total_snow_cm']
    $weather['wind_max']
    $weather['avg_precip_prob']
    */

$tags = [];
if ($weather['temp_max'] > 28) $tags[] = 'warm';
if ($weather['temp_min'] < 10 || $weather['total_snow_cm'] > 0) $tags[] = 'cold';
if ($weather['total_rain_mm'] > 0.5 || $weather['avg_precip_prob'] > 50) $tags[] = 'rain';
if ($weather['total_snow_cm'] > 0.25) $tags[] = 'snow';
if ($weather['wind_max'] >= 38) $tags[] = 'wind';

    return array_merge([
    'name'      => $place['name'],
    'admin1'    => $place['admin1'] ?? null,
    'country'   => $place['country'] ?? null,
    'latitude'  => $place['latitude'],
    'longitude' => $place['longitude'],
    'tags' => $tags
], $weather);


}

if (php_sapi_name() !== 'cli' && isset($_GET['name'], $_GET['start'], $_GET['end'])) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    $result = locationToTags($_GET['name'], $_GET['start'], $_GET['end']);
    echo $result
        ? json_encode($result)
        : json_encode(['error' => 'Request failed']);
    exit;
}