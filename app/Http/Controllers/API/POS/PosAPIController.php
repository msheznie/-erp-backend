<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Models\CustomerMasterCategory;

class PosAPIController extends AppBaseController
{
 function pullCustomerCategory(){

     $customerCategories = CustomerMasterCategory::all();
     $customerCategoryArray = array();
     foreach($customerCategories as $item){
         $data = array('id'=>$item->categoryID, 'party_type'=>1, 'description'=>$item->categoryDescription);
         array_push($customerCategoryArray,$data);
     }
     return response($customerCategoryArray,200);
 }
}
