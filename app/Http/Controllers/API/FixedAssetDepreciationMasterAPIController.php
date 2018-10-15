<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetDepreciationMasterAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetDepreciationMasterAPIRequest;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetDepreciationMasterController
 * @package App\Http\Controllers\API
 */
class FixedAssetDepreciationMasterAPIController extends AppBaseController
{
    /** @var  FixedAssetDepreciationMasterRepository */
    private $fixedAssetDepreciationMasterRepository;

    public function __construct(FixedAssetDepreciationMasterRepository $fixedAssetDepreciationMasterRepo)
    {
        $this->fixedAssetDepreciationMasterRepository = $fixedAssetDepreciationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationMasters",
     *      summary="Get a listing of the FixedAssetDepreciationMasters.",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Get all FixedAssetDepreciationMasters",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetDepreciationMaster")
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
        $this->fixedAssetDepreciationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetDepreciationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->all();

        return $this->sendResponse($fixedAssetDepreciationMasters->toArray(), 'Fixed Asset Depreciation Masters retrieved successfully');
    }

    /**
     * @param CreateFixedAssetDepreciationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetDepreciationMasters",
     *      summary="Store a newly created FixedAssetDepreciationMaster in storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Store FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationMaster")
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetDepreciationMasterAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->create($input);

        return $this->sendResponse($fixedAssetDepreciationMasters->toArray(), 'Fixed Asset Depreciation Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Display the specified FixedAssetDepreciationMaster",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Get FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
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
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'Fixed Asset Depreciation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetDepreciationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Update the specified FixedAssetDepreciationMaster in storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Update FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationMaster")
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetDepreciationMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->update($input, $id);

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'FixedAssetDepreciationMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Remove the specified FixedAssetDepreciationMaster from storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Delete FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
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
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        $fixedAssetDepreciationMaster->delete();

        return $this->sendResponse($id, 'Fixed Asset Depreciation Master deleted successfully');
    }

    public function getAllDepreciationByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved'));

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

        $assetCositng = FixedAssetDepreciationMaster::ofCompany($subCompanies);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCositng->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCositng->where('approved', $input['approved']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('depCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getDepreciationFormData(Request $request)
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

        $companyCurrency = \Helper::companyCurrency($companyId);

        $output = array(
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'companyCurrency' => $companyCurrency,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function assetDepreciationByID($id)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);
        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        $detail = FixedAssetDepreciationPeriod::with(['maincategory_by', 'financecategory_by', 'serviceline_by'])->ofDepreciation($id)->get();

        $output = ['master' => $fixedAssetDepreciationMaster, 'detail' => $detail];

        return $this->sendResponse($output, 'Fixed Asset Master retrieved successfully');
    }
}
