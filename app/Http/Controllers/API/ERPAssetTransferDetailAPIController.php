<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateERPAssetTransferDetailAPIRequest;
use App\Http\Requests\API\UpdateERPAssetTransferDetailAPIRequest;
use App\Models\ERPAssetTransferDetail;
use App\Repositories\ERPAssetTransferDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\AssetRequestDetail;
use App\Models\ERPAssetTransfer;
use App\Models\ErpLocation;
use App\Models\AssetRequest;
use App\Models\DepartmentMaster;
use App\Models\SMEPayAsset;
use App\Models\FixedAssetMaster;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ERPAssetTransferDetailController
 * @package App\Http\Controllers\API
 */

class ERPAssetTransferDetailAPIController extends AppBaseController
{
    /** @var  ERPAssetTransferDetailRepository */
    private $eRPAssetTransferDetailRepository;

    public function __construct(ERPAssetTransferDetailRepository $eRPAssetTransferDetailRepo)
    {
        $this->eRPAssetTransferDetailRepository = $eRPAssetTransferDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetTransferDetails",
     *      summary="Get a listing of the ERPAssetTransferDetails.",
     *      tags={"ERPAssetTransferDetail"},
     *      description="Get all ERPAssetTransferDetails",
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
     *                  @SWG\Items(ref="#/definitions/ERPAssetTransferDetail")
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
        $this->eRPAssetTransferDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->eRPAssetTransferDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eRPAssetTransferDetails = $this->eRPAssetTransferDetailRepository->all();

        return $this->sendResponse($eRPAssetTransferDetails->toArray(), trans('custom.e_r_p_asset_transfer_details_retrieved_successfull'));
    }

    /**
     * @param CreateERPAssetTransferDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/eRPAssetTransferDetails",
     *      summary="Store a newly created ERPAssetTransferDetail in storage",
     *      tags={"ERPAssetTransferDetail"},
     *      description="Store ERPAssetTransferDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetTransferDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetTransferDetail")
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
     *                  ref="#/definitions/ERPAssetTransferDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateERPAssetTransferDetailAPIRequest $request, $id)
    {
        $input = $request->all();

       $valuesOfAsset = array_filter(array_column($input, 'assetDropTransferIDDropVal'), function($n){ 
            return $n >0;
        });
        
        if(isset($valuesOfAsset)){ 
            $unique = array_unique($valuesOfAsset);
            $duplicates =  sizeof(array_diff_assoc($valuesOfAsset, $unique));
         
            if ($duplicates > 0) {
                 return $this->sendError(trans('custom.same_asset_cannot_be_link_multiple_times'));
            }
        }

        if (isset($input) && !empty($input)) {
            foreach ($input as $item => $value) {
                $erpAsset = ERPAssetTransferDetail::select('erp_fa_fa_asset_transfer_id')->where('erp_fa_fa_asset_transfer_id', $value['masterID'])
                    ->where('fa_master_id', $value['assetDropTransferID'])
                    ->where('pr_created_yn','!=',1)
                    ->get();
                    
                $assetRequest = AssetRequest::find($value['erp_fa_fa_asset_request_id']);
                if (count($erpAsset) > 0) {
                    return $this->sendError(trans('custom.same_asset_cannot_be_link_multiple_times'));
                }

                $assetExistUnApproved = ERPAssetTransferDetail::with(['assetTransferMaster'])
                ->where('fa_master_id',$value['assetDropTransferID']) 
                ->whereHas('assetTransferMaster', function ($query) use ($value) {
                    $query->where('company_id', $value['company_id'])
                        ->where('approved_yn', 0);
                })
                ->orderby('id','desc')
                ->first();  
                if(!empty($assetExistUnApproved->assetTransferMaster)){ 
                    return $this->sendError(trans('custom.asset_already_pulled_to_unapproved_document').$assetExistUnApproved->assetTransferMaster->document_code);  
                }

                $assetExistUnApproved = ERPAssetTransferDetail::where('fa_master_id',$value['assetDropTransferID'])
                ->whereHas('smePayAsset', function ($query) use ($value) {
                    $query->where('companyID', $value['company_id'])
                        ->where('returnStatus', 0);
                })
                ->where('receivedYN','=','1')
                ->orderby('id','desc')
                ->first();
                if(!empty($assetExistUnApproved)){ 
                    if($assetRequest->type == 1) {
                        $msg ='Some of the assets have already been assigned to employees. Are you sure you want to initiate the transfer';
                        return $this->sendResponse(['id' => false], $msg);

                    }
                }
                

                $data[] = [
                    'erp_fa_fa_asset_transfer_id' => $value['masterID'],
                    'erp_fa_fa_asset_request_id' => $value['erp_fa_fa_asset_request_id'],
                    'erp_fa_fa_asset_request_detail_id' => $value['id'],
                    'fa_master_id' => (isset($value['pr_created_yn']) && $value['pr_created_yn'] == true  ? 0 : $value['assetDropTransferID']),
                    'pr_created_yn' => (isset($value['pr_created_yn']) && $value['pr_created_yn'] == true ? 1 : 0),
                    'company_id' => $value['company_id'],
                    'created_user_id' => \Helper::getEmployeeSystemID(),
                    'itemCodeSystem' => $value['itemCodeSystem'],
                    'departmentSystemID' => (isset($value['type'][0]) && $value['type'][0] == 4 && isset($assetRequest)) ? $assetRequest->departmentSystemID : NULL
                ];
            }
            $this->eRPAssetTransferDetailRepository->insert($data);
            return $this->sendResponse(['id' => $id], trans('custom.asset_transfer_detail_saved_successfully'));
        } else {
            return $this->sendError('please add atleast one item to proceed');
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/eRPAssetTransferDetails/{id}",
     *      summary="Display the specified ERPAssetTransferDetail",
     *      tags={"ERPAssetTransferDetail"},
     *      description="Get ERPAssetTransferDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransferDetail",
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
     *                  ref="#/definitions/ERPAssetTransferDetail"
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
        /** @var ERPAssetTransferDetail $eRPAssetTransferDetail */
        $eRPAssetTransferDetail = $this->eRPAssetTransferDetailRepository->findWithoutFail($id);

