<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetRequestDetailAPIRequest;
use App\Http\Requests\API\UpdateAssetRequestDetailAPIRequest;
use App\Models\AssetRequestDetail;
use App\Repositories\AssetRequestDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\AssetRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\FixedAssetMaster;
use App\Models\DepartmentMaster;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetRequestDetailController
 * @package App\Http\Controllers\API
 */

class AssetRequestDetailAPIController extends AppBaseController
{
    /** @var  AssetRequestDetailRepository */
    private $assetRequestDetailRepository;

    public function __construct(AssetRequestDetailRepository $assetRequestDetailRepo)
    {
        $this->assetRequestDetailRepository = $assetRequestDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetRequestDetails",
     *      summary="Get a listing of the AssetRequestDetails.",
     *      tags={"AssetRequestDetail"},
     *      description="Get all AssetRequestDetails",
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
     *                  @SWG\Items(ref="#/definitions/AssetRequestDetail")
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
        $this->assetRequestDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->assetRequestDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetRequestDetails = $this->assetRequestDetailRepository->all();

        return $this->sendResponse($assetRequestDetails->toArray(), 'Asset Request Details retrieved successfully');
    }

    /**
     * @param CreateAssetRequestDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetRequestDetails",
     *      summary="Store a newly created AssetRequestDetail in storage",
     *      tags={"AssetRequestDetail"},
     *      description="Store AssetRequestDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetRequestDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetRequestDetail")
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
     *                  ref="#/definitions/AssetRequestDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetRequestDetailAPIRequest $request)
    {
        $input = $request->all();

        $assetRequestDetail = $this->assetRequestDetailRepository->create($input);

        return $this->sendResponse($assetRequestDetail->toArray(), 'Asset Request Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetRequestDetails/{id}",
     *      summary="Display the specified AssetRequestDetail",
     *      tags={"AssetRequestDetail"},
     *      description="Get AssetRequestDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetRequestDetail",
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
     *                  ref="#/definitions/AssetRequestDetail"
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
        /** @var AssetRequestDetail $assetRequestDetail */
        $assetRequestDetail = $this->assetRequestDetailRepository->findWithoutFail($id);

        if (empty($assetRequestDetail)) {
            return $this->sendError('Asset Request Detail not found');
        }

        return $this->sendResponse($assetRequestDetail->toArray(), 'Asset Request Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetRequestDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetRequestDetails/{id}",
     *      summary="Update the specified AssetRequestDetail in storage",
     *      tags={"AssetRequestDetail"},
     *      description="Update AssetRequestDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetRequestDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetRequestDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetRequestDetail")
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
     *                  ref="#/definitions/AssetRequestDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetRequestDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetRequestDetail $assetRequestDetail */
        $assetRequestDetail = $this->assetRequestDetailRepository->findWithoutFail($id);

        if (empty($assetRequestDetail)) {
            return $this->sendError('Asset Request Detail not found');
        }

        $assetRequestDetail = $this->assetRequestDetailRepository->update($input, $id);

