<?php

namespace App\Http\Controllers;

use App\ContactUs;
use App\NewsLetterSubscriber;
use App\Brands;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ContactUs::with(['location', 'brand'])->select('id', 'reference_no', 'fname', 'lname', 'email', 'subject', 'message', 'created_at', 'location_id', 'brand_id');

            // Filter by current store/location so B2C only sees B2C contacts, B2B only sees B2B
            $permitted_locations = auth()->user()->permitted_locations();
            $current_location_id = $request->session()->get('user.current_location_id');
            if ($current_location_id === null && is_array($permitted_locations) && !empty($permitted_locations)) {
                $current_location_id = $permitted_locations[0];
            }
            if ($current_location_id !== null) {
                $query->where('location_id', $current_location_id);
            }

            // Apply inventory type filter
            $inventory_type = $request->get('inventory_type', 'all');
            
            if ($inventory_type === 'b2b') {
                $query->whereHas('location', function($q) {
                    $q->where('is_b2c', 0);
                });
            } elseif ($inventory_type === 'b2c') {
                $query->whereHas('location', function($q) {
                    $q->where('is_b2c', 1);
                });
            }

            // Apply brand filter
            $brand_id = $request->get('brand_id');
            
            if ($brand_id) {
                $query->where('brand_id', $brand_id);
            }

            return DataTables::of($query)
                ->addColumn('name', function ($row) {
                    return $row->fname . ' ' . $row->lname;
                })
                ->addColumn('location', function ($row) {
                    return $row->location ? $row->location->name : 'N/A';
                })
                ->addColumn('brand', function ($row) {
                    // Which website/brand the contact came from (e.g. Curevana, Moon Buzz, Astral Puff)
                    if ($row->brand) {
                        return $row->brand->name;
                    }
                    return $row->location_id == 1 ? '—' : 'N/A';
                })
                ->addColumn('send_mail', function ($row) {
                    // return '<img src="./img/sendmail.svg" alt="send mail" style="width: 25px; height: 25px;" class="send_mail_contact_us" data-href="' . action([\App\Http\Controllers\ContactUsController::class, 'mailPopup'],[ $row->id ]) . '" >';
                    return '<img src="./img/sendmail.svg" alt="send mail" style="width: 25px; height: 25px;" class="send_mail_contact_us" data-href="' . action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $row->id, 'template_for' => 'contact_us_send']) . '" >';
                })

                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group dropdown scroll-safe-dropdown"><button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __('messages.actions') . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        $html .= '<li><a data-href="' . action([\App\Http\Controllers\ContactUsController::class, 'show'], [$row->id]) . '" class="view-contact-us"><i class="fa fa-eye"></i> ' . __('messages.view') . '</a></li>';
                        $html .= '<li><a data-id="'.$row->id .'" class="delete-contact-us"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->addColumn('message', function ($row) {
                    $words = explode(' ', $row->message);
                    return implode(' ', array_slice($words, 0, 5)) . (count($words) > 5 ? '...' : '');
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['send_mail', 'action', 'location', 'brand'])
                ->make(true);
        }

        // Get brands for B2C locations (location_id = 2)
        $b2c_brands = Brands::where('location_id', 2)->pluck('name', 'id')->toArray();

        return view('contact_us.index', compact('b2c_brands'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // try {
        //     $input = $request->only([
        //         'fname', 'lname', 'email', 'phone', 'subject', 'message', 'status', 'staff_id', 'location_id', 'brand_id'
        //     ]);

        //     // Get location_id from request or set default
        //     $location_id = $request->input('location_id', 1);
            
        //     // Set brand_id based on location_id condition
        //     $brand_id = null;
        //     if ($location_id != 1) {
        //         $brand_id = $request->input('brand_id');
        //     }

        //     $input['location_id'] = $location_id;
        //     $input['brand_id'] = $brand_id;

        //     $contactUs = ContactUs::create($input);

        //     $output = [
        //         'success' => true,
        //         'msg' => __('Contact Us created successfully'),
        //         'data' => $contactUs
        //     ];

        // } catch (\Exception $e) {
        //     \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

        //     $output = [
        //         'success' => false,
        //         'msg' => __('messages.something_went_wrong'),
        //     ];
        // }

        // return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactUs  $contactUs
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contect_us = ContactUs::with(['location', 'brand'])->findOrFail($id);   
        
        return view('contact_us.show')->with(compact('contect_us'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContactUs  $contactUs
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactUs $contactUs)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactUs  $contactUs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactUs $contactUs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactUs  $contactUs
     * @return \Illuminate\Http\Response
     */

    //  public function mailPopup($id){

    //     return view('contact_us.partial.mail_writer_popup')->with(compact('id'));

    //  }

     public function sendMail(Request $request){

        try {

            //model id like project_id, user_id
            $notable_id = request()->get('id');
            //model name like App\Contact
            $notable_type = request()->get('notable_type');

            $input = $request->only('heading', 'description', 'is_private', 'is_mail');

            if (!array_key_exists('is_mail', $input)) {
                $input['is_mail'] = 0;
            }

            $input['business_id'] = request()->session()->get('user.business_id');
            $input['created_by'] = request()->session()->get('user.id');

            DB::beginTransaction();
            if ($input['is_mail']) {
                $email = $model->email;
                $subject = $input['heading'];
                $description = $input['description'];
                Mail::to($email)->send(new DocumentNoteMail($subject, $description, $file_names));
            }
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (Exception $e) {
            DB::rollBack();

            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;

        return response()->json($request);
     }
     
     public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $contect_us = ContactUs::findOrFail($id);
                $contect_us->delete();
                $output = [
                    'success' => true,
                    'msg' => __('News Letter.deleted_success'),
                ];
            } catch (\Exception $e) {
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }


    
    public function getNewsLetter(Request $request)
    {
        if ($request->ajax()) {
             // NEWLY ADDED: Load location and brand relationships to display names instead of IDs
             $query = NewsLetterSubscriber::with(['location', 'brand'])->select('id', 'email', 'created_at', 'location_id', 'brand_id');

             // Apply inventory type filter
             $inventory_type = $request->get('inventory_type', 'all');
             
             if ($inventory_type === 'b2b') {
                 $query->whereHas('location', function($q) {
                     $q->where('is_b2c', 0);
                 });
             } elseif ($inventory_type === 'b2c') {
                 $query->whereHas('location', function($q) {
                     $q->where('is_b2c', 1);
                 });
             }
             $brand_id = $request->get('brand_id');
            
             if ($brand_id) {
                 $query->where('brand_id', $brand_id);
             }
       //ajara table data show code start
             // NEWLY ADDED: Display location and brand names instead of IDs in the newsletter table
             return Datatables::of($query) 
                 ->addColumn('location', function ($row) {
                     return $row->location ? $row->location->name : 'N/A';// checks if row has location which is not null
                 })
                 ->addColumn('brand', function ($row) {
                     return $row->brand ? $row->brand->name : 'N/A'; // checks if row has brand which is not null 
                 })
                 ->addColumn(
                    'action',
                    function ($row) {
                        return '<button class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_news_letter_button"  data-id=' . $row->id . '>
                                <i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '
                            </button>';
                    }
                )
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['action','location','brand'])
                ->make(true);
            


           
        }
        $b2c_brands = Brands::where('location_id', 2)->pluck('name', 'id')->toArray();
        return view('contact_us.newsletter',compact('b2c_brands'));
    }
    public function deleteNewsLetter($id)
    {
        if (request()->ajax()) {
            try {
                $news_letter = NewsLetterSubscriber::findOrFail($id);
                $news_letter->delete();
                $output = [
                    'success' => true,
                    'msg' => __('News Letter.deleted_success'),
                ];
            } catch (\Exception $e) {
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
}
