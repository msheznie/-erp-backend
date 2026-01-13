<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMolContributionAPIRequest;
use App\Http\Requests\API\UpdateMolContributionAPIRequest;
use App\Models\MolContribution;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\SupplierAssigned;
use App\Models\BookInvSuppMaster;
use App\Repositories\MolContributionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class MolContributionAPIController extends AppBaseController
{
    /** @var  MolContributionRepository */
    private $molContributionRepository;

    public function __construct(MolContributionRepository $molContributionRepo)
    {
        $this->molContributionRepository = $molContributionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/molContributions",
     *      summary="getMolContributionList",
     *      tags={"MolContribution"},
     *      description="Get all MolContributions",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/MolContribution")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->molContributionRepository->pushCriteria(new RequestCriteria($request));
        $this->molContributionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $molContributions = $this->molContributionRepository->all();

        return $this->sendResponse($molContributions->toArray(), 'Mol Contributions retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/molContributions",
     *      summary="createMolContribution",
     *      tags={"MolContribution"},
     *      description="Create MolContribution",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/MolContribution"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMolContributionAPIRequest $request)
    {
        $input = $request->all();

        $existingMol = MolContribution::where('company_id', $input['company_id'])
            ->where('contribution_type', $input['contribution_type'])
            ->where('mol_calculation_type_id', $input['mol_calculation_type_id'])
            ->exists();

        if ($existingMol) {
            return $this->sendError(trans('custom.mol_setup_duplicate_combination'));
        }

        if (isset($input['status']) && ($input['status'] == 1 || $input['status'] === true)) {
            MolContribution::where('company_id', $input['company_id'])
                ->where('status', 1)
                ->update(['status' => 0]);
        }

        $molContribution = $this->molContributionRepository->create($input);

        return $this->sendResponse($molContribution->toArray(), 'Mol Contribution saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/molContributions/{id}",
     *      summary="getMolContributionItem",
     *      tags={"MolContribution"},
     *      description="Get MolContribution",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MolContribution",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/MolContribution"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var MolContribution $molContribution */
        $molContribution = $this->molContributionRepository->findWithoutFail($id);

        if (empty($molContribution)) {
            return $this->sendError('Mol Contribution not found');
        }

        return $this->sendResponse($molContribution->toArray(), 'Mol Contribution retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/molContributions/{id}",
     *      summary="updateMolContribution",
     *      tags={"MolContribution"},
     *      description="Update MolContribution",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MolContribution",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/MolContribution"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMolContributionAPIRequest $request)
    {
        $input = $request->all();

        /** @var MolContribution $molContribution */
        $molContribution = $this->molContributionRepository->findWithoutFail($id);

        if (empty($molContribution)) {
            return $this->sendError('Mol Contribution not found');
        }

        $companyId = $input['company_id'] ?? $molContribution->company_id;
        $contributionType = $input['contribution_type'] ?? $molContribution->contribution_type;
        $molCalculationTypeId = $input['mol_calculation_type_id'] ?? $molContribution->mol_calculation_type_id;

        $existingMol = MolContribution::where('company_id', $companyId)
            ->where('contribution_type', $contributionType)
            ->where('mol_calculation_type_id', $molCalculationTypeId)
            ->where('id', '!=', $id)
            ->exists();

        if ($existingMol) {
            return $this->sendError(trans('custom.mol_setup_duplicate_combination'));
        }

        if (isset($input['status']) && ($input['status'] == 0 || $input['status'] === false)) {
            $hasTransactions = BookInvSuppMaster::where('mol_setup_id', $id)->exists();
            if ($hasTransactions) {
                return $this->sendError(trans('custom.mol_setup_cannot_be_deactivated'));
            }
        }

        if (isset($input['status']) && ($input['status'] == 1 || $input['status'] === true)) {
            MolContribution::where('company_id', $companyId)
                ->where('id', '!=', $id)
                ->where('status', 1)
                ->update(['status' => 0]);
        }

        $molContribution = $this->molContributionRepository->update($input, $id);

        return $this->sendResponse($molContribution->toArray(), 'MolContribution updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/molContributions/{id}",
     *      summary="deleteMolContribution",
     *      tags={"MolContribution"},
     *      description="Delete MolContribution",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MolContribution",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var MolContribution $molContribution */
        $molContribution = $this->molContributionRepository->findWithoutFail($id);

        if (empty($molContribution)) {
            return $this->sendError('Mol Contribution not found');
        }

        $hasTransactions = BookInvSuppMaster::where('mol_setup_id', $id)->exists();
        if ($hasTransactions) {
            return $this->sendError(trans('custom.mol_setup_cannot_be_deactivated'));
        }

        $molContribution->delete();

        return $this->sendResponse($id, 'Mol Contribution deleted successfully');
    }

    public function getContributionsDatatable(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('selectedCompanyID'));
        $molContributions = MolContribution::with(['authority', 'company', 'molExpenseGlAccount']);
        $companiesByGroup = "";

        if (array_key_exists('selectedCompanyID', $input)) {
            $molContributions = $molContributions->where('company_id', $input["selectedCompanyID"]);
        } else {
            if(array_key_exists ('selectedCompanyID' , $input)){
                if($input['selectedCompanyID'] > 0){
                    $molContributions = $molContributions->where('company_id', $input['selectedCompanyID']);
                }
            }else {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $companiesByGroup = $input['globalCompanyId'];
                    $molContributions = $molContributions->where('company_id', $companiesByGroup);
                } else {
                    $subCompanies = \Helper::getGroupCompany($input['globalCompanyId']);
                    $molContributions = $molContributions->whereIn('company_id', $subCompanies);
                }
            }
        }

        return \DataTables::eloquent($molContributions)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('mol_contribution.id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getMolContributionFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companies = "";
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companies = [$selectedCompanyId];
        }
        $companiesByGroup = Company::whereIn('companySystemID',$companies)->get();
        $chartOfAccount = ChartOfAccount::where('isApproved', 1)->where('controlAccountsSystemID', 2)
                                        ->whereHas('chartofaccount_assigned', function($query) use ($companies){
                                            $query->whereIn('companySystemID', $companies)
                                                  ->where('isAssigned', -1);
                                        })->get();

        $suppliers = SupplierAssigned::where('companySystemID',$selectedCompanyId)
            ->where('isAssigned',-1)
            ->whereHas('master', function($query) use ($companies){
                $query->where('approvedYN',1)
                    ->where('isActive',1);
            })
            ->get();

        $output = array(
            'companies' => $companiesByGroup,
            'chartOfAccount' => $chartOfAccount,
            'suppliers' => $suppliers
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
}
