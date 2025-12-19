<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankReconciliationTemplateMappingAPIRequest;
use App\Http\Requests\API\UpdateBankReconciliationTemplateMappingAPIRequest;
use App\Models\BankAccount;
use App\Models\BankReconciliationTemplateMapping;
use App\Models\Company;
use App\Repositories\BankReconciliationTemplateMappingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankReconciliationTemplateMappingController
 * @package App\Http\Controllers\API
 */

class BankReconciliationTemplateMappingAPIController extends AppBaseController
{
    /** @var  BankReconciliationTemplateMappingRepository */
    private $bankReconciliationTemplateMappingRepository;

    public function __construct(BankReconciliationTemplateMappingRepository $bankReconciliationTemplateMappingRepo)
    {
        $this->bankReconciliationTemplateMappingRepository = $bankReconciliationTemplateMappingRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/bankReconciliationTemplateMappings",
     *      summary="getBankReconciliationTemplateMappingList",
     *      tags={"BankReconciliationTemplateMapping"},
     *      description="Get all BankReconciliationTemplateMappings",
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
     *                  @OA\Items(ref="#/definitions/BankReconciliationTemplateMapping")
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
        $this->bankReconciliationTemplateMappingRepository->pushCriteria(new RequestCriteria($request));
        $this->bankReconciliationTemplateMappingRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankReconciliationTemplateMappings = $this->bankReconciliationTemplateMappingRepository->all();

        return $this->sendResponse($bankReconciliationTemplateMappings->toArray(), trans('custom.bank_reconciliation_template_mappings_retrieved_su'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/bankReconciliationTemplateMappings",
     *      summary="createBankReconciliationTemplateMapping",
     *      tags={"BankReconciliationTemplateMapping"},
     *      description="Create BankReconciliationTemplateMapping",
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
     *                  ref="#/definitions/BankReconciliationTemplateMapping"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankReconciliationTemplateMappingAPIRequest $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'bankAccountAutoID' => 'required',
            'assignToAccounts' => 'required',
            'bankName' => 'required',
            'openingBalance' => 'required',
            'bankAccountNumber' => 'required',
            'bankStatementDate' => 'required',
            'endingBalance' => 'required',
            'statementStartDate' => 'required',
            'statementEndDate' => 'required',
            'headerLine' => 'required',
            'firstLine' => 'required',
            'transactionNumber' => 'required',
            'transactionDate' => 'required',
            'debit' => 'required',
            'credit' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }
        $input['bankmasterAutoID'] = BankAccount::where('bankAccountAutoID', $input['bankAccountAutoID'])
            ->where('companySystemID', $input['companySystemID'])
            ->value('bankmasterAutoID');

        $assignToAccounts = $input['assignToAccounts'];
        if(isset($input['assignToAccounts'])){
            unset($input['assignToAccounts']);
        }
        if($assignToAccounts == 1) {
            $result = $this->bankReconciliationTemplateMappingRepository->updateAllAccounts($input);
            return $this->sendResponse([], $result);
        } else {
            if(!empty($input['templateId'])) {
                $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->findWithoutFail($input['templateId']);

                if (empty($bankReconciliationTemplateMapping)) {
                    return $this->sendError(trans('custom.bank_reconciliation_template_mapping_not_found_1'));
                }
                $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->update($input, $input['templateId']);
            } else {
                $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->create($input);
            }
            return $this->sendResponse($bankReconciliationTemplateMapping->toArray(), trans('custom.bank_reconciliation_template_mapping_saved_success'));
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/bankReconciliationTemplateMappings/{id}",
     *      summary="getBankReconciliationTemplateMappingItem",
     *      tags={"BankReconciliationTemplateMapping"},
     *      description="Get BankReconciliationTemplateMapping",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationTemplateMapping",
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
     *                  ref="#/definitions/BankReconciliationTemplateMapping"
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
        /** @var BankReconciliationTemplateMapping $bankReconciliationTemplateMapping */
        $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->findWithoutFail($id);

        if (empty($bankReconciliationTemplateMapping)) {
            return $this->sendError(trans('custom.bank_reconciliation_template_mapping_not_found_1'));
        }

        return $this->sendResponse($bankReconciliationTemplateMapping->toArray(), trans('custom.bank_reconciliation_template_mapping_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/bankReconciliationTemplateMappings/{id}",
     *      summary="updateBankReconciliationTemplateMapping",
     *      tags={"BankReconciliationTemplateMapping"},
     *      description="Update BankReconciliationTemplateMapping",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationTemplateMapping",
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
     *                  ref="#/definitions/BankReconciliationTemplateMapping"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankReconciliationTemplateMappingAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankReconciliationTemplateMapping $bankReconciliationTemplateMapping */
        $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->findWithoutFail($id);

        if (empty($bankReconciliationTemplateMapping)) {
            return $this->sendError(trans('custom.bank_reconciliation_template_mapping_not_found_1'));
        }

        $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->update($input, $id);

        return $this->sendResponse($bankReconciliationTemplateMapping->toArray(), trans('custom.bankreconciliationtemplatemapping_updated_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/bankReconciliationTemplateMappings/{id}",
     *      summary="deleteBankReconciliationTemplateMapping",
     *      tags={"BankReconciliationTemplateMapping"},
     *      description="Delete BankReconciliationTemplateMapping",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationTemplateMapping",
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
        /** @var BankReconciliationTemplateMapping $bankReconciliationTemplateMapping */
        $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->findWithoutFail($id);

        if (empty($bankReconciliationTemplateMapping)) {
            return $this->sendError(trans('custom.bank_reconciliation_template_mapping_not_found_1'));
        }

        $bankReconciliationTemplateMapping->delete();

        return $this->sendSuccess('Bank Reconciliation Template Mapping deleted successfully');
    }

    public function getTemplateMappingDetails(Request $request)
    {
        $accountAutoId = $request['accountAutoId'];

        $bankReconciliationTemplateMapping = $this->bankReconciliationTemplateMappingRepository->where('bankAccountAutoID', $accountAutoId)->first();
        if (!$bankReconciliationTemplateMapping) {
            return $this->sendError(trans('custom.bank_reconciliation_template_mapping_not_found'), 404);
        }
        return $this->sendResponse($bankReconciliationTemplateMapping->toArray(), trans('custom.bank_reconciliation_template_mapping_retrieved_suc'));
    }
}
