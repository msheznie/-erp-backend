<?php

use Illuminate\Database\Seeder;
use App\Models\ItemMaster;
use App\Models\FinanceItemCategoryMaster;
use App\Models\ItemAssigned;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ItemMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $runningSerialOrder = 101;

        for($i = 0; $i < 1000; $i++) {
            $item = ItemMaster::create([
                'primaryItemCode' => 'INV',
                'runningSerialOrder' => $runningSerialOrder,
                'documentSystemID' => 57,
                'documentID' => 'ITMM',
                'primaryCompanySystemID' => 1,
                'primaryCompanyID' => 'GUTech',
                'primaryCode' => 'INV'.$runningSerialOrder,
                'secondaryItemCode' => $faker->numberBetween($min = 500, $max = 8000),
                'barcode' => 'INV'.$runningSerialOrder,
                'itemDescription' => $faker->name,
                'unit' => 1,
                'financeCategoryMaster' => 1,
                'financeCategorySub' => 246,
                'isActive' => 1,
                'itemConfirmedYN' => 1,
                'itemConfirmedByEMPSystemID' => 11,
                'itemConfirmedByEMPID' => 888,
                'itemApprovedYN' => 1
            ]);


            $itemMaster = DB::table('itemmaster')
            ->selectRaw('itemCodeSystem,primaryCode as itemPrimaryCode,secondaryItemCode,barcode,itemDescription,unit as itemUnitOfMeasure,itemUrl,primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,financeCategoryMaster,financeCategorySub, -1 as isAssigned,companymaster.localCurrencyID as wacValueLocalCurrencyID,companymaster.reportingCurrency as wacValueReportingCurrencyID,NOW() as timeStamp,isPOSItem, faFinanceCatID')
            ->join('companymaster', 'companySystemID', '=', 'primaryCompanySystemID')
            ->where('itemCodeSystem', $item->itemCodeSystem)
            ->first();
            $itemMaster = collect($itemMaster)->toArray();

            $itemAssign = ItemAssigned::insert($itemMaster);


            $runningSerialOrder++;
        }

        $financeCategoryMaster = FinanceItemCategoryMaster::where('itemCategoryID', 1)->first();

        $financeCategoryMaster->lastSerialOrder = $runningSerialOrder;
        $financeCategoryMaster->save();


    }
}
