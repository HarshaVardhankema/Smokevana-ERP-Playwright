<?php

namespace App\Http\Controllers;

use App\Business;
use App\ShipStation;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ShipStationController extends Controller
{
    private $apiKey;
    private $apiSecret;
    private $baseUrl;


    public function __construct()
    {
        $this->apiKey = env('SHIPSTATION_API_KEY');
        $this->apiSecret = env('SHIPSTATION_API_SECRET');
        $this->baseUrl = 'https://api.shipstation.com';
    }

    private function normalizeShipEngineCountry($country): string
    {
        $c = strtoupper(trim((string) ($country ?? '')));
        $map = [
            'USA' => 'US',
            'UNITED STATES' => 'US',
            'UNITED STATES OF AMERICA' => 'US',
            'CANADA' => 'CA',
            'CAN' => 'CA',
            'MEXICO' => 'MX',
            'MEX' => 'MX',
        ];
        return $map[$c] ?? (strlen($c) === 2 ? $c : 'US');
    }

    private function normalizeShipEnginePostalCode($postalCode, string $countryCode): string
    {
        $pc = trim((string) ($postalCode ?? ''));
        if ($pc === '') {
            return '';
        }
        $cc = strtoupper($countryCode);
        if ($cc === 'US') {
            $digits = preg_replace('/\D/', '', $pc);
            if ($digits !== '' && strlen($digits) <= 5) {
                return str_pad($digits, 5, '0', STR_PAD_LEFT);
            }
            if (strlen($pc) >= 5) {
                return $pc;
            }
        }
        if ($cc === 'CA') {
            $digits = preg_replace('/\D/', '', $pc);
            if ($digits !== '' && strlen($digits) === 6) {
                return substr($digits, 0, 3) . ' ' . substr($digits, 3, 3);
            }
        }
        return $pc;
    }
    public function index()
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $shipstations = ShipStation::select([
                'id',
                'name',
                'api',
                'priority',
            ])->get();
            return DataTables::of($shipstations)
                ->addColumn('action', function ($row) {
                    $url = action('App\Http\Controllers\ShipStationController@verifyShipStation', [$row->id]);
                    $edit = action('App\Http\Controllers\ShipStationController@edit', [$row->id]);
                    $shipStationServices = action('App\Http\Controllers\ShipStationController@shipStationServices', [$row->id]);
                    $delete = action('App\Http\Controllers\ShipStationController@delete', [$row->id]);
                    return '
                    <a href="#" data-href="' . $shipStationServices . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info view_shipstation_services btn-modal" data-container=".view_modal" ><i class="fas fa-sliders-h"></i> Settings </a>
                    <button href="#" data-href="' . $edit . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info edit_shipstationapi_button" ><i class="fas fa-edit"></i> Edit</button>
                    <button  data-href="' . $url . '" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-success delete_tax_rate_button"><i class="glyphicon glyphicon-check"></i>Verify</button>
                    <button data-href="' . $delete . '" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_shipstation_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>';
                })->editColumn('api', function ($row) {
                    return '***********' . substr($row->api, -10);
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipstation.index');
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        return view('shipstation.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['warehouse', 'value', 'priority', 'contact_name', 'phone', 'company_name', 'address_1', 'city_locality', 'state_province', 'postal_code', 'country_code']);


            if ($request->has('usable')) {
                $isUsable = true;
            } else {
                $isUsable = false;
            }
            $tax_rate = ShipStation::updateOrCreate([
                'name' => $input['warehouse'],
            ], [
                'api' => $input['value'],
                'priority' => $input['priority'],
                'contact_name' => $input['contact_name'],
                'phone' => $input['phone'],
                'company_name' => $input['company_name'],
                'address_1' => $input['address_1'],
                'city_locality' => $input['city_locality'],
                'postal_code' => $input['postal_code'],
                'state_province' => $input['state_province'],
                'country_code' => $input['country_code'],
                'usable' => $isUsable,
                'business_id' => auth()->user()->business_id,
            ]);

            $output = [
                'success' => true,
                'data' => $input,
                'msg' => 'Success',
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function edit($id)
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Find the existing record by ID
            $tax_rate = ShipStation::findOrFail($id);

            // Return the edit view with the record data
            return view('shipstation.edit', compact('tax_rate'));
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            return redirect()->route('shipstation.index')->with('error', __('messages.something_went_wrong'));
        }
    }
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['warehouse', 'api', 'priority', 'contact_name', 'phone', 'company_name', 'address_1', 'city_locality', 'state_province', 'postal_code', 'country_code']);

            $tax_rate = ShipStation::findOrFail($id); // Find the existing record by ID
            if ($request->has('usable')) {
                $isUsable = true;
            } else {
                $isUsable = false;
            }
            // Update the existing record
            $tax_rate->update([
                'name' => $input['warehouse'],
                'api' => $input['api'],
                'priority' => $input['priority'],
                'contact_name' => $input['contact_name'],
                'phone' => $input['phone'],
                'company_name' => $input['company_name'],
                'address_1' => $input['address_1'],
                'city_locality' => $input['city_locality'],
                'postal_code' => $input['postal_code'],
                'state_province' => $input['state_province'],
                'country_code' => $input['country_code'],
                'usable' => $isUsable,
                'business_id' => auth()->user()->business_id,
            ]);

            $output = [
                'success' => true,
                'data' => $tax_rate,
                'msg' => 'Record updated successfully.',
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function delete($id)
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $tax_rate = ShipStation::findOrFail($id); // Find the record by ID
            $tax_rate->delete();
            $output = [
                'success' => true,
                'msg' => 'Record deleted successfully.',
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    public function verifyShipStation(Request $request, $id)
    {
        $shipStation = ShipStation::find($id);
        $api = $shipStation->api;
        $response = Http::withHeaders([
            'API-Key' => $api,
            'Content-Type' => 'application/json'
        ])->get("{$this->baseUrl}/v2/carriers");

        $data =  $response->json();
        if (!empty($data['carriers'])) {
            return  [
                'success' => true,
                'msg' => 'Connection Test Successful',
            ];
        }
        return  [
            'success' => false,
            'msg' => 'Connection Test Failed',
        ];
    }
    public function shipStationServices(Request $request, $id)
{
    $shipStation = ShipStation::find($id);
    $api = $shipStation->api;

    $response = Http::withHeaders([
        'API-Key' => $api,
        'Content-Type' => 'application/json'
    ])->get("{$this->baseUrl}/v2/carriers");

    $responseData = $response->json();

    // Ensure the 'carriers' key exists; otherwise, use an empty array
    $carriers = $responseData['carriers'] ?? [];

    // Decode serviceList if it's stored as JSON
    $shipmentList = json_decode($shipStation->serviceList, true) ?? [];

    return view('shipstation.view', [
        'carriers' => $carriers,
        'shpstation' => $shipStation,
        'shipmentList' => $shipmentList
    ]);
}

public function storeServices(Request $request)
{
    $shipStation = ShipStation::find($request->input('id'));

    // Get selected carriers, services, and packages
    $selectedCarriers = $request->input('carriers', []);
    $selectedServices = $request->input('carrier_services', []);
    $selectedPackages = $request->input('carrier_packages', []);

    // Get all carriers from ShipStation API to match data
    $response = Http::withHeaders([
        'API-Key' => $shipStation->api,
        'Content-Type' => 'application/json'
    ])->get("{$this->baseUrl}/v2/carriers");

    $allCarriers = collect($response->json()['carriers'] ?? []);

    // Filter only the selected carriers
    $savedCarriers = $allCarriers
        ->whereIn('carrier_id', $selectedCarriers)
        ->map(function ($carrier) use ($selectedServices, $selectedPackages) {
            return [
                'carrier_id' => $carrier['carrier_id'],
                'carrier_code' => $carrier['carrier_code'],
                'friendly_name' => $carrier['friendly_name'],
                'services' => collect($carrier['services'])
                    ->whereIn('service_code', $selectedServices[$carrier['carrier_id']] ?? [])
                    ->map(fn($service) => [
                        'carrier_id' => $service['carrier_id'],
                        'carrier_code' => $service['carrier_code'],
                        'service_code' => $service['service_code'],
                        'name' => $service['name'],
                    ])
                    ->values()
                    ->toArray(),
                'packages' => collect($carrier['packages'])
                    ->whereIn('package_code', $selectedPackages[$carrier['carrier_id']] ?? [])
                    ->map(fn($package) => [
                        'package_id' => $package['package_id'],
                        'package_code' => $package['package_code'],
                        'name' => $package['name'],
                        'description' => $package['description'] ?? null,
                    ])
                    ->values()
                    ->toArray(),
            ];
        })
        ->values()
        ->toArray();

    // Save structured data as JSON
    $shipStation->serviceList = json_encode($savedCarriers);
    $shipStation->save();

    return response()->json([
        'success' => true,
        'msg' => 'Services and packages saved successfully!',
    ]);
}

    public function cleanAddress(Request $request)
    {
        $response = Http::withHeaders([
            'API-Key' => 'reoooovm94svvw23atbhrmkuyyo98rj7be',
            'Content-Type' => 'application/json'
        ])
            ->post("{$this->baseUrl}/v2/labels", [
                "validate_address" => "validate_and_clean",
                "shipment" => [
                    "carrier_id" => "se-28529731",
                    "service_code" => "usps_priority_mail",
                    "ship_date" => "2024-09-23T00:00:00.000Z",
                    "ship_to" => $request->input('ship_to'),
                    "ship_from" => $request->input('ship_from'),
                    "warehouse_id" => "se-28529731",
                    "is_return" => false,
                    "confirmation" => "none",
                    "insurance_provider" => "none",
                    "packages" => $request->input('packages')
                ],
                "label_download_type" => "url",
                "label_format" => "pdf",
                "display_scheme" => "label",
                "label_layout" => "4x6"
            ]);

        return response()->json($response->json(), $response->status());
    }

    public function rateShopping() {}

    // popup 
    public function getEstRate(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'warehouse_id' => 'required|string',
            'from_country_code' => 'required|string',
            'from_postal_code' => 'required|string',
            'from_city_locality' => 'required|string',
            'from_state_province' => 'required|string',
            'to_country_code' => 'required|string',
            'to_postal_code' => 'required|string',
            'to_city_locality' => 'required|string',
            'to_state_province' => 'required|string',
            'weight' => 'required|array',
            'weight.value' => 'required|numeric',
            'weight.unit' => 'required|string',
            'packages' => 'required|array',
            'packages.*.package_type' => 'required|string',
            'packages.*.dimensions' => 'required|array',
            'packages.*.dimensions.unit' => 'required|string',
            'packages.*.dimensions.length' => 'required|numeric',
            'packages.*.dimensions.width' => 'required|numeric',
            'packages.*.dimensions.height' => 'required|numeric',
            'packages.*.insuranceProvider' => 'nullable|string',
            'packages.*.insured_value' => 'nullable|array',
            'packages.*.insured_value.currency' => 'nullable|string',
            'packages.*.insured_value.value' => 'nullable|numeric',
            'packages.*.label_messages' => 'nullable|array',
            'packages.*.content_description' => 'nullable|string',
        ]);
        $addressFields = [
            'from_country_code', 'from_postal_code', 'from_city_locality', 'from_state_province',
            'to_country_code', 'to_postal_code', 'to_city_locality', 'to_state_province'
        ];

        foreach ($addressFields as $field) {
            if (empty(trim($request->input($field)))) {
                return response()->json([
                    'status' => false,
                    'message' => "Missing or invalid value for '{$field}'"
                ], 422);
            }
}
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }
        $warehouse = ShipStation::where('id', $request->input('warehouse_id'))->first();

        if (!$warehouse) {
            return response()->json(['status' => false, 'message' => 'Warehouse not found']);
        } 

        if(empty($warehouse->serviceList)){
            return response()->json(['status' => false, 'message' => 'Services not found']);
        }

        $fromCountry = $this->normalizeShipEngineCountry($request->input('from_country_code'));
        $toCountry = $this->normalizeShipEngineCountry($request->input('to_country_code'));
        $fromPostal = $this->normalizeShipEnginePostalCode($request->input('from_postal_code'), $fromCountry);
        $toPostal = $this->normalizeShipEnginePostalCode($request->input('to_postal_code'), $toCountry);
        
        $services = json_decode($warehouse->serviceList, true);
        $response = [];
        foreach ($services as $service) {
            $payload = [
                'carrier_id' => $service['carrier_id'],
                'from_country_code' => $fromCountry,
                'from_postal_code' => $fromPostal,
                'from_city_locality' => trim((string) ($request->input('from_city_locality') ?? '')),
                'from_state_province' => trim((string) ($request->input('from_state_province') ?? '')),
                'to_country_code' => $toCountry,
                'to_postal_code' => $toPostal,
                'to_city_locality' => trim((string) ($request->input('to_city_locality') ?? '')),
                'to_state_province' => trim((string) ($request->input('to_state_province') ?? '')),
                'weight' => $request->input('weight'),
                'packages' => $request->input('packages'),
            ];
           
        try {
            $carrierResponse = Http::withHeaders([
                'API-Key' => $warehouse->api,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/v2/rates/estimate", $payload);

            if ($carrierResponse->successful()) {
                $response = array_merge($response, $carrierResponse->json());
            } else {
                // Handle error with message from carrier API
                $errorBody = $carrierResponse->json();
                return response()->json([
                    'status' => false,
                    'message' => $errorBody['message'] ?? 'Error from shipping carrier',
                    'details' => $errorBody
                ], $carrierResponse->status());
            }
        } catch (\Throwable $e) {
            // Optional: Log the error for debugging
            // \Log::error('Rate estimation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while contacting the shipping carrier.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    return response()->json([
        'status' => true,
        'data' => $response
    ]);
    }


    public function createShipmentAndLable(Request $request,$newInvoice=null)
    {
        $validate = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:ship_stations,id',
            'shipment' => 'required',
            'shipment.service_type' => 'required|string',
            'shipment.service_code' => 'required|string',
            'shipment.carrier_code' => 'required|string',
            'shipment.carrier_id' => 'required|string',
            'shipment.validate_address' => 'nullable|string|in:validate_and_clean,validate_only',
    
            'shipment.ship_to' => 'required',
            'shipment.ship_to.name' => 'required|string|max:255',
            'shipment.ship_to.phone' => 'required|string|min:10|max:10',
            'shipment.ship_to.company_name' => 'nullable|string|max:255',
            'shipment.ship_to.address_line1' => 'required|string|max:255',
            'shipment.ship_to.city_locality' => 'required|string|max:100',
            'shipment.ship_to.state_province' => 'required|string|max:100',
            'shipment.ship_to.postal_code' => 'required|string|max:20',
            'shipment.ship_to.country_code' => 'required|string|size:2',
            'shipment.ship_to.address_residential_indicator' => 'nullable|string|in:yes,no',
    
            'shipment.ship_from' => 'required',
            'shipment.ship_from.name' => 'required|string|max:255',
            'shipment.ship_from.phone' => 'required|string|min:10|max:10',
            'shipment.ship_from.company_name' => 'nullable|string|max:255',
            'shipment.ship_from.address_line1' => 'required|string|max:255',
            'shipment.ship_from.city_locality' => 'required|string|max:100',
            'shipment.ship_from.state_province' => 'required|string|max:100',
            'shipment.ship_from.postal_code' => 'required|string|max:20',
            'shipment.ship_from.country_code' => 'required|string|size:2',
            'shipment.ship_from.address_residential_indicator' => 'nullable|string|in:yes,no',
    
            'shipment.packages' => 'required|min:1',
            'shipment.packages.*.package_type' => 'required|string',
            'shipment.packages.*.dimensions' => 'required',
            'shipment.packages.*.dimensions.unit' => 'required|string|in:inch,cm',
            'shipment.packages.*.dimensions.length' => 'required|numeric|min:1',
            'shipment.packages.*.dimensions.width' => 'required|numeric|min:1',
            'shipment.packages.*.dimensions.height' => 'required|numeric|min:1',
    
            'shipment.packages.*.weight' => 'required',
            'shipment.packages.*.weight.value' => 'required|numeric|min:1',
            'shipment.packages.*.weight.unit' => 'required|string|in:gram,kg,oz,lb',
    
            'shipment.packages.*.insuranceProviderId' => 'nullable|integer',
            'shipment.packages.*.insuranceProvider' => 'nullable|string',
            'shipment.packages.*.insured_value' => 'nullable',
            'shipment.packages.*.insured_value.currency' => 'nullable|string|size:3',
            'shipment.packages.*.insured_value.value' => 'nullable|numeric|min:0',
    
            'shipment.packages.*.label_messages' => 'nullable',
            'shipment.packages.*.label_messages.reference1' => 'nullable|string|max:255',
    
            'shipment.packages.*.content_description' => 'nullable|string|max:500',
        ]);
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }
        $shipmentData = $request->all();
        $shipmentData['shipment']['packages'][0]['label_messages']['reference1'] = $newInvoice??(time());
        $warehouse = ShipStation::where('id',  $request->input('warehouse_id'))->first();
        $response = Http::withHeaders([
            'API-Key' => $warehouse->api,
            'Content-Type' => 'application/json'
        ])->post("{$this->baseUrl}/v2/labels", $shipmentData);
        return $response->body();
    }
    
    public function voidShipment(Request $request,$id){

        $sale = Transaction::find($id);
        
        $invoiceID= $sale->invoice_no;
        $warehouseId = $sale->shipment['shipment_details']['warehouse_id'];
        $shipmentID = $sale->shipment['shipment_details']['shipment_id'];
        $warehouse = ShipStation::where('id',  $warehouseId)->first();
        $response = Http::withHeaders([
            'API-Key' => $warehouse->api,
            'Content-Type' => 'application/json'
        ])->put("{$this->baseUrl}/v2/labels/{$shipmentID}/void");
        $responseData = $response->json();
        if (isset($responseData['approved']) && $responseData['approved'] === true) {
            if ($sale) {
                $sale->shipment = null;
                $sale->shipping_status = null;
                $sale->shipping_details = null;
                $sale->save();
                return response()->json([
                    'status' => true,
                    'message' => "Shipment with id $shipmentID has been voided: " . $responseData['message']
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Sale $invoiceID Can not void."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => "Failed to void Shipment with id $shipmentID: " . ($responseData['message'] ?? 'Unknown error')
            ]);
        }
        
    }

    public function listOfShipment(Request $request){
        $warehouses = ShipStation::where('usable',1)->get();
        $responseData =[];
        foreach($warehouses as $warehouse){
            $response = Http::withHeaders([
                'API-Key' => $warehouse->api,
                'Content-Type' => 'application/json'
            ])->get("{$this->baseUrl}/v2/labels");
            $responseData = $response->json();
            if (isset($responseData['labels'])) {
                $responseData = array_merge($responseData[], $response->json());
            }
        }
        return response()->json([
            'status' => true,
            'data' => $responseData
        ]);
    }
}