        return $this->sendResponse($assetRequestDetail->toArray(), 'AssetRequestDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetRequestDetails/{id}",
     *      summary="Remove the specified AssetRequestDetail from storage",
     *      tags={"AssetRequestDetail"},
     *      description="Delete AssetRequestDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetRequestDetail",
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
        /** @var AssetRequestDetail $assetRequestDetail */
        $assetRequestDetail = $this->assetRequestDetailRepository->findWithoutFail($id);

        if (empty($assetRequestDetail)) {
            return $this->sendError('Asset Request Detail not found');
        }

        $assetRequestDetail->delete();

        return $this->sendSuccess('Asset Request Detail deleted successfully');
    }
    public function getAssetRequestDetails(Request $request)
    {
        $id = $request['id'];
        $companyID = $request['companyId']; 
        $data['assetRequestDetail'] = AssetRequestDetail::where('company_id', $companyID)->where('erp_fa_fa_asset_request_id', $id)->get();
        $data['assetRequestMaster'] = AssetRequest::with(['company','confirmed_by','approved_by','employee'])->where('company_id', $companyID)->where('id', $id)->first();
        return $this->sendResponse($data, 'Asset Request data');
    }
    public function getAssetRequestMaster(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyId'];

        $typeCondtion = '';
        if($input['type'] == 1) {
            $typeCondtion = ' (type = 1 OR type IS NULL) AND ';
        }

        if($input['type'] == 4) {
            $typeCondtion = ' type = 2 AND ';
        }

        $query = "SELECT id, document_code, (requesedQty.qtyRequested - IFNULL(transferedQTY.transferedQty,0)) as qty 
        FROM erp_fa_fa_asset_request
        LEFT JOIN 
        (SELECT erp_fa_fa_asset_request_id as reqMasterID, SUM(qty) as qtyRequested FROM `erp_fa_fa_asset_request_details` WHERE request_company_id = $companyID 
        GROUP BY erp_fa_fa_asset_request_id  ) as requesedQty ON requesedQty.reqMasterID = erp_fa_fa_asset_request.id 
        LEFT JOIN (SELECT erp_fa_fa_asset_request_id AS MasterID, COUNT( id ) AS transferedQty FROM `erp_fa_fa_asset_transfer_details` WHERE company_id = $companyID  GROUP BY erp_fa_fa_asset_request_id) as transferedQTY ON transferedQTY.MasterID = erp_fa_fa_asset_request.id 
        WHERE $typeCondtion  approved_yn = 1 AND request_company_id = $companyID  HAVING qty > 0";
        $assetRequestMaster =DB::select($query);

         /* AssetRequest::where('company_id', $companyID)->where('approved_yn', 1)->get(); */
        return $this->sendResponse($assetRequestMaster, 'Asset request master data retrieved successfully');
    }
    public function getAssetRequestDetailSelected(Request $request){ 
        $input = $request->all();
        $companyID = $input['companyId'];
        $assetRequestMasterID = $input['AssetRequestMasterID'];

        //$assetRequestDetail = AssetRequestDetail::where('company_id', $companyID)->where('erp_fa_fa_asset_request_id', $assetRequestMasterID)->get();
       $assetRequest = AssetRequest::find($assetRequestMasterID);
       $assetRequestDetail = DB::select("SELECT erp_fa_fa_asset_request_details.*, (qty - IFNULL(qtyTransfer,0) ) as qty FROM `erp_fa_fa_asset_request_details` 
        LEFT JOIN (SELECT erp_fa_fa_asset_request_detail_id as requestDetailID, count(id) as qtyTransfer FROM `erp_fa_fa_asset_transfer_details` 
        WHERE erp_fa_fa_asset_request_id = $assetRequestMasterID
        GROUP BY erp_fa_fa_asset_request_detail_id) transferedQty ON transferedQty.requestDetailID = erp_fa_fa_asset_request_details.id
        WHERE request_company_id = $companyID AND erp_fa_fa_asset_request_id = $assetRequestMasterID HAVING qty > 0");

        $allowItemToTypePolicy = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 75)
                                            ->where('companySystemID', $companyID)
                                            ->first();

        if ($allowItemToType) {
            if ($allowItemToType->isYesNO) {
                $allowItemToTypePolicy = true;
            }
        }
        $department = null;
        if(isset($assetRequest->departmentSystemID)) {
            $department = DepartmentMaster::where('departmentSystemID',$assetRequest->departmentSystemID)->first();
        }


        $data = [
            'assetRequestDetail'=> $assetRequestDetail,
            'allowItemToTypePolicy'=> $allowItemToTypePolicy,
            'department' => $department
        ];
        return $this->sendResponse($data, 'Asset request detail data retrieved successfully');
    }
    public function getAssetDropData(Request $request){ 
        $input = $request->all();
        $companyID = $input['companyId'];
        $assetMaster = FixedAssetMaster::where('companySystemID',$companyID)
        ->where('approved',-1)
        ->where('selectedForDisposal',0)
        ->where('DIPOSED',0)
        ->get();
        return $this->sendResponse($assetMaster->toArray(), 'Asset master data retrieved successfully');
    }
   
}
