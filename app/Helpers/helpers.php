<?php

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Role;
use App\Models\Wallet;
use App\Models\Company;
use App\Models\Vendor;
use App\Models\Client;
use Illuminate\Validation\Rule;

if (! function_exists('DateTimeFormate')) {
    function DateTimeFormate($date)
    {
        try {
            $dateTime = new DateTime($date);
            return $dateTime->format('d-m-Y h:i:s A');
        } catch (Exception $e) {
            return null; // Handle invalid date here
        }
    }
}


if (!function_exists('userCan')) {
    function userCan($permission)
    {
        if ($permission) {
            $permissionRecord = Permission::where('name', $permission)->first();

            if ($permissionRecord) {
                $user = Auth::user();
                if ($user && in_array($permissionRecord->id, explode(',', $user->permission_ids))) {
                    $rolePermissions = RolePermission::where('permission_id', $permissionRecord->id)->get()->toArray();

                    if ($rolePermissions) {
                        $roleIds = array_column($rolePermissions, 'role_id');

                        $vaialable_role = Role::whereIn('id',$roleIds)->first();

                        if(!$vaialable_role){
                            return false;
                        }else{
                            return true;
                        }

                    }
                }
            }
        }
        return false;
    }
}







function getFileIcon($extension) {
    switch (strtolower($extension)) {
        case 'pdf':
            return 'https://i.imgur.com/gQ9zB3V.png'; // replace with actual PDF icon URL
        case 'doc':
        case 'docx':
            return 'https://i.imgur.com/word-icon.png'; // replace with actual Word icon URL
        case 'xls':
        case 'xlsx':
            return 'https://i.imgur.com/excel-icon.png'; // replace with actual Excel icon URL
        case 'ppt':
        case 'pptx':
            return 'https://i.imgur.com/ppt-icon.png'; // replace with actual PPT icon URL
        default:
            return 'https://i.imgur.com/default-icon.png'; // default icon
    }
}


function getCompanyId() {
    $usertype = Auth::user()->roles_ids;
    $usertype = explode(',', $usertype);
    $roles = Role::whereIn('id', $usertype)->pluck('name')->toArray();

    if (in_array('Super Admin', $roles) && (Auth::user()->company_id == "" || Auth::user()->company_id == null)) {
        return "super_admin";
    } else {
        $company_id = Auth::user()->company_id;
        if ($company_id) {

            return $company_id;
            $companies = Company::where('id', $company_id)->get()->toArray();
        } else {
            return false;
        }
    }
}

function WalletCreate($wallet_person_id,$wallets_type,$company_id = null) {
    $companyId = getCompanyId();
    if (is_numeric($companyId)) {
        $wallet = Wallet::where('wallet_person_id',$wallet_person_id)->where('wallets_type',$wallets_type)->first();
        if($wallet){
            return $wallet;
        }else{
            $wallet = new Wallet;
            $wallet->amount = 0;
            $wallet->wallet_person_id = $wallet_person_id;
            $wallet->wallets_type = $wallets_type;
            $wallet->company_id = $company_id ? $company_id : $companyId;
            if($wallet->save()){
                return "Wallet  Created Successfully";
            }else{
                throw new \Exception('Failed to create Wallet.');
            }
        }
    }
}


if (! function_exists('WalletIdToUserDetailGet')) {
    function WalletIdToUserDetailGet($wallet_id)
    {

        try {
            $wallet_detail = Wallet::where('id',$wallet_id)->first();
            $detail = [];
            if($wallet_detail){
                if($wallet_detail['wallets_type'] == "company"){
                    $detail['name'] = Company::where('id',$wallet_detail['wallet_person_id'])->pluck('name')->first();
                }else if($wallet_detail['wallets_type'] == "user"){
                    $detail['name'] = User::where('id',$wallet_detail['wallet_person_id'])->pluck('name')->first();
                }
                else if($wallet_detail['wallets_type'] == "client"){
                    $detail['name'] = Client::where('id',$wallet_detail['wallet_person_id'])->pluck('name')->first();
                }
                else if($wallet_detail['wallets_type'] == "vendor"){
                    $detail['name'] = Vendor::where('id',$wallet_detail['wallet_person_id'])->pluck('name')->first();
                }
                return $detail['name'];
            }
        } catch (Exception $e) {
            return null; // Handle invalid date here
        }
    }
}


if (! function_exists('CheckLoginUserMainAdmin')) {
    function CheckLoginUserMainAdmin(){
        $usertype = Auth::user()->roles_ids;
        $usertype = explode(',', $usertype);
        $roles = Role::whereIn('id', $usertype)->pluck('name')->toArray();
        if (in_array('Main Admin', $roles)) {
            return true;
        } else {
            return false;
        }
    }
}



