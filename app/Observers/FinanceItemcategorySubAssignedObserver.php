<?php

namespace App\Observers;

use App\Models\FinanceItemcategorySubAssigned;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class FinanceItemcategorySubAssignedObserver
{
    public function created(FinanceItemcategorySubAssigned $financeItemcategorySubAssigned)
    {

    }

    public function updated(FinanceItemcategorySubAssigned $financeItemcategorySubAssigned)
    {
//        Log::useFiles(storage_path() . '/logs/audit.log');
//        $updatedField = $financeItemcategorySubAssigned->getDirty();
//        if(isset($updatedField['isAssigned'])) {
//            $user = User::find(auth()->id());
//            Log::info('data:', [
//                'sub_category_id' => "$financeItemcategorySubAssigned->itemCategorySubID",
//                'user_name' => $user->name,
//                'date_time' => date('Y-m-d H:i:s'),
//                'amended_field' => "Company - Is Assigned",
//                'previous_value' => ($financeItemcategorySubAssigned->isAssigned == true) ? 'False' : 'True',
//                'new_value' => ($financeItemcategorySubAssigned->isAssigned == true) ? 'True' : 'False',
//                'table' => 'financeitemcategorysub',
//                'channel' => 'audit',
//                'tenant_uuid' => isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local'
//            ]);
//        }
//        if(isset($updatedField['isActive'])) {
//            $user = User::find(auth()->id());
//            Log::info('data:', [
//                'sub_category_id' => "$financeItemcategorySubAssigned->itemCategorySubID",
//                'user_name' => $user->name,
//                'date_time' => date('Y-m-d H:i:s'),
//                'amended_field' => "Company - Is Active",
//                'previous_value' => ($financeItemcategorySubAssigned->isActive == true) ? 'False' : 'True',
//                'new_value' => ($financeItemcategorySubAssigned->isActive == true) ? 'True' : 'False',
//                'table' => 'financeitemcategorysub',
//                'channel' => 'audit',
//                'tenant_uuid' => isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local'
//            ]);
//        }
    }
}
