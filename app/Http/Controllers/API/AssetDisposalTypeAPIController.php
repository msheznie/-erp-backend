<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateAssetDisposalTypeAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalTypeAPIRequest;
use App\Models\AssetDisposalType;
use App\Models\ChartOfAccount;
use App\Repositories\AssetDisposalTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\AuditLogsTrait;

/**
 * Class AssetDisposalTypeController
 * @package App\Http\Controllers\API
 */

class AssetDisposalTypeAPIController extends AppBaseController
{
    /** @var  AssetDisposalTypeRepository */
    private $assetDisposalTypeRepository;
    use AuditLogsTrait;

    public function __construct(AssetDisposalTypeRepository $assetDisposalTypeRepo)
    {
        $this->assetDisposalTypeRepository = $assetDisposalTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalTypes",
     *      summary="Get a listing of the AssetDisposalTypes.",
     *      tags={"AssetDisposalType"},
     *      description="Get all AssetDisposalTypes",
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
     *                  @SWG\Items(ref="#/definitions/AssetDisposalType")
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
        $this->assetDisposalTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDisposalTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDisposalTypes = $this->assetDisposalTypeRepository->all();

        return $this->sendResponse($assetDisposalTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_types')]));
    }

    /**
     * @param CreateAssetDisposalTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDisposalTypes",
     *      summary="Store a newly created AssetDisposalType in storage",
     *      tags={"AssetDisposalType"},
     *      description="Store AssetDisposalType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalType")
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
     *                  ref="#/definitions/AssetDisposalType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDisposalTypeAPIRequest $request)
    {
        $input = $request->all();

        $assetDisposalTypes = $this->assetDisposalTypeRepository->create($input);

        return $this->sendResponse($assetDisposalTypes->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_disposal_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalTypes/{id}",
     *      summary="Display the specified AssetDisposalType",
     *      tags={"AssetDisposalType"},
     *      description="Get AssetDisposalType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalType",
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
     *                  ref="#/definitions/AssetDisposalType"
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
        /** @var AssetDisposalType $assetDisposalType */
        $assetDisposalType = $this->assetDisposalTypeRepository
            ->with('chartofaccount:chartOfAccountSystemID,AccountCode,AccountDescription')
            ->findWithoutFail($id);

        if (empty($assetDisposalType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_types')]));
        }

        return $this->sendResponse($assetDisposalType->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_types')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetDisposalTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDisposalTypes/{id}",
     *      summary="Update the specified AssetDisposalType in storage",
     *      tags={"AssetDisposalType"},
     *      description="Update AssetDisposalType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalType")
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
     *                  ref="#/definitions/AssetDisposalType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDisposalTypeAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var AssetDisposalType $assetDisposalType */
        $assetDisposalType = $this->assetDisposalTypeRepository->findWithoutFail($id);

        if (empty($assetDisposalType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_types')]));
        }

        $input['glCode'] = ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountID'])->value('AccountCode');
        $input['updated_by'] = Helper::getEmployeeInfo()->employeeSystemID;

        $uuid = $input['tenant_uuid'] ?? 'local';
        $db = $input['db'] ?? '';

        if(isset($input['tenant_uuid']) ){
            unset($input['tenant_uuid']);
        }

        if(isset($input['db']) ){
            unset($input['db']);
        }

        $previousValue = $assetDisposalType->toArray();
        $newValue = $input;
        $transactionID = 0;

        $assetDisposalType = $this->assetDisposalTypeRepository->update($input, $id);

        $this->auditLog($db, $transactionID, $uuid, "chart_of_account_config", "{$input['departmentName']} - {$assetDisposalType->typeDescription} has updated", "U", $newValue, $previousValue);

        return $this->sendResponse($assetDisposalType->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_disposal_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDisposalTypes/{id}",
     *      summary="Remove the specified AssetDisposalType from storage",
     *      tags={"AssetDisposalType"},
     *      description="Delete AssetDisposalType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalType",
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
        /** @var AssetDisposalType $assetDisposalType */
        $assetDisposalType = $this->assetDisposalTypeRepository->findWithoutFail($id);

        if (empty($assetDisposalType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_types')]));
        }

        $assetDisposalType->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_disposal_types')]));
    }

    function config_list(Request $request){
        $input = $request->all();

        $sort = Helper::dataTableSortOrder($input);
        $search = $request->input('search.value');

        $qry = $this->assetDisposalTypeRepository->fetch_data($search);
        return DataTables::eloquent($qry)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('disposalTypesID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
