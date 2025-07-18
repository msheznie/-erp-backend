<?php

namespace App\Repositories;

use App\Models\ItemAssigned;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\TenderBoqItems;
use App\Models\TenderBoqItemsEditLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderBoqItemsRepository
 * @package App\Repositories
 * @version April 8, 2022, 6:26 pm +04
 *
 * @method TenderBoqItems findWithoutFail($id, $columns = ['*'])
 * @method TenderBoqItems find($id, $columns = ['*'])
 * @method TenderBoqItems first($columns = ['*'])
 */
class TenderBoqItemsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'main_work_id',
        'item_id',
        'uom',
        'qty',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBoqItems::class;
    }

    public function getTenderBoqItems(Request $request){
        $input = $request->all();
        $versionID = $input['versionID'] ?? 0;

        $data['detail'] = $versionID > 0 ?
            TenderBoqItemsEditLog::getTenderBoqItemList($input['main_work_id'], $versionID) :
            TenderBoqItems::getTenderBoqItemList($input['main_work_id']);

        $data['uomDrop'] = Unit::get();
        $itemDrop = ItemAssigned::with(['item_master'])->where('companySystemID',$input['companySystemID'])->get();

        $items =array();
        foreach($itemDrop as $key => $val){
            $items[$key]['id'] = $val['itemCodeSystem'];
            $items[$key]['label'] = $val['item_master']['itemShortDescription'];
        }
        $data['itemDrop'] = $items;

        return $data;
    }

    public function checkBoqItemsExists($input, $editOrAmend, $itemName, $mainWorkID)
    {
        $exist = $editOrAmend ?
            TenderBoqItemsEditLog::checkItemNameExists($itemName, $mainWorkID) :
            TenderBoqItems::checkItemNameExists($itemName, $mainWorkID);

        $d['purchase_request_id'] = '';
        if (!empty($exist)) {
            $isFromOrigin = isset($input['origin']) && in_array($input['origin'], [1, 2]);

            if ($isFromOrigin) {
                $input['qty'] += $exist->qty;
                $d['purchase_request_id'] = isset($input['purchaseRequestID'])
                    ? $input['purchaseRequestID'] . ',' . $exist->purchase_request_id
                    : $exist->purchase_request_id;

                if($editOrAmend){
                    $exist->is_deleted = 1;
                    $exist->save();
                } else {
                    $exist->delete();
                }
            } else {
                return ['success' => false, 'message' => 'Item already exists'];
            }
        }
        return ['success' => true, 'message' => 'Valid item name', 'data' => $d['purchase_request_id']];
    }
    public function checkItemExistsForUpload($input, $itemName)
    {
        $versionID = $input['versionID'] ?? 0;
        if($versionID > 0){
            return TenderBoqItemsEditLog::checkItemNameExists($itemName, $input['main_work_id']);
        }
        return TenderBoqItems::checkItemNameExists($itemName, $input['main_work_id']);
    }
    public function saveBoqItemsUpload($data, $input)
    {
        try{
            return DB::transaction(function () use ($data, $input){
                $versionID = $input['versionID'] ?? 0;
                $data['tender_id'] = $input['tenderID'];
                $main_work_id = $input['main_work_id'];
                $data['origin'] = 0;
                $isEdit = $versionID > 0;

                if($isEdit){
                    $data['id'] = null;
                    $data['level_no'] = 1;
                    $data['amd_main_work_id'] = $input['main_work_id'];
                    $data['tender_edit_version_id'] = $versionID;
                    TenderBoqItemsEditLog::create($data);
                } else {
                    TenderBoqItems::create($data);
                }

                $mainWork = $isEdit
                    ? PricingScheduleDetailEditLog::find($main_work_id)
                    : PricingScheduleDetail::find($main_work_id);

                $mainWorkItems = $isEdit
                    ? PricingScheduleDetailEditLog::getPricingScheduleMainWork($mainWork->tender_id, $mainWork->amd_pricing_schedule_master_id, $versionID)
                    : PricingScheduleDetail::getPricingScheduleMainWork($mainWork->tender_id, $mainWork->pricing_schedule_master_id);

                $isMainWorkComplete = $mainWorkItems->count() > 0;

                if ($isMainWorkComplete) {
                    foreach ($mainWorkItems->get() as $main) {
                        if (empty($main->tender_boq_items)) {
                            $isMainWorkComplete = false;
                            break;
                        }
                    }
                }

                if ($isMainWorkComplete) {
                    $master = ['boq_status' => 1];

                    if ($isEdit) {
                        PricingScheduleMasterEditLog::where('amd_id', $mainWork->amd_pricing_schedule_master_id)->update($master);
                    } else {
                        PricingScheduleMaster::where('id', $mainWork->pricing_schedule_master_id)->update($master);
                    }
                }

                return ['success' => true, 'message' => 'Uploaded successfully.'];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $ex->getMessage()];
        }
    }

    public function checkValidUploadRequestParams($input){
        $validator = Validator::make($input, [
            'tenderID' => 'required',
            'main_work_id' => 'required',
        ], [

            'tenderID.required' => 'Tender Master ID is required',
            'main_work_id.required' => 'Main Work ID is required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => implode(', ', $validator->errors()->all())];
        }
        return ['success' => true, 'message' => 'Validation check success'];
    }
    public function checkValidBoqItemRequestParams($input){
        $validator = Validator::make($input, [
            'tender_id' => 'required',
            'main_work_id' => 'required',
            'item_name' => 'required',
            'uom' => 'required',
            'qty' => 'required',
        ], [

            'tender_id.required' => 'Tender Master ID is required',
            'main_work_id.required' => 'Main Work ID is required',
            'item_name.required' => 'Item is required',
            'uom.required' => 'UOM is required',
            'qty.required' => 'QTY is required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => implode(', ', $validator->errors()->all())];
        }
        return ['success' => true, 'message' => 'Validation check success'];
    }
}
