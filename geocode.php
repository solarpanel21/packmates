<?php
function geocode_lookup(string $query, int $count = 1): ?array {
    $url = 'https://geocoding-api.open-meteo.com/v1/search?name=' . urlencode($query) . '&count=' . $count . '&language=en&format=json';
    $response = file_get_contents($url);
    if ($response === false) return null;
    $data = json_decode($response, true);
    return $data['results'] ?? null;
}

if (php_sapi_name() !== 'cli' && isset($_GET['name'])) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    $count = isset($_GET['count']) ? (int)$_GET['count'] : 10;
    $results = geocode_lookup($_GET['name'], $count);
    if (!$results) {
        echo json_encode(['error' => 'No results found']);
    } else {
        $output = [];
        foreach ($results as $r) {
            $output[] = [
                'name'      => $r['name'],
                'admin1'    => $r['admin1'] ?? null,
                'country'   => $r['country'] ?? null,
                'latitude'  => $r['latitude'],
                'longitude' => $r['longitude']
            ];
        }
        echo json_encode(['results' => $output]);
    }
    exit;
}