        if (empty($eRPAssetTransferDetail)) {
            return $this->sendError(trans('custom.e_r_p_asset_transfer_detail_not_found'));
        }

        return $this->sendResponse($eRPAssetTransferDetail->toArray(), trans('custom.e_r_p_asset_transfer_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateERPAssetTransferDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/eRPAssetTransferDetails/{id}",
     *      summary="Update the specified ERPAssetTransferDetail in storage",
     *      tags={"ERPAssetTransferDetail"},
     *      description="Update ERPAssetTransferDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransferDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ERPAssetTransferDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ERPAssetTransferDetail")
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
     *                  ref="#/definitions/ERPAssetTransferDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateERPAssetTransferDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var ERPAssetTransferDetail $eRPAssetTransferDetail */
        $eRPAssetTransferDetail = $this->eRPAssetTransferDetailRepository->findWithoutFail($id);

        if (empty($eRPAssetTransferDetail)) {
            return $this->sendError(trans('custom.e_r_p_asset_transfer_detail_not_found'));
        }

        $eRPAssetTransferDetail = $this->eRPAssetTransferDetailRepository->update($input, $id);

        return $this->sendResponse($eRPAssetTransferDetail->toArray(), trans('custom.erpassettransferdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/eRPAssetTransferDetails/{id}",
     *      summary="Remove the specified ERPAssetTransferDetail from storage",
     *      tags={"ERPAssetTransferDetail"},
     *      description="Delete ERPAssetTransferDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ERPAssetTransferDetail",
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

        /** @var ERPAssetTransferDetail $eRPAssetTransferDetail */
        $eRPAssetTransferDetail = $this->eRPAssetTransferDetailRepository->findWithoutFail($id);

        if (empty($eRPAssetTransferDetail)) {
            return $this->sendError(trans('custom.asset_transfer_detail_not_found'));
        }
        $eRPAssetTransferDetail->delete();
        return $this->sendResponse($id, trans('custom.asset_transfer_detail_deleted_successfully'));
    }
    public function get_employee_asset_transfer_details(Request $request, $id)
    {
        $data['assetMaster'] = ERPAssetTransfer::where('id', $id)->first();

        if(!isset($data['assetMaster']))
        {
            return $this->sendError(trans('custom.asset_transfer_master_data_not_found'));
        }

        $assetTransferDetails = ERPAssetTransferDetail::where('erp_fa_fa_asset_transfer_id', $id);

        switch ($data['assetMaster']->type)
        {
            case 1 :
                $assetTransferDetails->with(['assetRequestDetail', 'assetMaster','assetRequestMaster','item_detail']);
                break;
            case 2 :
                $assetTransferDetails->with(['fromLocation', 'toLocation', 'assetMaster']);
                break;
            case 3 :
                $assetTransferDetails->with(['assetMaster','fromEmployee' => function($query) {
                    $query->select(['employeeSystemID','empFullName']);
                },'toEmployee' => function($query) {
                    $query->select(['employeeSystemID','empFullName']);
                }]);
                break;
            case 4 :
                $assetTransferDetails->with(['assetRequestDetail', 'assetMaster','assetRequestMaster','item_detail','department' => function ($d) {
                    $d->select(['departmentSystemID','DepartmentDescription','DepartmentID']);
                }]);
                break;
            default:
                break;
        }

        $data['assetRequestDetails'] = $assetTransferDetails->get();
        return $this->sendResponse($data, 'Asset Request Detail');
    }
    public function typeAheadAssetDrop(Request $request){
        $input = $request->all();
        $companyID = $input['companyID'];
        $search = $input['search'];
        $data['assetMaster_drop'] = FixedAssetMaster::where('companySystemID', $companyID)
                                                    ->where(function($query) use ($search) {
                                                        $query->where('faCode', 'like', "%{$search}%")
                                                              ->orWhere('assetDescription', 'like', "%{$search}%");
                                                    })
                                                    ->where('approved', -1)
                                                    ->where('DIPOSED', 0)
                                                    ->get();

        return $this->sendResponse($data, trans('custom.asset_request_drop_down_data_retrieved_successfull'));
    }

    public function assetTransferDrop(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];
        $data['assetMaster_drop'] = [];
        $data['location_drop'] = ErpLocation::select('locationID', 'locationName')->get();
        return $this->sendResponse($data, trans('custom.asset_request_drop_down_data_retrieved_successfull'));
    }
    public function addEmployeeAsset(Request $request, $id)
    {
        $input = $request->all();
        $valuesOfAsset = array_column($input, 'asset');
        $unique = array_unique($valuesOfAsset);
        $duplicates =  sizeof(array_diff_assoc($valuesOfAsset, $unique));
        if ($duplicates > 0) {
            return $this->sendError(trans('custom.same_asset_cannot_be_add_multiple_times'));
        }

        if (isset($input) && !empty($input)) {
            $x = 1;
            foreach ($input as $item => $value) {
                if($input[0]['isDirectToEmployee']) {
                    if ($value['from_emp'][0] == $value['to_emp']) {
                        return $this->sendError('Line No ' . $x . ' From Location And To Location Cannot be same');
                    } else {
    
                        $data[] = [
                            'erp_fa_fa_asset_transfer_id' => $id,
                            'from_emp_id' => $value['from_emp'][0],
                            'to_emp_id' => $value['to_emp'],
                            'fa_master_id' => $value['asset'],
                            'company_id' => $value['companySystemID'],
                            'from_location_id' => NULL,
                            'to_location_id' => NULL,
                            'created_user_id' => \Helper::getEmployeeSystemID(),
                        ];
    
    
                        // FixedAssetMaster::find($value['asset'])
                        //     ->update([
                        //         'empID' =>  $value['to_emp']
                        //     ]);
                    }
                }else {
                    if ($value['from_location'][0] == $value['to_location']) {
                        return $this->sendError('Line No ' . $x . ' From Location And To Location Cannot be same');
                    } else {
    
                        $data[] = [
                            'erp_fa_fa_asset_transfer_id' => $id,
                            'from_location_id' => $value['from_location'][0],
                            'to_location_id' => isset($value['to_location'][0]) ? $value['to_location'][0]:$value['to_location'],
                            'fa_master_id' => $value['asset'],
                            'company_id' => $value['companySystemID'],
                            'created_user_id' => \Helper::getEmployeeSystemID(),
                            'from_emp_id' => NULL,
                            'to_emp_id' => NULL,
                        ];
    
    
                        // FixedAssetMaster::find($value['asset'])
                        //     ->update([
                        //         'LOCATION' =>  isset($value['to_location'][0]) ? $value['to_location'][0]:$value['to_location']
                        //     ]);
                    }
                }
                
                $x++;
            }
            $this->eRPAssetTransferDetailRepository->insert($data);
            return $this->sendResponse(['id' => $id], trans('custom.asset_transfer_detail_saved_successfully'));
        }
    }
    public function getAssetTransferDetails(Request $request)
    {
        $id = $request['id'];
        $companyID = $request['companyId'];

        return $this->sendResponse($this->getAssetTransfer($id, $companyID), 'Asset Request data');
    }

    public function printERPAssetTransfer(Request $request)
    {

        $id = $request->get('transferID');
        $companyID = $request->get('companyID');
        $AssetTransferMaster = ERPAssetTransfer::find($id);
        if (empty($AssetTransferMaster)) {
            return $this->sendError(trans('custom.asset_transfer_not_found'));
        }
        $transferDetails = $this->getAssetTransfer($id, $companyID);


        $time = strtotime("now");
        $fileName = 'asset_transfer.blade' . $id . '_' . $time . '.pdf';
        $html = view('print.asset_transfer', $transferDetails);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
    }

    public function getAssetTransfer($id, $companyID)
    {

        $data['assetTransferMaster'] = ERPAssetTransfer::with([
            'company', 'confirmed_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('rejectedYN', 0)
                    ->where('documentSystemID', 103);
            }
        ])->where('company_id', $companyID)->where('id', $id)->first();
        if ($data['assetTransferMaster']->type == 1) {
            $data['assetTransferDetail'] = ERPAssetTransferDetail::with(['assetRequestDetail', 'assetMaster'])->where('company_id', $companyID)
                ->where('erp_fa_fa_asset_transfer_id', $id)->get();
        } else {
            $data['assetTransferDetail'] = ERPAssetTransferDetail::with(['fromLocation', 'toLocation', 'assetMaster','fromEmployee' => function($query) {
                $query->select(['employeeSystemID','empFullName']);
            },'toEmployee' => function($query) {
                $query->select(['employeeSystemID','empFullName']);
            }])->where('company_id', $companyID)
                ->where('erp_fa_fa_asset_transfer_id', $id)->get();
        }


        return $data;
    }
    public function assetTransferDetailAsset(Request $request)
    {

        $eRPAssetTransferDetail = $this->eRPAssetTransferDetailRepository->findWithoutFail($request['id']);
        $erpAsset = ERPAssetTransferDetail::select('erp_fa_fa_asset_transfer_id')->where('erp_fa_fa_asset_transfer_id', $eRPAssetTransferDetail->erp_fa_fa_asset_transfer_id)
            ->where('fa_master_id', $request['value'])->get();

        if (count($erpAsset) > 0) {
            return $this->sendError(trans('custom.same_asset_cannot_be_link_multiple_times'));
        }

        $updateData = [
            'fa_master_id' =>  $request['value']
        ];

        ERPAssetTransferDetail::where('id', $request['id'])
            ->update($updateData);

        return $this->sendResponse(['id' => $request['value']], trans('custom.asset_transfer_detail_saved_successfully'));
    }
    public function getAssetLocationValue(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];
        $assetID = $input['assetID'];
        $data['assetLocation'] = FixedAssetMaster::select('faID', 'LOCATION')
            ->where('faID', $assetID)
            ->where('companySystemID', $companyID)->first();

