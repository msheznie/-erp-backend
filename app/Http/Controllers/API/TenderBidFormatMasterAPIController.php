<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderBidFormatMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderBidFormatMasterAPIRequest;
use App\Models\PricingScheduleMaster;
use App\Models\TenderBidFormatDetail;
use App\Models\TenderBidFormatMaster;
use App\Models\TenderFieldType;
use App\Repositories\TenderBidFormatMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\PricingScheduleDetail;
/**
 * Class TenderBidFormatMasterController
 * @package App\Http\Controllers\API
 */

class TenderBidFormatMasterAPIController extends AppBaseController
{
    /** @var  TenderBidFormatMasterRepository */
    private $tenderBidFormatMasterRepository;

    public function __construct(TenderBidFormatMasterRepository $tenderBidFormatMasterRepo)
    {
        $this->tenderBidFormatMasterRepository = $tenderBidFormatMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidFormatMasters",
     *      summary="Get a listing of the TenderBidFormatMasters.",
     *      tags={"TenderBidFormatMaster"},
     *      description="Get all TenderBidFormatMasters",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TenderBidFormatMaster")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->tenderBidFormatMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBidFormatMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBidFormatMasters = $this->tenderBidFormatMasterRepository->all();

        return $this->sendResponse($tenderBidFormatMasters->toArray(), trans('custom.tender_bid_format_masters_retrieved_successfully'));
    }

    /**
     * @param CreateTenderBidFormatMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderBidFormatMasters",
     *      summary="Store a newly created TenderBidFormatMaster in storage",
     *      tags={"TenderBidFormatMaster"},
     *      description="Store TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidFormatMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidFormatMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderBidFormatMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBidFormatMasterAPIRequest $request)
    {
        $input = $request->all();

        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->create($input);

        return $this->sendResponse($tenderBidFormatMaster->toArray(), trans('custom.tender_bid_format_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidFormatMasters/{id}",
     *      summary="Display the specified TenderBidFormatMaster",
     *      tags={"TenderBidFormatMaster"},
     *      description="Get TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderBidFormatMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var TenderBidFormatMaster $tenderBidFormatMaster */
        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->findWithoutFail($id);

        if (empty($tenderBidFormatMaster)) {
            return $this->sendError(trans('custom.tender_bid_format_master_not_found'));
        }

