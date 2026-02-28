<?php

namespace App\Http\Controllers;

use App\Media;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | UserController
    |--------------------------------------------------------------------------
    |
    | This controller handles the manipualtion of user
    |
    */

    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Shows profile of logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        return view('user.profile', compact('user', 'languages'));
    }

    /**
     * updates user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $user_id = $request->session()->get('user.id');
            $input = $request->only(['surname', 'first_name', 'last_name', 'email', 'language', 'marital_status',
                'blood_group', 'contact_number', 'fb_link', 'twitter_link', 'social_media_1',
                'social_media_2', 'permanent_address', 'current_address',
                'guardian_name', 'custom_field_1', 'custom_field_2',
                'custom_field_3', 'custom_field_4', 'id_proof_name', 'id_proof_number', 'gender', 'family_number', 'alt_number', ]);

            if (! empty($request->input('dob'))) {
                $input['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }
            if (! empty($request->input('bank_details'))) {
                $input['bank_details'] = json_encode($request->input('bank_details'));
            }

            $user = User::find($user_id);
            $user->update($input);

            Media::uploadMedia($user->business_id, $user, request(), 'profile_photo', true);

            //update session
            $input['id'] = $user_id;
            $business_id = request()->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            session()->put('user', $input);

            $output = ['success' => 1,
                'msg' => __('lang_v1.profile_updated_successfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('user/profile')->with('status', $output);
    }

    /**
     * updates user password
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        //Disable in demo
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $user_id = $request->session()->get('user.id');
            $user = User::where('id', $user_id)->first();

            if (Hash::check($request->input('current_password'), $user->password)) {
                $user->password = Hash::make($request->input('new_password'));
                $user->save();
                $output = ['success' => 1,
                    'msg' => __('lang_v1.password_updated_successfully'),
                ];
            } else {
                $output = ['success' => 0,
                    'msg' => __('lang_v1.u_have_entered_wrong_password'),
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('user/profile')->with('status', $output);
    }

    public function updateProfileField(Request $request)
    {
        $notAllowed = $this->moduleUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        if ($request->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');

                $field = $request->input('field');
                $value = $request->input('value');

                $allowed_fields = [
                    'name', 'email', 'language', 'contact_number',
                    'dob', 'gender', 'marital_status', 'blood_group',
                    'alt_number', 'family_number',
                    'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4',
                    'guardian_name', 'id_proof_name', 'id_proof_number',
                    'permanent_address', 'current_address',
                    'fb_link', 'twitter_link', 'social_media_1', 'social_media_2',
                ];

                $is_bank_details = false;
                if (is_string($field) && str_starts_with($field, 'bank_details.')) {
                    $is_bank_details = true;
                }

                if (! $is_bank_details && ! in_array($field, $allowed_fields)) {
                    return [
                        'success' => 0,
                        'msg' => __('messages.something_went_wrong'),
                    ];
                }

                $user = User::findOrFail($user_id);

                if ($field === 'email') {
                    $request->validate([
                        'value' => 'nullable|email|unique:users,email,' . $user_id,
                    ]);
                    $user->email = $value;
                } elseif ($field === 'dob') {
                    $request->validate([
                        'value' => 'nullable|string',
                    ]);

                    if (! empty($value)) {
                        $user->dob = $this->moduleUtil->uf_date($value);
                    } else {
                        $user->dob = null;
                    }
                } elseif ($field === 'language') {
                    $request->validate([
                        'value' => 'required|string',
                    ]);
                    $user->language = $value;
                } elseif ($field === 'contact_number') {
                    $request->validate([
                        'value' => 'nullable|string|max:20',
                    ]);
                    $user->contact_number = $value;
                } elseif ($field === 'gender') {
                    $request->validate([
                        'value' => 'nullable|string|in:male,female,others',
                    ]);
                    $user->gender = $value;
                } elseif ($field === 'marital_status') {
                    $request->validate([
                        'value' => 'nullable|string|in:married,unmarried,divorced',
                    ]);
                    $user->marital_status = $value;
                } elseif ($field === 'blood_group') {
                    $request->validate([
                        'value' => 'nullable|string|max:50',
                    ]);
                    $user->blood_group = $value;
                } elseif ($field === 'alt_number' || $field === 'family_number' || $field === 'id_proof_number') {
                    $request->validate([
                        'value' => 'nullable|string|max:50',
                    ]);
                    $user->{$field} = $value;
                } elseif ($field === 'custom_field_1' || $field === 'custom_field_2' || $field === 'custom_field_3' || $field === 'custom_field_4') {
                    $request->validate([
                        'value' => 'nullable|string|max:191',
                    ]);
                    $user->{$field} = $value;
                } elseif ($field === 'guardian_name' || $field === 'id_proof_name') {
                    $request->validate([
                        'value' => 'nullable|string|max:191',
                    ]);
                    $user->{$field} = $value;
                } elseif ($field === 'permanent_address' || $field === 'current_address') {
                    $request->validate([
                        'value' => 'nullable|string|max:5000',
                    ]);
                    $user->{$field} = $value;
                } elseif ($field === 'fb_link' || $field === 'twitter_link' || $field === 'social_media_1' || $field === 'social_media_2') {
                    $request->validate([
                        'value' => 'nullable|string|max:255',
                    ]);
                    $user->{$field} = $value;
                } elseif ($field === 'name') {
                    $request->validate([
                        'value' => 'required|string|max:191',
                    ]);

                    $name = trim((string) $value);
                    $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);

                    $first = $parts[0] ?? '';
                    $last = '';
                    if (count($parts) > 1) {
                        $last = implode(' ', array_slice($parts, 1));
                    }

                    $user->first_name = $first;
                    $user->last_name = $last;
                } elseif ($is_bank_details) {
                    $key = str_replace('bank_details.', '', $field);
                    $allowed_bank_keys = ['account_holder_name', 'account_number', 'bank_name', 'bank_code', 'branch', 'tax_payer_id'];

                    if (! in_array($key, $allowed_bank_keys)) {
                        return [
                            'success' => 0,
                            'msg' => __('messages.something_went_wrong'),
                        ];
                    }

                    $request->validate([
                        'value' => 'nullable|string|max:255',
                    ]);

                    $bank_details = [];
                    if (! empty($user->bank_details)) {
                        $decoded = json_decode($user->bank_details, true);
                        if (is_array($decoded)) {
                            $bank_details = $decoded;
                        }
                    }

                    $bank_details[$key] = $value;
                    $user->bank_details = json_encode($bank_details);
                }

                $user->save();

                $session_user = $request->session()->get('user');
                if (! is_array($session_user)) {
                    $session_user = [];
                }
                $session_user['id'] = $user->id;
                $session_user['business_id'] = $user->business_id;
                $session_user['first_name'] = $user->first_name;
                $session_user['last_name'] = $user->last_name;
                $session_user['surname'] = $user->surname;
                $session_user['email'] = $user->email;
                $session_user['language'] = $user->language;
                $session_user['contact_number'] = $user->contact_number;
                session()->put('user', $session_user);

                return [
                    'success' => 1,
                    'msg' => __('lang_v1.profile_updated_successfully'),
                    'data' => [
                        'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                        'email' => $user->email,
                        'language' => $user->language,
                        'contact_number' => $user->contact_number,
                        'fb_link' => $user->fb_link,
                        'twitter_link' => $user->twitter_link,
                        'social_media_1' => $user->social_media_1,
                        'social_media_2' => $user->social_media_2,
                    ],
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                return [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        abort(404);
    }
}