        return $data;
    }
    public function getAssetEmployeeValue(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];
        $assetID = $input['assetID'];
        $data['assetLocation'] = FixedAssetMaster::with('assignedEmployee')
            ->where('faID', $assetID)
            ->where('companySystemID', $companyID)->first();

        return $data;
    }

    public function getDepartmentList(Request $request) {
        $departments = DepartmentMaster::select(['departmentSystemID','DepartmentDescription'])->showInCombo()->where('isActive',true)->get();
        return $this->sendResponse($departments, 'Department data reterived');

    }

    public function getDepartmentOfAsset(Request $request) {
        $input = $request->all();
        $deparment = FixedAssetMaster::select('departmentSystemID')->where('faID',$input['assetID'])->with(['departmentmaster' => function ($q) {
            $q->select(['DepartmentDescription','departmentSystemID']);
        }])->first();
        return $this->sendResponse($deparment, 'Department reterived');
    }

    public function getCurrentAssigneeOfAsset(Request $request) {
        $input = $request->all();
        $employee = FixedAssetMaster::where('faID',$input['assetID'])->with(['assignedEmployee'])->first();
        return $this->sendResponse($employee, 'Assignee reterived');
    }

    public function UpdateReturnStatus(Request $request) {

        if($request->input('assetTrasnferDetailID')) {
            $assetTransferDetail = ERPAssetTransferDetail::find($request->input('assetTrasnferDetailID'));
            if($assetTransferDetail) {
                $payAssetsObj = SMEPayAsset::where('Erp_faID',$assetTransferDetail->fa_master_id)->update(['returnStatus' => $request->input('status')]);
            }

        }else {
            $items = $request->input('items');

            foreach($items as $item) {
                $payAssetsObj = SMEPayAsset::where('Erp_faID',$item['assetDropTransferID'])->update(['returnStatus' => $request->input('status')]);
            }
    
        }


        return $this->sendResponse([], 'data reterived');

    }
}
