<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== Testing Geocoding Service Directly ===\n\n";

// Test the geocoding service
$service = new \App\Services\GeocodingService();

// Test with USA address
$address = "1200 Ocean Dr, Miami Beach, FL 33139, USA";
echo "Testing address: $address\n\n";

$result = $service->getCoordinates($address);

if ($result) {
    echo "✅ SUCCESS!\n\n";
    echo "Latitude: " . $result['latitude'] . "\n";
    echo "Longitude: " . $result['longitude'] . "\n";
    echo "Formatted Address: " . $result['formatted_address'] . "\n";
    echo "Location Type: " . $result['location_type'] . "\n\n";
    
    echo "JSON Response:\n";
    echo json_encode([
        'success' => true,
        'message' => 'Geocoding successful',
        'data' => $result
    ], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "❌ FAILED\n\n";
    echo "Check storage/logs/laravel-2025-10-27.log for details\n";
}


