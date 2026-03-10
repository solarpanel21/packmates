<?php

// Fetches weather data from Open-Meteo and returns a summary array, or null on failure.
// $lat and $lon are coordinates, $start and $end are dates in 'YYYY-MM-DD' format.
function fetch_weather(float $lat, float $lon, string $start, string $end): ?array {
    $url = 'https://api.open-meteo.com/v1/forecast?'
        . 'latitude=' . $lat
        . '&longitude=' . $lon
        . '&hourly=temperature_2m,precipitation_probability,precipitation,rain,snowfall,wind_speed_10m'
        . '&start_date=' . urlencode($start)
        . '&end_date='   . urlencode($end);

    $response = file_get_contents($url);
    if ($response === false) return null;

    $data = json_decode($response, true);
    $hourly = $data['hourly'] ?? null;
    if (!$hourly) return null;

    // Filter out null values before calculating min/max/averages
    $temps  = array_filter($hourly['temperature_2m'],           fn($v) => $v !== null);
    $winds  = array_filter($hourly['wind_speed_10m'],           fn($v) => $v !== null);
    $precip = array_filter($hourly['precipitation'],            fn($v) => $v !== null);
    $rain   = array_filter($hourly['rain'],                     fn($v) => $v !== null);
    $snow   = array_filter($hourly['snowfall'],                 fn($v) => $v !== null);
    $precip_prob = array_filter($hourly['precipitation_probability'], fn($v) => $v !== null);

    return [
        // Temperature in °C
        'temp_min'          => min($temps),
        'temp_max'          => max($temps),
        'temp_avg'          => array_sum($temps) / count($temps),

        // Wind speed in km/h
        'wind_max'          => max($winds),
        'wind_avg'          => array_sum($winds) / count($winds),

        // Total precipitation in mm over the whole period
        'total_precip_mm'   => array_sum($precip),
        'total_rain_mm'     => array_sum($rain),

        // Total snowfall in cm over the whole period
        'total_snow_cm'     => array_sum($snow),

        // Average chance of precipitation as a percentage
        'avg_precip_prob'   => array_sum($precip_prob) / count($precip_prob),
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
function testWeather(): void {
    $w = fetch_weather(30.4382, -84.2806, '2026-03-15', '2026-03-22');
    if (!$w) {
        error_log("testWeather: request failed");
        return;
    }

    error_log(sprintf(
        "Weather summary: temp min=%.1f°C max=%.1f°C avg=%.1f°C | " .
        "wind max=%.1f avg=%.1f km/h | " .
        "precip total=%.2fmm rain=%.2fmm snow=%.2fcm avg_prob=%.1f%%",
        $w['temp_min'], $w['temp_max'], $w['temp_avg'],
        $w['wind_max'], $w['wind_avg'],
        $w['total_precip_mm'], $w['total_rain_mm'], $w['total_snow_cm'],
        $w['avg_precip_prob']
    ));
}

testWeather();