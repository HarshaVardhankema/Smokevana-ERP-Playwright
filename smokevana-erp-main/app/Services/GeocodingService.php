<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
    protected $placeDetailsUrl = 'https://maps.googleapis.com/maps/api/place/details/json';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    /**
     * Get latitude and longitude from address
     * 
     * @param string $address Full address to geocode
     * @return array|null Returns ['latitude' => float, 'longitude' => float] or null on failure
     */
    public function getCoordinates($address)
    {
        // Check if API key is configured
        if (empty($this->apiKey)) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        // Clean and validate address
        $address = trim($address);
        if (empty($address)) {
            Log::warning('Empty address provided for geocoding');
            return null;
        }

        try {
            // Make request to Google Maps Geocoding API
            $response = Http::timeout(10)->get($this->baseUrl, [
                'address' => $address,
                'key' => $this->apiKey
            ]);

            // Check if request was successful
            if (!$response->successful()) {
                Log::error('Google Maps API request failed', [
                    'status' => $response->status(),
                    'address' => $address
                ]);
                return null;
            }

            $data = $response->json();

            // Check API response status
            if ($data['status'] !== 'OK') {
                Log::warning('Google Maps API returned non-OK status', [
                    'status' => $data['status'],
                    'address' => $address,
                    'error_message' => $data['error_message'] ?? 'No error message'
                ]);
                return null;
            }

            // Extract coordinates from response
            if (isset($data['results'][0]['geometry']['location'])) {
                $location = $data['results'][0]['geometry']['location'];
                
                $coordinates = [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'formatted_address' => $data['results'][0]['formatted_address'] ?? null,
                    'location_type' => $data['results'][0]['geometry']['location_type'] ?? null
                ];

                Log::info('Successfully geocoded address', [
                    'address' => $address,
                    'coordinates' => $coordinates
                ]);

                return $coordinates;
            }

            Log::warning('No coordinates found in Google Maps API response', [
                'address' => $address
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Exception during geocoding', [
                'address' => $address,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Build full address from components
     * 
     * @param array $addressComponents
     * @return string
     */
    public function buildAddress($addressComponents)
    {
        $parts = array_filter([
            $addressComponents['address_line_1'] ?? '',
            $addressComponents['address_line_2'] ?? '',
            $addressComponents['city'] ?? '',
            $addressComponents['state'] ?? '',
            $addressComponents['country'] ?? '',
            $addressComponents['zip_code'] ?? ''
        ]);

        return implode(', ', $parts);
    }

    /**
     * Geocode from address components
     * 
     * @param array $addressComponents
     * @return array|null
     */
    public function geocodeFromComponents($addressComponents)
    {
        $fullAddress = $this->buildAddress($addressComponents);
        return $this->getCoordinates($fullAddress);
    }

    /**
     * Get coordinates from Google Places place_id
     * 
     * @param string $placeId The place_id from Google Places Autocomplete
     * @return array|null Returns ['latitude' => float, 'longitude' => float, 'formatted_address' => string] or null on failure
     */
    public function getCoordinatesFromPlaceId($placeId)
    {
        // Check if API key is configured
        if (empty($this->apiKey)) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        // Validate place_id
        $placeId = trim($placeId);
        if (empty($placeId)) {
            Log::warning('Empty place_id provided');
            return null;
        }

        try {
            // Make request to Google Places Details API
            $response = Http::timeout(10)->get($this->placeDetailsUrl, [
                'place_id' => $placeId,
                'fields' => 'geometry,formatted_address,address_components,name',
                'key' => $this->apiKey
            ]);

            // Check if request was successful
            if (!$response->successful()) {
                Log::error('Google Places API request failed', [
                    'status' => $response->status(),
                    'place_id' => $placeId
                ]);
                return null;
            }

            $data = $response->json();

            // Check API response status
            if ($data['status'] !== 'OK') {
                Log::warning('Google Places API returned non-OK status', [
                    'status' => $data['status'],
                    'place_id' => $placeId,
                    'error_message' => $data['error_message'] ?? 'No error message'
                ]);
                return null;
            }

            // Extract coordinates and address from response
            if (isset($data['result']['geometry']['location'])) {
                $location = $data['result']['geometry']['location'];
                $result = $data['result'];
                
                // Parse address components
                $addressComponents = $this->parseAddressComponents($result['address_components'] ?? []);
                
                $coordinates = [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'formatted_address' => $result['formatted_address'] ?? null,
                    'place_id' => $placeId,
                    'location_type' => $result['geometry']['location_type'] ?? null,
                    'place_name' => $result['name'] ?? null,
                    // Parsed address components
                    'street_number' => $addressComponents['street_number'] ?? null,
                    'route' => $addressComponents['route'] ?? null,
                    'city' => $addressComponents['city'] ?? null,
                    'state' => $addressComponents['state'] ?? null,
                    'state_code' => $addressComponents['state_code'] ?? null,
                    'country' => $addressComponents['country'] ?? null,
                    'country_code' => $addressComponents['country_code'] ?? null,
                    'zip_code' => $addressComponents['zip_code'] ?? null,
                ];

                Log::info('Successfully got coordinates from place_id', [
                    'place_id' => $placeId,
                    'coordinates' => $coordinates
                ]);

                return $coordinates;
            }

            Log::warning('No coordinates found in Google Places API response', [
                'place_id' => $placeId
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Exception during place_id geocoding', [
                'place_id' => $placeId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Parse Google Places address components into structured format
     * 
     * @param array $components
     * @return array
     */
    protected function parseAddressComponents($components)
    {
        $parsed = [];
        
        foreach ($components as $component) {
            $types = $component['types'] ?? [];
            
            if (in_array('street_number', $types)) {
                $parsed['street_number'] = $component['long_name'];
            }
            if (in_array('route', $types)) {
                $parsed['route'] = $component['long_name'];
            }
            if (in_array('locality', $types)) {
                $parsed['city'] = $component['long_name'];
            }
            if (in_array('administrative_area_level_1', $types)) {
                $parsed['state'] = $component['long_name'];
                $parsed['state_code'] = $component['short_name'];
            }
            if (in_array('country', $types)) {
                $parsed['country'] = $component['long_name'];
                $parsed['country_code'] = $component['short_name'];
            }
            if (in_array('postal_code', $types)) {
                $parsed['zip_code'] = $component['long_name'];
            }
        }
        
        return $parsed;
    }
}

