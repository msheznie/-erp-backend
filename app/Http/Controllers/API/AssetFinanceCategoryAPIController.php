<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateAssetFinanceCategoryAPIRequest;
use App\Http\Requests\API\UpdateAssetFinanceCategoryAPIRequest;
use App\Models\AssetFinanceCategory;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\YesNoSelection;
use App\Repositories\AssetFinanceCategoryRepository;
use App\Scopes\ActiveScope;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetFinanceCategoryController
 * @package App\Http\Controllers\API
 */

class AssetFinanceCategoryAPIController extends AppBaseController
{
    /** @var  AssetFinanceCategoryRepository */
    private $assetFinanceCategoryRepository;

    public function __construct(AssetFinanceCategoryRepository $assetFinanceCategoryRepo)
    {
        $this->assetFinanceCategoryRepository = $assetFinanceCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetFinanceCategories",
     *      summary="Get a listing of the AssetFinanceCategories.",
     *      tags={"AssetFinanceCategory"},
     *      description="Get all AssetFinanceCategories",
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
     *                  @SWG\Items(ref="#/definitions/AssetFinanceCategory")
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
        $this->assetFinanceCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->assetFinanceCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetFinanceCategories = $this->assetFinanceCategoryRepository->all();

        return $this->sendResponse($assetFinanceCategories->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_finance_categories')]));
    }

    /**
     * @param CreateAssetFinanceCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetFinanceCategories",
     *      summary="Store a newly created AssetFinanceCategory in storage",
     *      tags={"AssetFinanceCategory"},
     *      description="Store AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetFinanceCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetFinanceCategory")
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
     *                  ref="#/definitions/AssetFinanceCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetFinanceCategoryAPIRequest $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'COSTGLCODESystemID' => 'required|numeric|min:1',
            'ACCDEPGLCODESystemID' => 'required|numeric|min:1',
            'DEPGLCODESystemID' => 'required|numeric|min:1',
            'DISPOGLCODESystemID' => 'required|numeric|min:1',
            'financeCatDescription' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['COSTGLCODE'] = $this->getAccountCode($input['COSTGLCODESystemID']);
        $input['ACCDEPGLCODE'] = $this->getAccountCode($input['ACCDEPGLCODESystemID']);
        $input['DEPGLCODE'] = $this->getAccountCode($input['DEPGLCODESystemID']);
        $input['DISPOGLCODE'] = $this->getAccountCode($input['DISPOGLCODESystemID']);

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = Helper::getEmployeeID();
        $input['sortOrder'] = AssetFinanceCategory::max('sortOrder') + 1;
        $assetFinanceCategories = $this->assetFinanceCategoryRepository->create($input);

        return $this->sendResponse($assetFinanceCategories->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_finance_categories')]));
    }

    private function getAccountCode($id){

        $data = ChartOfAccount::find($id);
        if(empty($data)){
            return '';
        }

        return $data->AccountCode;
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetFinanceCategories/{id}",
     *      summary="Display the specified AssetFinanceCategory",
     *      tags={"AssetFinanceCategory"},
     *      description="Get AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetFinanceCategory",
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
     *                  ref="#/definitions/AssetFinanceCategory"
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
        /** @var AssetFinanceCategory $assetFinanceCategory */
        $assetFinanceCategory = AssetFinanceCategory::withoutGlobalScope(ActiveScope::class)->findOrFail($id);

        if (empty($assetFinanceCategory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_finance_categories')]));
        }

        return $this->sendResponse($assetFinanceCategory->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_finance_categories')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetFinanceCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetFinanceCategories/{id}",
     *      summary="Update the specified AssetFinanceCategory in storage",
     *      tags={"AssetFinanceCategory"},
     *      description="Update AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetFinanceCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetFinanceCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetFinanceCategory")
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
     *                  ref="#/definitions/AssetFinanceCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetFinanceCategoryAPIRequest $request)
    {
        $input = $request->all();
        $formula = isset($input['formula']) ? $input['formula'] : null;
        $input = $this->convertArrayToValue($input);

        $input['faFinanceCatID'] = isset($input['faFinanceCatID'])?$input['faFinanceCatID']:0;
        /** @var AssetFinanceCategory $assetFinanceCategory */
        $assetFinanceCategory = AssetFinanceCategory::withoutGlobalScope(ActiveScope::class)->findOrFail($input['faFinanceCatID']);

        if (empty($assetFinanceCategory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_finance_categories')]));
        }


        if (!is_null($formula)) {
            if (is_array(($formula))) {
                if ($formula) {
                    $input['formula'] = implode('~', $formula);
                } else {
                    $input['formula'] = null;
                }
            }
        }

        $validator = \Validator::make($input, [
            'COSTGLCODESystemID' => 'required|numeric|min:1',
            'ACCDEPGLCODESystemID' => 'required|numeric|min:1',
            'DEPGLCODESystemID' => 'required|numeric|min:1',
            'DISPOGLCODESystemID' => 'required|numeric|min:1',
            'financeCatDescription' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['COSTGLCODE'] = $this->getAccountCode($input['COSTGLCODESystemID']);
        $input['ACCDEPGLCODE'] = $this->getAccountCode($input['ACCDEPGLCODESystemID']);
        $input['DEPGLCODE'] = $this->getAccountCode($input['DEPGLCODESystemID']);
        $input['DISPOGLCODE'] = $this->getAccountCode($input['DISPOGLCODESystemID']);

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = Helper::getEmployeeID();

        if (isset($input['Actions'])) {
            unset($input['Actions']);
        }

        if (isset($input['DT_Row_Index'])) {
            unset($input['DT_Row_Index']);
        }
        
        $assetFinanceCategory = AssetFinanceCategory::withoutGlobalScope(ActiveScope::class)->where('faFinanceCatID',$input['faFinanceCatID'])->update($input);

        return $this->sendResponse($assetFinanceCategory, trans('custom.update', ['attribute' => trans('custom.asset_finance_categories')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetFinanceCategories/{id}",
     *      summary="Remove the specified AssetFinanceCategory from storage",
     *      tags={"AssetFinanceCategory"},
     *      description="Delete AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetFinanceCategory",
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
        /** @var AssetFinanceCategory $assetFinanceCategory */
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_finance_categories')]));
        }

        $assetFinanceCategory->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_finance_categories')]));
    }

    public function getAllAssetFinanceCategory(Request $request){

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assetFinanceCategories = AssetFinanceCategory::withoutGlobalScope(ActiveScope::class)
                                                       ->orderBy('faFinanceCatID',$sort);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetFinanceCategories = $assetFinanceCategories->where(function ($query) use ($search) {
                $query->where('financeCatDescription', 'LIKE', "%{$search}%")
                    ->orWhere('COSTGLCODE', 'LIKE', "%{$search}%")
                    ->orWhere('ACCDEPGLCODE', 'LIKE', "%{$search}%")
                    ->orWhere('DEPGLCODE', 'LIKE', "%{$search}%")
                    ->orWhere('DISPOGLCODE', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetFinanceCategories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }

    public function getAssetFinanceCategoryFormData(Request $request)
    {
        $companyId = $request->get('selectedCompanyId');
        $yesNoSelection = YesNoSelection::selectRaw('idyesNoselection as value,YesNo as label')->get();


        $chartOfAccounts = ChartOfAccountsAssigned::where('companySystemID',$companyId)
                                                    ->selectRaw('chartOfAccountSystemID as value,CONCAT(AccountCode, " | " ,AccountDescription) as label')
                                                    ->get();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccounts' => $chartOfAccounts,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

}
