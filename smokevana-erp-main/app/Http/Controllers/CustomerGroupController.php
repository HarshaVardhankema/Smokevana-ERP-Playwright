<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\CustomerGroup;
use App\SellingPriceGroup;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerGroupController extends Controller
{
    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('view_customer_group')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $customer_group = CustomerGroup::where('customer_groups.business_id', $business_id)
                                    ->leftjoin('selling_price_groups as spg', 'spg.id', '=', 'customer_groups.selling_price_group_id')
                                ->select(['customer_groups.name', 'spg.name as selling_price_group', 'customer_groups.id']);

            return Datatables::of($customer_group)
                    ->addColumn('action', function ($row) {
                        $edit_btn = '';
                        if (auth()->user()->can('customer.update')) {
                            $edit_href = action([\App\Http\Controllers\CustomerGroupController::class, 'edit'], [$row->id]);
                            $edit_btn = '<button type="button" data-href="' . e($edit_href) . '" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-edit edit_customer_group_button"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</button> ';
                        }
                        $delete_btn = '';
                        if (auth()->user()->can('customer.delete')) {
                            $delete_href = action([\App\Http\Controllers\CustomerGroupController::class, 'destroy'], [$row->id]);
                            $delete_btn = '<button type="button" data-href="' . e($delete_href) . '" class="tw-dw-btn tw-dw-btn-xs table-action-btn table-action-btn-delete delete_customer_group_button"><i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '</button>';
                        }
                        return '<div class="sm-table-actions">' . $edit_btn . $delete_btn . '</div>';
                    })
                    ->editColumn('selling_price_group', '{{$selling_price_group ?? "--"}}')
                    ->removeColumn('id')
                    ->rawColumns([2])
                    ->make(false);
        }

        return view('customer_group.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $price_groups = SellingPriceGroup::forDropdown($business_id, false);

        return view('customer_group.create')->with(compact('price_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id', 'price_percentage']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            
            // Set default price_calculation_type if not provided
            $input['price_calculation_type'] = $input['price_calculation_type'] ?? 'percentage';
            
            // Handle fields based on price_calculation_type
            if ($input['price_calculation_type'] == 'percentage') {
                $input['amount'] = ! empty($input['amount']) ? $this->commonUtil->num_uf($input['amount']) : 0;
                $input['selling_price_group_id'] = null;
                $input['price_percentage'] = null;
            } else {
                // selling_price_group type
                $input['amount'] = 0;
                $input['price_percentage'] = ! empty($input['price_percentage']) ? $this->commonUtil->num_uf($input['price_percentage']) : null;
            }

            $customer_group = CustomerGroup::create($input);
            $output = ['success' => true,
                'data' => $customer_group,
                'msg' => __('lang_v1.success'),
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerGroup  $customerGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $customer_group = CustomerGroup::where('business_id', $business_id)->find($id);

            $business_id = request()->session()->get('user.business_id');
            $price_groups = SellingPriceGroup::forDropdown($business_id, false);

            return view('customer_group.edit')
                ->with(compact('customer_group', 'price_groups'));
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
        if (! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id', 'price_percentage']);
                $business_id = $request->session()->get('user.business_id');

                // Set default price_calculation_type if not provided
                $input['price_calculation_type'] = $input['price_calculation_type'] ?? 'percentage';
                
                // Handle fields based on price_calculation_type
                if ($input['price_calculation_type'] == 'percentage') {
                    $input['amount'] = ! empty($input['amount']) ? $this->commonUtil->num_uf($input['amount']) : 0;
                    $input['selling_price_group_id'] = null;
                    $input['price_percentage'] = null;
                } else {
                    // selling_price_group type
                    $input['amount'] = 0;
                    $input['price_percentage'] = ! empty($input['price_percentage']) ? $this->commonUtil->num_uf($input['price_percentage']) : null;
                }

                $customer_group = CustomerGroup::where('business_id', $business_id)->findOrFail($id);

                $customer_group->update($input);

                $output = ['success' => true,
                    'msg' => __('lang_v1.success'),
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $cg = CustomerGroup::where('business_id', $business_id)->findOrFail($id);
                $cg->delete();

                $output = ['success' => true,
                    'msg' => __('lang_v1.success'),
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
}
