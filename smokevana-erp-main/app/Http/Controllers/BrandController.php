<?php

namespace App\Http\Controllers;

use App\Media;
use App\Brands;
use App\Product;
use App\BrandConfig;
use App\BusinessLocation;
use App\Category;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\ContactController;

class BrandController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;
    protected $businessUtil;
    protected $contactController;
    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, BusinessUtil $businessUtil, ContactController $contactController)
    {
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->contactController = $contactController;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('brand.view') && ! auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $location_id =$this->contactController->getLocationForContact(request());

            $brands = Brands::where('business_id', $business_id)
                        ->select(['logo','name','slug','visibility', 'description','brand_url', 'id', 'location_id'])
                        ->when($location_id != null, function ($query) use ($location_id) {
                            $query->where('location_id', $location_id);
                        })
                        ->when(request()->has('visibility') && request()->visibility !== '' && request()->visibility !== 'all', function ($query) {
                            $query->where('visibility', request()->visibility);
                        });

            $datatables = Datatables::of($brands)
                ->editColumn('logo', function ($brands) {
                    $logoUrl = $brands->logo ? url('uploads/img/' . $brands->logo) : url('img/default.png');
                    $brandUrl = $brands->brand_url ? $brands->brand_url : '#';
                    return '<div style="display: flex;"><a href="' . $brandUrl . '" target="_blank"><img src="' . $logoUrl . '" alt="Brand logo" class="product-thumbnail-small"></a></div>';
                })
                ->editColumn('description', function($brands){
                    $note = $brands->description;
                    return Str::limit($note, 30);
                    
                })
               
                
                ->addColumn(
                    'action',
                    function($brand) {
                        // Check if brand's location is B2C
                        $is_b2c = false;
                        if ($brand->location_id) {
                            $is_b2c = BusinessLocation::where('id', $brand->location_id)->value('is_b2c');
                        }
                        
                        $action = '';
                        if (auth()->user()->can('brand.update')) {
                            $action .= '<button data-href="'.action('App\Http\Controllers\BrandController@edit', [$brand->id]).'" class="btn btn-brand-action btn-brand-edit edit_brand_button"><i class="fas fa-pencil-alt"></i> '.__("messages.edit").'</button> ';
                        }
                        
                        if (auth()->user()->can('brand.delete')) {
                            $action .= '<button data-href="'.action('App\Http\Controllers\BrandController@destroy', [$brand->id]).'" class="btn btn-brand-action btn-brand-delete delete_brand_button"><i class="fas fa-trash-alt"></i> '.__("messages.delete").'</button> ';
                        }
                        
                        if (auth()->user()->can('brand.view')) {
                            $action .= '<button data-href="'.action('App\Http\Controllers\BrandController@show', [$brand->id]).'" class="btn btn-brand-action btn-brand-view view_brand_button"><i class="fas fa-eye"></i> '.__("messages.view").'</button> ';
                        }
                        
                        // Show settings icon only if brand's location is B2C
                        if ($is_b2c) {
                            $action .= '<a href="'.action('App\Http\Controllers\BrandController@config', [$brand->id]).'" class="btn btn-brand-action btn-brand-settings"><i class="fas fa-cog"></i> '.__("brand.settings").'</a>';
                        }
                        
                        return $action;
                    }

                )
                ->removeColumn('id','brand_url','location_id');
                
            // Set columns that should not be escaped
            $datatables->escapeColumns([]);
            
            return $datatables->make(false);
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $this->contactController->getLocationForContact(request());
        $brands_query = Brands::where('business_id', $business_id)
            ->when($location_id != null, function ($query) use ($location_id) {
                $query->where('location_id', $location_id);
            });
        $total_brands = (clone $brands_query)->count();
        $public_brands = (clone $brands_query)->where('visibility', 'public')->count();
        $brand_ids = (clone $brands_query)->pluck('id');
        $products_assigned = Product::whereIn('brand_id', $brand_ids)->count();
        $top_brand_products = $brand_ids->isEmpty() ? 0 : (int) Product::whereIn('brand_id', $brand_ids)
            ->selectRaw('brand_id, count(*) as c')
            ->groupBy('brand_id')
            ->get()
            ->max('c');

        return view('brand.index', compact('total_brands', 'public_brands', 'products_assigned', 'top_brand_products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }
        
        $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

        $business_id = request()->session()->get('user.business_id');
        $location_id =$this->contactController->getLocationForContact(request());
        $categories = Category::where('business_id', $business_id)
                        ->when($location_id != null, function ($query) use ($location_id) {
                            $query->where('location_id', $location_id)
                            ->where('parent_id', 0);
                        })
                        ->select(['name', 'id'])
                        ->get();
              
        $business_locations = [];
        $is_super_admin = false;
        if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
            $business_locations = BusinessLocation::forDropdown($business_id);
            $is_super_admin = true;
        }
        $brandCategory = $categories->pluck('name','id');
        return view('brand.create')
                ->with(compact('quick_add', 'is_repair_installed','brandCategory','business_locations','is_super_admin'));
    }
    private function slugMaker($baseName, $locationId, $brandId = null)
    {
        try {
            // Convert name to a slug
            $slugifiedName = Str::slug($baseName);
            $counter = 0;
            $newSlug = $slugifiedName;
            
            // Check if a brand with the same name already exists in this location
            $existingBrandInLocation = Brands::withTrashed()
                ->where('location_id', $locationId)
                ->where('name', $baseName) // Compare with original name, not slugified
                ->when($brandId, function ($query, $brandId) {
                    $query->where('id', '!=', $brandId);
                })
                ->first();
            
            // If brand name exists in this location, make slug unique within this location only
            if ($existingBrandInLocation) {
                while (Brands::withTrashed()
                    ->where('location_id', $locationId)
                    ->where('slug', $newSlug)
                    ->when($brandId, function ($query, $brandId) {
                        $query->where('id', '!=', $brandId);
                    })
                    ->exists()
                ) {
                    $counter++;
                    $newSlug = $slugifiedName . '-' . $counter;
                }
            }
            // If brand name doesn't exist in this location, the slug can be the same as other locations
            
            return $newSlug;
        } catch (\Exception $e) {
            \Log::error('Error in slugMaker: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
     public function store(Request $request)
    {
        if (! auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'brand_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category' => 'nullable|exists:categories,id',
                'visibility' => 'nullable|in:public,coming soon,protected',
                'slug' => 'nullable|string|max:255',
                'brand_url' => 'nullable|url|max:500',
            ]);


            $input = $request->only(['name', 'description','brand_logo','category','visibility','brand_banner','slug','brand_url']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');

            if ($this->moduleUtil->isModuleInstalled('Repair')) {
                $input['use_for_repair'] = ! empty($request->input('use_for_repair')) ? 1 : 0;
            }
            if(!empty($request->input('location_id'))){
                $input['location_id'] = $request->input('location_id');
            }else{
                $input['location_id'] = $this->contactController->getLocationForContact(request());
            }
            
            // Ensure location_id is not null
            if (empty($input['location_id'])) {
                throw new \Exception('Location ID is required');
            }
            
            if (empty($input['slug'])) {
                $slug = $this->slugMaker($input['name'], $input['location_id']);
            } else {
                // Validate if the slug is unique
                $slug = $this->slugMaker($input['slug'], $input['location_id']);
            }
            $input['slug']=$slug;
            
            $logo_name = $this->businessUtil->uploadFile($request, 'brand_logo', 'img', 'image');
            if (! empty($logo_name)) {
                $input['logo'] = $logo_name;
            }
            $banner_name = $this->businessUtil->uploadFile($request, 'brand_banner', 'img', 'image');
            if (! empty($banner_name)) {
                $input['banner'] = $banner_name;
            }
            $brand = Brands::create($input);
            $output = ['success' => true,
                'data' => $brand,
                'msg' => __('brand.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('brand.view')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()){
            $business_id = request()->session()->get('user.business_id');
            $brand = Brands::where('business_id', $business_id)->find($id);      
    
            return view('brand.preview')->with(compact('brand'));

        }
    
        
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('brand.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $brand = Brands::where('business_id', $business_id)->find($id);

            $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

        $business_id = request()->session()->get('user.business_id');
        $location_id =$brand->location_id;
        $categories = Category::where('business_id', $business_id)
                        ->when($location_id != null, function ($query) use ($location_id) {
                            $query->where('location_id', $location_id)
                            ->where('parent_id', 0);
                        })
                        ->select(['name', 'id'])
                        ->get();
              
        $business_locations = [];
        $is_super_admin = false;
        if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
            $business_locations = BusinessLocation::forDropdown($business_id);
            $is_super_admin = true;
        }
        $brandCategory = $categories->pluck('name','id');
            return view('brand.edit')
                ->with(compact('brand', 'is_repair_installed','brandCategory','business_locations','is_super_admin'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('brand.update')) {
            abort(403, 'Unauthorized action.');
        }
    
        if ($request->ajax()) {
            try {
                // Validate the incoming request
                $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string|max:500',
                    'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'brand_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'category' => 'nullable|exists:categories,id',
                    'visibility' => 'nullable|in:public,coming soon,protected',
                    'slug' => 'nullable|string|max:255',
                    'brand_url' => 'nullable|url|max:500',
                ]);
    
                $input = $request->only(['name', 'description', 'category','visibility','slug','brand_url']);
                $business_id = $request->session()->get('user.business_id');
    
                $brand = Brands::where('business_id', $business_id)->findOrFail($id);
                
                // Get location_id for slug generation
                $locationId = $request->input('location_id') ?: $brand->location_id;
                
                // Only update slug if explicitly provided, otherwise keep existing slug
                if (!empty($input['slug'])) {
                    // If slug is provided, use it as-is (validate uniqueness)
                    $slug = $this->slugMaker($input['slug'], $locationId, $id);
                    $input['slug'] = $slug;
                } else {
                    // If slug is empty, keep the existing slug
                    $input['slug'] = $brand->slug;
                    // Only generate new slug if brand doesn't have one
                    if (empty($brand->slug)) {
                        $input['slug'] = $this->slugMaker($input['name'], $locationId, $id);
                    }
                }
                
                $brand->name = $input['name'];
                $brand->description = $input['description'];
                $brand->category = $input['category']; 
                $brand->visibility = $input['visibility'];
                $brand->slug = $input['slug'];
                $brand->brand_url = $input['brand_url'];
                if($request->input('location_id')){
                    $brand->location_id = $request->input('location_id');
                }else{
                    $brand->location_id = $this->contactController->getLocationForContact(request());
                }
                // Check if the 'Repair' module is installed and update use_for_repair field
                if ($this->moduleUtil->isModuleInstalled('Repair')) {
                    $brand->use_for_repair = !empty($request->input('use_for_repair')) ? 1 : 0;
                }
    
                // Handle file upload if exists
                $fileInputs = [
                    'brand_banner',
                    'brand_logo'
                ];

            
                // Loop through each file input
                foreach ($fileInputs as $inputName) {
                    if ($request->hasFile($inputName)) {
                        $file = $request->file($inputName);
                        
                        // Check if the file is valid
                        if (!$file->isValid()) {
                            return response()->json(['success' => false, 'msg' => __('messages.invalid_file')]);
                        }
            
                        // Generate a unique file name using timestamp and random number
                        $timestamp = time();
                        $randomNumber = rand(1000000000, 9999999999);
                        $originalFileName = $file->getClientOriginalName();
                        $fileName = "{$timestamp}_{$randomNumber}_{$originalFileName}";
            
                        // Define the destination path
                        $destinationPath = public_path('uploads/img');
                        
                        // Ensure the upload directory exists
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0775, true);
                        }
            
                        // Move the file to the destination folder
                        $file->move($destinationPath, $fileName);
            
                        // Save the filename in the corresponding model field
                        if ($inputName == 'brand_banner') {
                            $brand->banner = $fileName; // Save banner file name
                        } elseif ($inputName == 'brand_logo') {
                            $brand->logo = $fileName; // Save logo file name
                        }
                    }
                }
                // Save the brand data
                $brand->save();
    
                // Return success response
                return response()->json(['success' => true, 'msg' => __('brand.updated_success')]);
    
            } catch (\Exception $e) {
                // Log the error
                \Log::error('Error updating brand: ' . $e->getMessage());
                
                // Return failure response
                return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
            }
        }
    }
    


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('brand.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $brand = Brands::where('business_id', $business_id)->findOrFail($id);
                $brand->delete();

                $output = ['success' => true,
                    'msg' => __('brand.deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function getBrandsApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $brands = Brands::where('business_id', $api_settings->business_id)
                                ->get();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($brands);
    }

    /**
     * Show brand configuration page
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function config($id)
    {
        if (! auth()->user()->can('brand.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $this->contactController->getLocationForContact(request());
        
        $brand = Brands::where('business_id', $business_id)->findOrFail($id);
        
        // Get existing brand config or create new instance
        $brandConfig = BrandConfig::where('brand_id', $id)->first();
        
        if (!$brandConfig) {
            $brandConfig = new BrandConfig();
            $brandConfig->brand_id = $id;
        }

        // Get all notification template types
        $notificationTemplates = BrandConfig::brandNotificationTemplates();

        // Parse existing template settings
        $existingTemplates = [];
        if ($brandConfig->template_settings && is_array($brandConfig->template_settings)) {
            foreach ($brandConfig->template_settings as $template) {
                if (isset($template['template_type'])) {
                    $existingTemplates[$template['template_type']] = [
                        'subject' => $template['subject'] ?? '',
                        'template_body' => $template['template_body'] ?? '',
                        'cc' => $template['cc'] ?? '',
                        'bcc' => $template['bcc'] ?? '',
                    ];
                }
            }
        }

        return view('brand.config')->with(compact('brand', 'brandConfig', 'notificationTemplates', 'existingTemplates'));
    }

    /**
     * Save brand configuration
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saveConfig(Request $request, $id)
    {
        if (! auth()->user()->can('brand.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'mail_host' => 'nullable|string|max:255',
                'mail_port' => 'nullable|integer',
                'mail_username' => 'nullable|string|max:255',
                'mail_password' => 'nullable|string|max:255',
                'mail_from_address' => 'nullable|email|max:255',
                'mail_from_name' => 'nullable|string|max:255',
            ]);

            $business_id = $request->session()->get('user.business_id');
            $brand = Brands::where('business_id', $business_id)->findOrFail($id);

            // Get existing brand config to preserve password if not changed
            $existingConfig = BrandConfig::where('brand_id', $id)->first();
            
            // Prepare email settings array
            $emailSettings = [
                'mail_host' => $request->input('mail_host'),
                'mail_port' => $request->input('mail_port'),
                'mail_username' => $request->input('mail_username'),
                'mail_password' => !empty($request->input('mail_password')) 
                    ? $request->input('mail_password') 
                    : (!empty($existingConfig->email_settings['mail_password']) ? $existingConfig->email_settings['mail_password'] : ''),
                'mail_from_address' => $request->input('mail_from_address'),
                'mail_from_name' => $request->input('mail_from_name'),
            ];

            // Get all template types and prepare template settings
            $notificationTemplates = BrandConfig::brandNotificationTemplates();
            $templateSettings = [];
            $templateData = $request->input('template_data', []);
            
            foreach ($notificationTemplates as $templateType => $templateInfo) {
                $templateSettings[] = [
                    'template_type' => $templateType,
                    'subject' => isset($templateData[$templateType]['subject']) ? $templateData[$templateType]['subject'] : '',
                    'template_body' => isset($templateData[$templateType]['template_body']) ? $templateData[$templateType]['template_body'] : '',
                    'cc' => isset($templateData[$templateType]['cc']) ? $templateData[$templateType]['cc'] : '',
                    'bcc' => isset($templateData[$templateType]['bcc']) ? $templateData[$templateType]['bcc'] : '',
                ];
            }

            // Update or create brand config
            BrandConfig::updateOrCreate(
                ['brand_id' => $id],
                [
                    'email_settings' => $emailSettings,
                    'template_settings' => $templateSettings,
                ]
            );

            $output = [
                'success' => true,
                'msg' => __('brand.config_updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Test brand email configuration
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function testEmailConfig(Request $request, $id)
    {
        if (! auth()->user()->can('brand.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'test_email' => 'required|email',
            ]);

            $business_id = $request->session()->get('user.business_id');
            $brand = Brands::where('business_id', $business_id)->findOrFail($id);

            $notificationUtil = new \App\Utils\NotificationUtil();
            $result = $notificationUtil->testBrandEmailConfiguration($id, $request->test_email);

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}