        return $this->sendResponse($tenderBidFormatMaster->toArray(), trans('custom.tender_bid_format_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderBidFormatMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderBidFormatMasters/{id}",
     *      summary="Update the specified TenderBidFormatMaster in storage",
     *      tags={"TenderBidFormatMaster"},
     *      description="Update TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidFormatMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidFormatMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderBidFormatMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBidFormatMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBidFormatMaster $tenderBidFormatMaster */
        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->findWithoutFail($id);

        if (empty($tenderBidFormatMaster)) {
            return $this->sendError(trans('custom.tender_bid_format_master_not_found'));
        }

        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->update($input, $id);

        return $this->sendResponse($tenderBidFormatMaster->toArray(), trans('custom.tenderbidformatmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderBidFormatMasters/{id}",
     *      summary="Remove the specified TenderBidFormatMaster from storage",
     *      tags={"TenderBidFormatMaster"},
     *      description="Delete TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var TenderBidFormatMaster $tenderBidFormatMaster */
        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->findWithoutFail($id);

        if (empty($tenderBidFormatMaster)) {
            return $this->sendError(trans('custom.tender_bid_format_master_not_found'));
        }

        $tenderBidFormatMaster->delete();

        return $this->sendSuccess('Tender Bid Format Master deleted successfully');
    }

    public function getTenderBidFormats(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];



        $tenderMaster = TenderBidFormatMaster::where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->where('tender_name', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($tenderMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function storeBidFormat(Request $request)
    {
       $input = $request->all();
       $employee = \Helper::getEmployeeInfo();
       /*$boq_applicable = 0;
       if(isset($input['boq_applicable']) && $input['boq_applicable']){
           $boq_applicable = 1;
       }*/

       $exist = TenderBidFormatMaster::where('tender_name',$input['tender_name'])
           ->where('company_id',$input['companySystemID'])->first();

       if(!empty($exist)){
           return ['success' => false, 'message' => 'Description already exist'];
       }

        DB::beginTransaction();
        try {
           // $data['boq_applicable']=$boq_applicable;
           $data['tender_name']=$input['tender_name'];
           $data['company_id']=$input['companySystemID'];
           $data['created_by'] = $employee->employeeSystemID;

           $result = TenderBidFormatMaster::create($data);

           if($result){
               $detail_data = [
                   'tender_id' =>   $result['id'],
                   'label' => "Final Total",
                   'field_type' => 4,
                   'is_disabled' => 0,
                   'boq_applicable' => 0,
                   'finalTotalYn' => 1,
                   'created_by' => $employee->employeeSystemID
               ];
               $detail_result = TenderBidFormatDetail::create($detail_data);
               if($detail_result) {
                   DB::commit();
                   return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
               }
           }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function loadBidFormatMaster(Request $request)
    {
        $input = $request->all();

        $data['master'] = TenderBidFormatMaster::where('id',$input['id'])->where('company_id',$input['companySystemID'])->first();
        $data['detail'] = TenderBidFormatDetail::where('tender_id',$input['id'])->orderBy('finalTotalYn', 'ASC')->get();
        $data['tenderType'] = TenderFieldType::get();
        $pricebid = self::priceBidExistInTender($input['id']);
        if(!empty($pricebid)){
            $data['pricebid'] = 1;
        }else{
            $data['pricebid'] = 0;
        }

        return $data;
    }

    public function addPriceBidDetail(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        $is_disabled = 0;
        $boq_applicable = 0;
        if(!isset($input['label']) || empty($input['label'])){
            return ['success' => false, 'message' => 'Label is required'];
        }

        if(!isset($input['field_type']) || empty($input['field_type'])){
            return ['success' => false, 'message' => 'Field Type is required'];
        }

        if(isset($input['is_disabled']) && $input['is_disabled']){
            $is_disabled = 1;
        }

        if(isset($input['boq_applicable']) && $input['boq_applicable']){
            $boq_applicable = 1;
        }

        $exist = TenderBidFormatDetail::where('label',$input['label'])
            ->where('tender_id',$input['tender_id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Label already exist'];
        }

        DB::beginTransaction();
        try {
            $data['is_disabled']=$is_disabled;
            $data['boq_applicable']=$boq_applicable;
            $data['tender_id']=$input['tender_id'];
            $data['label']=$input['label'];
            $data['field_type']=$input['field_type'];
            $data['created_by'] = $employee->employeeSystemID;

            $result = TenderBidFormatDetail::create($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved'];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function updatePriceBidDetail(Request $request)
    {
        
        
        $details = $request->get('details');
        $type = $request->get('val');

        $input = $this->convertArrayToSelectedValue($details, array('field_type'));
        
       
        $employee = \Helper::getEmployeeInfo();
        $is_disabled = 0;
        $boq_applicable = 0;
        if(!isset($input['label']) || empty($input['label'])){
            return ['success' => false, 'message' => 'Label is required'];
        }

        if(!isset($input['field_type']) || empty($input['field_type'])){
            return ['success' => false, 'message' => 'Field Type is required'];
        }

        if(isset($input['is_disabled']) && $input['is_disabled']){
            $is_disabled = 1;
        }

        if(isset($input['boq_applicable']) && $input['boq_applicable']){
            $boq_applicable = 1;
        }

        $exist = TenderBidFormatDetail::where('label',$input['label'])
            ->where('tender_id',$input['tender_id'])->where('id','!=',$input['id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Label already exist'];
        }
        $tender_id = $input['tender_id'];
        $id = $input['id'];


        

        if(is_null($type))
        {
          
            $result = $this->checkPirceBidItem($tender_id,$id);

            if($result['is_exit'])
            {
                return ['success' => false, 'message' => 'Unable to update the line item,The item is used in the following formula '.$result['formulas']];
            }
        }
  



        DB::beginTransaction();
        try {
            
            $data['is_disabled']= $input['is_disabled'];
            if($input['field_type'] != 2)
            {
                $data['boq_applicable']=false;
            }
            else
            {
                $data['boq_applicable']=$input['boq_applicable'];
            }
   
            if($input['field_type'] == 4)
            {
                $data['boq_applicable']= false;
                $data['is_disabled']= false;
            }
            else
            {
                $data['formula_string']= null;
            }
            
           
            $data['label']=$input['label'];
            $data['field_type']=$input['field_type'];
            $data['updated_by'] = $employee->employeeSystemID;
           
            $result = TenderBidFormatDetail::where('id',$input['id'])->update($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function updateBidFormat(Request $request)
    {
        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();
        /*$boq_applicable = 0;
        if(isset($input['boq_applicable']) && $input['boq_applicable']){
            $boq_applicable = 1;
        }*/

        $exist = TenderBidFormatMaster::where('tender_name',$input['tender_name'])
            ->where('id','!=',$input['id'])
            ->where('company_id',$input['companySystemID'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Description already exist'];
        }

        DB::beginTransaction();
        try {
           // $pricebid = self::priceBidExistInTender($input['id']);
            /*if(empty($pricebid)) {
                $data['boq_applicable'] = $boq_applicable;
            }*/
            $data['tender_name']=$input['tender_name'];
            $data['updated_by'] = $employee->employeeSystemID;

            $result = TenderBidFormatMaster::where('id',$input['id'])->update($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }


    public function deletePriceBideDetail(Request $request)
    {
        $input = $request->all();

        $tender_id = $input['tender_id'];
        $id = $input['id'];
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {

            $result = $this->checkPirceBidItem($tender_id,$id);

            if($result['is_exit'])
            {
                return ['success' => false, 'message' => 'Unable to delete the line item,The item is used in the following formula '.$result['formulas']];
            }
            $data['deleted_by'] = $employee->employeeSystemID;
            TenderBidFormatDetail::where('id',$input['id'])->update($data);
            $result = TenderBidFormatDetail::where('id',$input['id'])->delete();
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function deletePriceBidMaster(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $pricebid = self::priceBidExistInTender($input['id']);

            if(!empty($pricebid)){
                return ['success' => false, 'message' => 'Price bid format cannot be deleted it has been used in tenders'];
            }

            $data['deleted_by'] = $employee->employeeSystemID;
            $data['deleted_at'] = now();
            $result = TenderBidFormatMaster::where('id',$input['id'])->update($data);
            TenderBidFormatDetail::where('tender_id',$input['id'])->delete();
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    function priceBidExistInTender($id){
       return PricingScheduleMaster::with(['tender_master'])->whereHas('tender_master')->where('price_bid_format_id',$id)->first();
    }

    public function getBitFormatItems(Request $request)
    {   
        $input = $request->all();
        $id = $input['id'];
        $bit_format_id = $input['tender_id'];

        $result = TenderBidFormatDetail::where('tender_id',$bit_format_id)->where('finalTotalYn','=',0)->where('id','!=',$id)->whereIn('field_type', [2, 3,4])->get();


        $formula = TenderBidFormatDetail::where('tender_id',$bit_format_id)->where('id',$id)->where('field_type',4)->Select('formula_string')->first();
        $data['result'] = $result;
        $data['formula'] = $formula->formula_string;

        return $this->sendResponse($data, trans('custom.tenderbidformatmaster_updated_successfully'));


    }

    public function addFormula(Request $request)
    {
        $input = $request->all();
        
        $id = $input['detail_id'];
        $bit_format_id = $input['bit_format_id'];
        $formula = isset($input['formula']) ? $input['formula'] : null;
        $new_formula = null;
        
        $p = '';
        $cont = '';
        $data = [];
        $formula_arr = null;  
  
        try {
            
            foreach ($formula as $formula_row) {
                if (trim($formula_row) != '') 
                {
                    $val1 = '';
    
                    $elementType = $formula_row[0];
    
                 
                    if ($elementType == '$') {
                        $elementArr = explode('$', $formula_row);
                        $val1 = 1;
                        $cont = $cont.$val1;
             
                    }
                    else if ($elementType == '#') {
                    $elementArr = explode('#', $formula_row);
                    $val1 = 1;
                    $cont = $cont.$val1;

                    }
                    else if($elementType == '|')
                    {
                        
                        $elementArr1 = explode('|', $formula_row);
                        $value = ($elementArr1[1]);
                        $cont = $cont.$value;
                       
                           
                    }
                    else if($elementType == '_')
                    {
                        $elementArr2 = explode('_', $formula_row);
                        if(empty($elementArr2[1]) || is_null($elementArr2))
                        {
                            $value2 = 0;
                        }
                        else
                        {
                            $value2 = 1;
                        }
    
                        
                        $cont = $cont.$value2;
                        
    
                    }
                }
               
            }
           

            
            $p = eval(' '.$cont.';');



            } catch (\Exception $e) {
               
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }

     

        if (!is_null($formula)) {
            if (is_array(($formula))) {
                if ($formula) {
                    $new_formula = implode('~', $formula);
                }
            }
        }




        DB::beginTransaction();
        try {
            $data['formula_string']= $new_formula;
            $result = TenderBidFormatDetail::where('id',$id)->where('tender_id',$bit_format_id)->first();
            $result->formula_string = $new_formula;
            $result->save();
          


            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }

        } catch (\Exception $e) {
             DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }


    }

    public function formulaGenerate(Request $request)
    {

        $results = $request->all();
       
       
        $details = [];
        foreach($results as $key=>$val)
        {
            if($val['typeId'] == 4)
            {   $p = '';
                $cont = '';
                $data = [];
                $formula_arr = null;         
                if (!is_null($val['formula_string'])) {
                       
                        if ($val['formula_string']) {
                            $formula_arr = explode('~', $val['formula_string']);


                            foreach ($formula_arr as $formula_row) {
                                if (trim($formula_row) != '') 
                                {
                                    $val1 = '';

                                    $elementType = $formula_row[0];
                                    if ($elementType == '$') {
                                        $elementArr = explode('$', $formula_row);
                                        $value = intval($elementArr[1]);
                                        foreach($results as $result)
                                        {
                                            if($result['bid_format_detail_id'] == $value)
                                            {
                                                    if($result['typeId'] == 2)
                                                    {
                                                        if($result['value'] != null)
                                                        {
                                                            $val1 = $result['value'];
                                                        }
                                                        else
                                                        {
                                                            $val1 = 0;
                                                        }
                                                        
                                                    }
                                                    else if($result['typeId'] == 3)
                                                    {
                                                       

                                                        if($result['value'] != null)
                                                        {
                                                            $val1 = $result['value']/100;
                                                        }
                                                        else
                                                        {
                                                            $val1 = 1;
                                                        }
                                                        
                                                    }
                                                $cont = $cont.$val1;
                                                break;
                                            }
                                            
                                        }
                                    }
                                    else if($elementType == '|')
                                    {
                                        
                                        $elementArr1 = explode('|', $formula_row);
                                        $value = ($elementArr1[1]);
                                        $cont = $cont.$value;
                                       
                                           
                                    }
                                    else if($elementType == '_')
                                    {
                                        $elementArr2 = explode('_', $formula_row);
                                        if(empty($elementArr2[1]) || is_null($elementArr2))
                                        {
                                            $value2 = 0;
                                        }
                                        else
                                        {
                                            $value2 = ($elementArr2[1]);
                                        }

                                        
                                        $cont = $cont.$value2;
                                        

                                    }
                                }
                               
                            }

                            $p = eval('return '.$cont.';');
                        } 
                    
                }
                $data[$key] = $p;
                array_push($details,$data);
            }
           

        }

        return $this->sendResponse($details, trans('custom.tenderbidformatmaster_updated_successfully'));

    }

    private function checkPirceBidItem($tender_id,$id)
    {

        $tender_details = TenderBidFormatDetail::where('tender_id',$tender_id)->where('field_type',4)->get();
        $is_value_exit = false;
        $formulas = '';
        if(isset($tender_details))
        {
            foreach($tender_details as $val)
            {
                if (!is_null($val['formula_string'])) {
                   
                    if ($val['formula_string']) {
                        $formula_arr = explode('~', $val['formula_string']);

                        foreach ($formula_arr as $formula_row) {
                            if (trim($formula_row) != '') 
                            {
                                $elementType = $formula_row[0];

                                if ($elementType == '$') {
                                    $elementArr = explode('$', $formula_row);
                                    $value = intval($elementArr[1]);
                                    if($value == $id)
                                    {
                                        $is_value_exit = true;
                                        $formulas = $formulas.','.$val['label'];
                                        break;
                                    }
                               
                                }
                            }
                        }
                    }
                }

            }
        }
        $data['is_exit'] = $is_value_exit;
        $data['formulas'] = $formulas;
        return $data;
    }
  

}
