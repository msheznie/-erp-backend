<?php
/**
 * =============================================
 * -- File Name : AssetCapitalizationAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 09 - September 2018
 * -- Description : This file contains the all CRUD for Asset Capitalization
 * -- REVISION HISTORY
 * -- Date: 03-September 2018 By:Mubashir Description: Added new functions named as getAllCapitalizationByCompany(),getAllCapitalizationByCompany()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetCapitalizationAPIRequest;
use App\Http\Requests\API\UpdateAssetCapitalizationAPIRequest;
use App\Models\AssetCapitalization;
use App\Models\CurrencyMaster;
use App\Models\FixedAssetCategory;
use App\Models\Months;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\AssetCapitalizationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetCapitalizationController
 * @package App\Http\Controllers\API
 */

class AssetCapitalizationAPIController extends AppBaseController
{
    /** @var  AssetCapitalizationRepository */
    private $assetCapitalizationRepository;

    public function __construct(AssetCapitalizationRepository $assetCapitalizationRepo)
    {
        $this->assetCapitalizationRepository = $assetCapitalizationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizations",
     *      summary="Get a listing of the AssetCapitalizations.",
     *      tags={"AssetCapitalization"},
     *      description="Get all AssetCapitalizations",
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
     *                  @SWG\Items(ref="#/definitions/AssetCapitalization")
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
        $this->assetCapitalizationRepository->pushCriteria(new RequestCriteria($request));
        $this->assetCapitalizationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetCapitalizations = $this->assetCapitalizationRepository->all();

        return $this->sendResponse($assetCapitalizations->toArray(), 'Asset Capitalizations retrieved successfully');
    }

    /**
     * @param CreateAssetCapitalizationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetCapitalizations",
     *      summary="Store a newly created AssetCapitalization in storage",
     *      tags={"AssetCapitalization"},
     *      description="Store AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalization that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalization")
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
     *                  ref="#/definitions/AssetCapitalization"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetCapitalizationAPIRequest $request)
    {
        $input = $request->all();

        $assetCapitalizations = $this->assetCapitalizationRepository->create($input);

        return $this->sendResponse($assetCapitalizations->toArray(), 'Asset Capitalization saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizations/{id}",
     *      summary="Display the specified AssetCapitalization",
     *      tags={"AssetCapitalization"},
     *      description="Get AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalization",
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
     *                  ref="#/definitions/AssetCapitalization"
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
        /** @var AssetCapitalization $assetCapitalization */
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            return $this->sendError('Asset Capitalization not found');
        }

        return $this->sendResponse($assetCapitalization->toArray(), 'Asset Capitalization retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetCapitalizationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetCapitalizations/{id}",
     *      summary="Update the specified AssetCapitalization in storage",
     *      tags={"AssetCapitalization"},
     *      description="Update AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalization",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalization that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalization")
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
     *                  ref="#/definitions/AssetCapitalization"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetCapitalizationAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetCapitalization $assetCapitalization */
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            return $this->sendError('Asset Capitalization not found');
        }

        $assetCapitalization = $this->assetCapitalizationRepository->update($input, $id);

        return $this->sendResponse($assetCapitalization->toArray(), 'AssetCapitalization updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetCapitalizations/{id}",
     *      summary="Remove the specified AssetCapitalization from storage",
     *      tags={"AssetCapitalization"},
     *      description="Delete AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalization",
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
        /** @var AssetCapitalization $assetCapitalization */
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            return $this->sendError('Asset Capitalization not found');
        }

        $assetCapitalization->delete();

        return $this->sendResponse($id, 'Asset Capitalization deleted successfully');
    }

    public function getCapitalizationFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = AssetCapitalization::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $assetCategoryType = FixedAssetCategory::all();

        $output = array(
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'assetCategoryType' => $assetCategoryType,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function getAllCapitalizationByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month','year','cancelYN','confirmedYN','approved','allocationTypeID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetCapitalization = AssetCapitalization::with(['created_by'])->whereIN('companySystemID', $subCompanies);

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $assetCapitalization->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCapitalization->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCapitalization->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $assetCapitalization->whereMonth('BPVdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $assetCapitalization->whereYear('BPVdate', '=', $input['year']);
            }
        }

        if (array_key_exists('allocationTypeID', $input)) {
            if ($input['allocationTypeID'] && !is_null($input['allocationTypeID'])) {
                $assetCapitalization->where('allocationTypeID', $input['allocationTypeID']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCapitalization = $assetCapitalization->where(function ($query) use ($search) {
                $query->where('capitalizationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCapitalization)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('capitalizationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }


}
