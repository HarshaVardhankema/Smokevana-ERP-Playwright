<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\CoaCategory;
use App\CoaList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoaController extends Controller
{
    /**
     * Ensure only Admin users can access certain actions.
     *
     * @return void
     */
    protected function ensureAdmin()
    {
        $user = auth()->user();
        $business_id = session('business.id');

        if (
            empty($user) ||
            empty($business_id) ||
            ! $user->hasRole('Admin#' . $business_id)
        ) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display a listing of COA categories with their lists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $business_id = session('business.id');
        $location_id = $request->get('location_id');
        $brand_id = $request->get('brand_id');
        $search = $request->get('search');

        $query = CoaCategory::with(['lists', 'location', 'brand'])
            ->where('business_id', $business_id);

        if (! empty($location_id)) {
            $query->where('location_id', $location_id);
        }

        if (! empty($brand_id)) {
            $query->where('brand_id', $brand_id);
        }

        // Add search filter for category name
        if (! empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        // Show categories in creation order so the first created is 1, then 2, 3, ...
        $categories = $query->orderBy('created_at', 'asc')->get();

        // Get all lists grouped by category for the Lists tab
        $listsQuery = CoaList::with(['category.location', 'category.brand'])
            ->whereHas('category', function($q) use ($business_id, $location_id, $brand_id, $search) {
                $q->where('business_id', $business_id);
                if (!empty($location_id)) {
                    $q->where('location_id', $location_id);
                }
                if (!empty($brand_id)) {
                    $q->where('brand_id', $brand_id);
                }
                if (!empty($search)) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                }
            })
            ->join('coa_categories', 'coa_lists.coa_category_id', '=', 'coa_categories.id')
            ->select('coa_lists.*')
            ->orderBy('coa_categories.name')
            ->orderBy('coa_lists.name');

        $allLists = $listsQuery->get();

        $locationQuery = BusinessLocation::where('business_id', $business_id)
            ->orderBy('name');
        
        $locations = $locationQuery->get()->mapWithKeys(function ($location) {
            $name = $location->name;
            // Check if name already contains B2B or B2C
            $hasB2B = stripos($name, 'B2B') !== false;
            $hasB2C = stripos($name, 'B2C') !== false;
            
            if (!$hasB2B && !$hasB2C) {
                // Only append suffix if not already present
                $suffix = (!empty($location->is_b2c) && $location->is_b2c == 1) ? ' B2C' : ' B2B';
                $name = $name . $suffix;
            }
            
            return [$location->id => $name];
        });

        $brands = Brands::orderBy('name')->pluck('name', 'id');

        $activeTab = $request->get('tab', 'category'); // Default to 'category' tab

        return view('coa.index', compact(
            'categories',
            'allLists',
            'locations',
            'brands',
            'location_id',
            'brand_id',
            'activeTab',
            'search'
        ));
    }

    /**
     * Show the form for creating a new COA.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->ensureAdmin();

        $business_id = session('business.id');

        // Get all existing categories for dropdown
        $existingCategories = CoaCategory::where('business_id', $business_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $locationQuery = BusinessLocation::where('business_id', $business_id)
            ->orderBy('name');
        
        $locations = $locationQuery->get()->mapWithKeys(function ($location) {
            $name = $location->name;
            // Check if name already contains B2B or B2C
            $hasB2B = stripos($name, 'B2B') !== false;
            $hasB2C = stripos($name, 'B2C') !== false;
            
            if (!$hasB2B && !$hasB2C) {
                // Only append suffix if not already present
                $suffix = (!empty($location->is_b2c) && $location->is_b2c == 1) ? ' B2C' : ' B2B';
                $name = $name . $suffix;
            }
            
            return [$location->id => $name];
        });

        $brands = Brands::orderBy('name')->pluck('name', 'id');

        return view('coa.create', compact('locations', 'brands', 'existingCategories'));
    }

    /**
     * Store a newly created COA category and its lists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $business_id = session('business.id');
        $user_id = auth()->id();

        $validated = $request->validate([
            'category_id' => 'nullable|integer|exists:coa_categories,id',
            'category_name' => 'required|string|max:255',
            'location_id' => 'required|integer|exists:business_locations,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'lists' => 'required|array|min:1',
            'lists.*.name' => 'required|string|max:255',
            'lists.*.link' => 'required|string|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Determine category: use existing category or create new one
            if (!empty($validated['category_id'])) {
                // Using existing category
                $category = CoaCategory::findOrFail($validated['category_id']);
            } else {
                // Check if category with this name already exists
                $existingCategory = CoaCategory::where('business_id', $business_id)
                    ->where('name', $validated['category_name'])
                    ->first();
                
                if ($existingCategory) {
                    $category = $existingCategory;
                } else {
                    // Creating a new category
                    $category = CoaCategory::create([
                        'business_id' => $business_id,
                        'location_id' => $validated['location_id'],
                        'brand_id' => $validated['brand_id'] ?? null,
                        'name' => $validated['category_name'],
                        'created_by' => $user_id,
                    ]);
                }
            }

            foreach ($validated['lists'] as $list) {
                CoaList::create([
                    'coa_category_id' => $category->id,
                    'name' => $list['name'],
                    'link' => $list['link'],
                    'created_by' => $user_id,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('coa.index')
                ->with('status', [
                    'success' => 1,
                    'msg' => __('COA created successfully.'),
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('status', [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ]);
        }
    }

    /**
     * Display the specified COA category.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $this->ensureAdmin();

        $business_id = session('business.id');

        $category = CoaCategory::with(['lists', 'location', 'brand'])
            ->where('business_id', $business_id)
            ->findOrFail($id);

        return view('coa.show', compact('category'));
    }

    /**
     * Remove the specified COA category and its lists from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->ensureAdmin();

        $business_id = session('business.id');

        try {
            $category = CoaCategory::where('business_id', $business_id)->findOrFail($id);
            $category->lists()->delete();
            $category->delete();

            $output = [
                'success' => true,
                'msg' => __('COA deleted successfully.'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if (request()->ajax()) {
            return response()->json($output);
        }

        return redirect()
            ->route('coa.index')
            ->with('status', [
                'success' => $output['success'] ? 1 : 0,
                'msg' => $output['msg'],
            ]);
    }

    /**
     * Public API: Get COA categories and lists for a given location (and optional brand).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        $locationId = $request->query('location_id');
        $brandId = $request->query('brand_id');

        if (empty($locationId)) {
            return response()->json([
                'status' => false,
                'message' => 'location_id is required',
            ], 422);
        }

        $query = CoaCategory::with(['lists' => function ($q) {
            $q->select('id', 'coa_category_id', 'name', 'link');
        }])
            ->where('location_id', $locationId);

        if (! empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        // Order by creation time so first created = position 1
        $categories = $query
            ->orderBy('created_at', 'asc')
            ->get([
                'id',
                'name',
                'business_id',
                'location_id',
                'brand_id',
            ]);

        // Add sequential position to each category
        $categoriesWithPosition = $categories->map(function ($category, $index) {
            $category->position = $index + 1; // 1-based position
            return $category;
        });

        return response()->json([
            'status' => true,
            'data' => $categoriesWithPosition,
            'total' => $categoriesWithPosition->count(),
        ]);
    }

    /**
     * Search categories for autocomplete (AJAX endpoint).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCategories(Request $request)
    {
        $business_id = session('business.id');
        $searchTerm = strtolower($request->get('q', ''));

        // Predefined category list
      

        // Get existing categories from database
        $dbCategories = CoaCategory::where('business_id', $business_id)
            ->pluck('name')
            ->toArray();

        // Merge predefined and database categories, remove duplicates
        $allCategories = array_unique(array_merge($predefinedCategories, $dbCategories));

        // Filter categories based on search term (case-insensitive)
        // Only return results if search term is provided
        $filteredCategories = [];
        if (!empty($searchTerm)) {
            $searchTermLower = strtolower(trim($searchTerm));
            foreach ($allCategories as $category) {
                $categoryLower = strtolower($category);
                // Check for exact match or starts with the search term
                if ($categoryLower === $searchTermLower || strpos($categoryLower, $searchTermLower) === 0) {
                    $filteredCategories[] = $category;
                }
            }
            // Limit to 20 results
            $filteredCategories = array_slice($filteredCategories, 0, 20);
        }
        // If search term is empty, return empty array (no suggestions)

        // Format for Select2
        $results = [];
        foreach ($filteredCategories as $index => $categoryName) {
            // Check if this category exists in database
            $dbCategory = CoaCategory::where('business_id', $business_id)
                ->where('name', $categoryName)
                ->first();

            $results[] = [
                'id' => $categoryName,
                'text' => $categoryName,
                'categoryId' => $dbCategory ? $dbCategory->id : null,
            ];
        }

        return response()->json([
            'categories' => $results,
            'total' => count($results),
        ]);
    }

    /**
     * Public API: Get a single COA category and its lists by ID.
     *
     * @param  int  $id  Category ID
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiShow($id, Request $request)
    {
        $locationId = $request->query('location_id');
        $brandId = $request->query('brand_id');

        // Build query with ID and optional filters
        $query = CoaCategory::with(['lists' => function ($q) {
            $q->select('id', 'coa_category_id', 'name', 'link');
        }])
            ->where('id', $id);

        // Apply location filter if provided
        if (! empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        // Apply brand filter if provided
        if (! empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        // Get the category
        $category = $query->first([
            'id',
            'name',
            'business_id',
            'location_id',
            'brand_id',
        ]);

        if (! $category) {
            $message = 'COA not found';
            if (! empty($locationId) || ! empty($brandId)) {
                $message .= ' with the specified filters';
            }
            return response()->json([
                'status' => false,
                'message' => $message,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $category,
        ]);
    }
}
