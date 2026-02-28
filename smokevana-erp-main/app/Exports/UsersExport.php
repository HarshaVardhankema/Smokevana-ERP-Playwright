<?php

namespace App\Exports;

use App\User;
use App\Utils\ModuleUtil;
use DB;
use Maatwebsite\Excel\Concerns\FromArray;

class UsersExport implements FromArray
{
    protected $business_id;

    protected $moduleUtil;

    public function __construct($business_id)
    {
        $this->business_id = $business_id;
        $this->moduleUtil = app(ModuleUtil::class);
    }

    public function array(): array
    {
        $users = User::where('business_id', $this->business_id)
            ->user()
            ->where('is_cmmsn_agnt', 0)
            ->select([
                'id',
                'username',
                DB::raw("TRIM(CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) as full_name"),
                'email',
                'allow_login',
            ])
            ->get();

        $headers = [__('business.username'), __('user.name'), __('user.role'), __('business.email')];
        $rows = [$headers];

        foreach ($users as $user) {
            $username = $user->username;
            if (empty($user->allow_login)) {
                $username .= ' ' . __('lang_v1.login_not_allowed');
            }
            $role_name = $this->moduleUtil->getUserRoleName($user->id);
            $rows[] = [
                $username,
                trim($user->full_name ?? ''),
                $role_name,
                $user->email ?? '',
            ];
        }

        return $rows;
    }
}
