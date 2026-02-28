<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\ContactController;

class TaxonomyController extends Controller
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
        $this->contactController = $contactController;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category_type = request()->get('type');
        if ($category_type == 'product' && ! auth()->user()->can('category.view') && ! auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $can_edit = true;
            if ($category_type == 'product' && ! auth()->user()->can('category.update')) {
                $can_edit = false;
            }

            $can_delete = true;
            if ($category_type == 'product' && ! auth()->user()->can('category.delete')) {
                $can_delete = false;
            }
            $location_id =$this->contactController->getLocationForContact(request());

            $business_id = request()->session()->get('user.business_id');

            // $category = Category::where('business_id', $business_id)
            //                 ->where('category_type', $category_type)
            //                 ->select(['logo','name', 'short_code', 'slug','visibility','description', 'id', 'parent_id']);
            
            $category = Category::where('categories.business_id', $business_id)
                                    ->where('categories.category_type', $category_type)
                                    ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
                                    ->leftJoin('business_locations', 'categories.location_id', '=', 'business_locations.id')
                                    ->select([
                                        'categories.logo',
                                        'categories.name',
                                        'categories.short_code',
                                        'categories.slug',
                                        'categories.visibility',
                                        'categories.description',
                                        'categories.id',
                                        'categories.parent_id',
                                        'parent.name as parent_cat', // <-- this is the parent category name
                                        'business_locations.name as location_name' // <-- this is the location name
                                    ]);
            if($location_id != null){
                $category->where('categories.location_id', $location_id);
            }

            return Datatables::of($category)
                ->editColumn('logo', function ($category) {
                    $logoUrl = $category->logo ? url('uploads/img/' . $category->logo) : url('img/default.png');
                    return '<div style="display: flex;"><img src="' .  $logoUrl. '" alt="Brand logo" class="product-thumbnail-small"></div>';
                })
                ->addColumn(
                    'action', function ($row) use ($can_edit, $can_delete, $category_type) {
                        $html = '';
                        if ($can_edit) {
                            $html .= '<button data-href="'.action([\App\Http\Controllers\TaxonomyController::class, 'edit'], [$row->id]).'?type='.$category_type.'" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-edit"><i class="glyphicon glyphicon-edit"></i>'.__('messages.edit').'</button>';
                        }

                        if ($can_delete) {
                            $html .= '&nbsp;<button data-href="'.action([\App\Http\Controllers\TaxonomyController::class, 'destroy'], [$row->id]).'" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-delete"><i class="glyphicon glyphicon-trash"></i> '.__('messages.delete').'</button>';
                        }

                        return $html;
                    }
                )
                ->editColumn('name', function ($row) {
                    if ($row->parent_id != 0) {
                        return 'S-- '.$row->name;
                    } else {
                        return $row->name;
                    }
                })
                ->editColumn('parent_cat', function ($row)
                 {
                    if ($row->parent_id =! 0) {
                        
                        return $row->parent_cat ?: '';
                    }
                   
                })
                ->addColumn('location', function ($row) {
                    return  ($row->location_name ?? 'N/A');
                })
                ->removeColumn('id')
                ->removeColumn('parent_id')
                ->rawColumns(['logo','action'])
                ->make(true);
        }

        $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

        return view('taxonomy.index')->with(compact('module_category_data', 'module_category_data'));
    }
    private function slugMaker($baseName, $categoryId = null)
    {
        // Convert name to a slug
        $baseName = Str::slug($baseName);
        $counter = 0;
        $newSlug = $baseName;
        while (Category::withTrashed()->where('slug', $newSlug)
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('id', '!=', $categoryId);
            })
            ->exists()
        ) {
            $counter++;
            $newSlug = $baseName . '-' . $counter;
        }
        return $newSlug;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category_type = request()->get('type');
        if ($category_type == 'product' && ! auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $user_location_id =$this->contactController->getLocationForContact(request());
        $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

        $categories = Category::where('business_id', $business_id)
                        ->where('parent_id', 0)
                        ->where('category_type', $category_type)
                        ->when($user_location_id != null, function ($query) use ($user_location_id) {
                            $query->where('location_id', $user_location_id);
                        })
                        ->select(['name', 'short_code', 'id'])
                        ->get();

        $parent_categories = [];
        if (! empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }
        $business_id = request()->session()->get('user.business_id');
        $is_super_admin = auth()->user()->can('access_all_locations') || auth()->user()->can('admin');
        $business_locations = [];
        if ($is_super_admin) {
            $business_locations = BusinessLocation::forDropdown($business_id);
        }

        // Get brands for the dropdown
        $brands = \App\Brands::where('business_id', $business_id)
            ->when($user_location_id != null, function ($query) use ($user_location_id) {
                $query->where('location_id', $user_location_id);
            })
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        return view('taxonomy.create')
                    ->with(compact('parent_categories', 'module_category_data', 'category_type', 'business_locations', 'is_super_admin', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category_type = request()->input('category_type');
        if ($category_type == 'product' && ! auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $input = $request->only(['name','cat_banner','cat_logo','slug','visibility', 'short_code', 'category_type', 'description', 'brand_ids']);
            if (! empty($request->input('add_as_sub_cat')) && $request->input('add_as_sub_cat') == 1 && ! empty($request->input('parent_id'))) {
                $input['parent_id'] = $request->input('parent_id');
            } else {
                $input['parent_id'] = 0;
            }
            $input['business_id'] = $request->session()->get('user.business_id');
            if($request->input('location_id')){
                $input['location_id'] = $request->input('location_id');
            }else{
                $input['location_id'] = $this->contactController->getLocationForContact(request());
            }
            $input['created_by'] = $request->session()->get('user.id');
            if(empty($input['slug'])) {
                $slug = $this->slugMaker($input['name']);
            } else {
                // Validate if the slug is unique
                $slug = $this->slugMaker($input['slug']);
            }
            $input['slug']=$slug;
           
             // Handle file upload if exists
             $fileInputs = [
                 'cat_logo',
                'cat_banner',
                'category_banner',
            ];
            foreach ($fileInputs as $inputName) {
                if ($request->hasFile($inputName)) {
                    Log::info('Upload');
                    $file = $request->file($inputName);
                    
                    // Check if the file is valid
                    if (!$file->isValid()) {
                        return response()->json(['success' => false, 'msg' => __('messages.invalid_file')]);
                    }
                    $timestamp = time();
                    $randomNumber = rand(1000000000, 9999999999);
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = "{$timestamp}_{$randomNumber}_{$originalFileName}";
                    $destinationPath = public_path('uploads/img');
                    
                    // Ensure the upload directory exists
                    if (!File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0775, true);
                    }
        
                    // Move the file to the destination folder
                    $file->move($destinationPath, $fileName);
        
                    // Save the filename in the corresponding model field
                    if ($inputName == 'cat_banner') {
                        $input['banner'] =$fileName; // Save banner file name
                    } elseif ($inputName == 'cat_logo') {
                        $input['logo'] =$fileName; // Save logo file name
                    } elseif ($inputName == 'category_banner') {
                        $input['category_banner'] =$fileName; // Save category banner file name
                    }
                }
            }
            $category = Category::create($input);

            // Handle brand relationships for B2C (many-to-many)
            if (!empty($input['brand_ids'])) {
                $brandIds = is_array($input['brand_ids']) ? $input['brand_ids'] : explode(',', $input['brand_ids']);
                $brandIds = array_filter(array_map('intval', $brandIds)); // Clean and validate IDs
                
                if (!empty($brandIds)) {
                    // Verify brands belong to the same business
                    $validBrandIds = \App\Brands::where('business_id', $input['business_id'])
                        ->whereIn('id', $brandIds)
                        ->pluck('id')
                        ->toArray();
                    
                    if (!empty($validBrandIds)) {
                        $category->brandCategories()->sync($validBrandIds);
                    }
                }
            }

            $output = ['success' => true,
                'data' => $category,
                'msg' => __('category.added_success'),
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
     * Get brands for a specific location (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $location_id
     * @return \Illuminate\Http\Response
     */
    public function getBrandsForLocation(Request $request, $location_id)
    {
        if (! auth()->user()->can('category.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            
            $brands = \App\Brands::where('business_id', $business_id)
                ->where('location_id', $location_id)
                ->select(['id', 'name'])
                ->orderBy('name', 'asc')
                ->get();

            return response()->json($brands);

        } catch (\Exception $e) {
            \Log::error('Error fetching brands for location: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
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
        $category_type = request()->get('type');
        if ($category_type == 'product' && ! auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $category = Category::where('business_id', $business_id)->find($id);

            $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

            $parent_categories = Category::where('business_id', $business_id)
                                        ->when($category->location_id != null, function ($query) use ($category) {  
                                            $query->where('location_id', $category->location_id);
                                        })
                                        ->where('parent_id', 0)
                                        ->where('category_type', $category_type)
                                        ->where('id', '!=', $id)
                                        ->pluck('name', 'id');
            $is_parent = false;

            if ($category->parent_id == 0) {
                $is_parent = true;
                $selected_parent = null;
            } else {
                $selected_parent = $category->parent_id;
            }
            $business_id = request()->session()->get('user.business_id');
            $is_super_admin = auth()->user()->can('access_all_locations') || auth()->user()->can('admin');
            $business_locations = [];
            if ($is_super_admin) {
                $business_locations = BusinessLocation::forDropdown($business_id);
            }

            // Get brands for the dropdown
            $brands = \App\Brands::where('business_id', $business_id)
                ->when($category->location_id != null, function ($query) use ($category) {
                    $query->where('location_id', $category->location_id);
                })
                ->select(['id', 'name'])
                ->orderBy('name', 'asc')
                ->get();

            // Get selected brand IDs for this category
            $selected_brand_ids = $category->brandCategories()->pluck('brands.id')->toArray();

            return view('taxonomy.edit')
                ->with(compact('category', 'parent_categories', 'is_parent', 'selected_parent', 'module_category_data', 'business_locations', 'is_super_admin', 'brands', 'selected_brand_ids'));
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
        if (! auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }
    
        if ($request->ajax()) {
            try {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string|max:500',
                    'cat_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'cat_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'category_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'visibility' => 'nullable|in:public,coming soon,protected',
                    'slug' => 'nullable|string|max:255',
                    'brand_ids' => 'nullable|array',
                    'brand_ids.*' => 'integer|exists:brands,id',
                ]);
                $input = $request->only(['name', 'description', 'visibility', 'slug','parent_id', 'brand_ids']);
                if (! empty($request->input('add_as_sub_cat')) && $request->input('add_as_sub_cat') == 1 && ! empty($request->input('parent_id'))) {
                    $input['parent_id'] = $request->input('parent_id');
                } if (! empty($request->input('add_as_sub_cat')) && $request->input('add_as_sub_cat') == 1 && ! empty($request->input('parent_id'))) {
                    $input['parent_id'] = $request->input('parent_id');
                } else {
                    $input['parent_id'] = 0;
                }
                $business_id = $request->session()->get('user.business_id');
                if($request->input('location_id')){
                    $input['location_id'] = $request->input('location_id');
                }else{
                    $input['location_id'] = $this->contactController->getLocationForContact(request());
                }
                $category = Category::where('business_id', $business_id)->findOrFail($id);
                // Only update slug if explicitly provided, otherwise keep existing slug
                if (!empty($input['slug'])) {
                    // If slug is provided, use it as-is (validate uniqueness)
                    $slug = $this->slugMaker($input['slug'], $id);
                    $input['slug'] = $slug;
                } else {
                    // If slug is empty, keep the existing slug
                    $input['slug'] = $category->slug;
                    // Only generate new slug if category doesn't have one
                    if (empty($category->slug)) {
                        $input['slug'] = $this->slugMaker($input['name'], $id);
                    }
                }
                $category->name = $input['name'];
                $category->description = $input['description'];
                $category->visibility = $input['visibility'];
                $category->slug = $input['slug'];
                $category->parent_id = $input['parent_id'];
                $category->location_id = $input['location_id'];
                if ($this->moduleUtil->isModuleInstalled('Repair')) {
                    $category->use_for_repair = !empty($request->input('use_for_repair')) ? 1 : 0;
                }
                $fileInputs = [
                    'cat_logo',
                    'cat_banner',
                    'category_banner'
                ];
                foreach ($fileInputs as $inputName) {
                    if ($request->hasFile($inputName)) {
                        $file = $request->file($inputName);
                        if (!$file->isValid()) {
                            return response()->json(['success' => false, 'msg' => __('messages.invalid_file')]);
                        }
                        $timestamp = time();
                        $randomNumber = rand(1000000000, 9999999999);
                        $originalFileName = $file->getClientOriginalName();
                        $fileName = "{$timestamp}_{$randomNumber}_{$originalFileName}";
                        $destinationPath = public_path('uploads/img');
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0775, true);
                        }
                        $file->move($destinationPath, $fileName);
                        if ($inputName == 'cat_banner') {
                            $category->banner = $fileName; 
                        } elseif ($inputName == 'cat_logo') {
                            $category->logo = $fileName;
                        } elseif ($inputName == 'category_banner') {
                            $category->category_banner = $fileName;
                        }
                    }
                    
                }
    
                // Save the updated category data
                $category->save();

                // Handle brand relationships for B2C (many-to-many)
                if (isset($input['brand_ids'])) {
                    if (!empty($input['brand_ids'])) {
                        $brandIds = is_array($input['brand_ids']) ? $input['brand_ids'] : explode(',', $input['brand_ids']);
                        $brandIds = array_filter(array_map('intval', $brandIds)); // Clean and validate IDs
                        
                        if (!empty($brandIds)) {
                            // Verify brands belong to the same business
                            $validBrandIds = \App\Brands::where('business_id', $business_id)
                                ->whereIn('id', $brandIds)
                                ->pluck('id')
                                ->toArray();
                            
                            $category->brandCategories()->sync($validBrandIds);
                        }
                    } else {
                        // If brand_ids is empty, remove all relationships
                        $category->brandCategories()->detach();
                    }
                }
    
                // Return success response
                return response()->json(['success' => true, 'msg' => __('category.updated_success')]);
    
            } catch (\Exception $e) {
                // Log the error
                \Log::error('Error updating category: ' . $e->getMessage());
    
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
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $category = Category::where('business_id', $business_id)->findOrFail($id);

                if ($category->category_type == 'product' && ! auth()->user()->can('category.delete')) {
                    abort(403, 'Unauthorized action.');
                }

                $category->delete();

                $output = ['success' => true,
                    'msg' => __('category.deleted_success'),
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

    public function getCategoriesApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $categories = Category::catAndSubCategories($api_settings->business_id);
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($categories);
    }

    /**
     * get taxonomy index page
     * through ajax
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxonomyIndexPage(Request $request)
    {
        if (request()->ajax()) {
            $category_type = $request->get('category_type');
            $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

            return view('taxonomy.ajax_index')
                ->with(compact('module_category_data', 'category_type'));
        }
    }
    public function getCategoriesForLocation($location_id, Request $request)
    {
        $is_perentcategory = false;
        if(!empty($request->get('is_perentcategory'))){
            $is_perentcategory = true;
        }
        $categories = Category::where('location_id', $location_id)
        ->when($is_perentcategory, function ($query) {
            $query->where('parent_id', 0);
        })
        ->select(['name', 'id'])
        ->get();
        return response()->json($categories);
    }
}
