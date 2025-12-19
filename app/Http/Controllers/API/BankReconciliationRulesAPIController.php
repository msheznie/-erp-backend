<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankReconciliationRulesAPIRequest;
use App\Http\Requests\API\UpdateBankReconciliationRulesAPIRequest;
use App\Models\BankReconciliationRules;
use App\Repositories\BankReconciliationRulesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\BankStatementMaster;

/**
 * Class BankReconciliationRulesController
 * @package App\Http\Controllers\API
 */

class BankReconciliationRulesAPIController extends AppBaseController
{
    /** @var  BankReconciliationRulesRepository */
    private $bankReconciliationRulesRepository;

    public function __construct(BankReconciliationRulesRepository $bankReconciliationRulesRepo)
    {
        $this->bankReconciliationRulesRepository = $bankReconciliationRulesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/bankReconciliationRules",
     *      summary="getBankReconciliationRulesList",
     *      tags={"BankReconciliationRules"},
     *      description="Get all BankReconciliationRules",
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
     *                  @OA\Items(ref="#/definitions/BankReconciliationRules")
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
        $this->bankReconciliationRulesRepository->pushCriteria(new RequestCriteria($request));
        $this->bankReconciliationRulesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankReconciliationRules = $this->bankReconciliationRulesRepository->all();

        return $this->sendResponse($bankReconciliationRules->toArray(), trans('custom.bank_reconciliation_rules_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/bankReconciliationRules",
     *      summary="createBankReconciliationRules",
     *      tags={"BankReconciliationRules"},
     *      description="Create BankReconciliationRules",
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
     *                  ref="#/definitions/BankReconciliationRules"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankReconciliationRulesAPIRequest $request)
    {
        $input = $request->all();

        $bankReconciliationRules = $this->bankReconciliationRulesRepository->create($input);

        return $this->sendResponse($bankReconciliationRules->toArray(), trans('custom.bank_reconciliation_rules_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/bankReconciliationRules/{id}",
     *      summary="getBankReconciliationRulesItem",
     *      tags={"BankReconciliationRules"},
     *      description="Get BankReconciliationRules",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationRules",
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
     *                  ref="#/definitions/BankReconciliationRules"
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
        /** @var BankReconciliationRules $bankReconciliationRules */
        $bankReconciliationRules = $this->bankReconciliationRulesRepository->findWithoutFail($id);

        if (empty($bankReconciliationRules)) {
            return $this->sendError(trans('custom.bank_reconciliation_rules_not_found'));
        }

        return $this->sendResponse($bankReconciliationRules->toArray(), trans('custom.bank_reconciliation_rules_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/bankReconciliationRules/{id}",
     *      summary="updateBankReconciliationRules",
     *      tags={"BankReconciliationRules"},
     *      description="Update BankReconciliationRules",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationRules",
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
     *                  ref="#/definitions/BankReconciliationRules"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankReconciliationRulesAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('transactionType', 'systemDocumentColumn', 'statementDocumentColumn', 'statementChqueColumn'));

        /** @var BankReconciliationRules $bankReconciliationRules */
        $bankReconciliationRules = $this->bankReconciliationRulesRepository->findWithoutFail($id);

        if (empty($bankReconciliationRules)) {
            return $this->sendError(trans('custom.bank_reconciliation_rules_not_found'));
        }

        $input['isMatchAmount'] = $input['isMatchAmount'] ?? 0;
        $input['isMatchDate'] = $input['isMatchDate'] ?? 0;
        $input['isMatchDocument'] = $input['isMatchDocument'] ?? 0;
        $input['isUseReference'] = $input['isUseReference'] ?? 0;
        $input['isDefault'] = 0;

        $bankReconciliationRules = $this->bankReconciliationRulesRepository->update($input, $id);

        return $this->sendResponse($bankReconciliationRules->toArray(), trans('custom.bankreconciliationrules_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/bankReconciliationRules/{id}",
     *      summary="deleteBankReconciliationRules",
     *      tags={"BankReconciliationRules"},
     *      description="Delete BankReconciliationRules",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationRules",
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
        /** @var BankReconciliationRules $bankReconciliationRules */
        $bankReconciliationRules = $this->bankReconciliationRulesRepository->findWithoutFail($id);

        if (empty($bankReconciliationRules)) {
            return $this->sendError(trans('custom.bank_reconciliation_rules_not_found'));
        }

        $isMatchingInProgress = BankStatementMaster::where('bankAccountAutoId', $bankReconciliationRules->bankAccountAutoID)
                                                ->where('documentStatus', 1)
                                                ->whereIn('matchingInprogress', [1, 2, 4])
                                                ->first();
        if(!empty($isMatchingInProgress)) {
            return $this->sendError('Matching is in progress.');
        }
        $bankReconciliationRules->delete();

        return $this->sendResponse($id, trans('custom.bank_reconciliation_rule_deleted_successfully'));
    }

    public function getBankStatementUploadRules(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'companyID' => 'required',
            'bankAccountId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $companyId = $request->input('companyID');
        $bankAccountId = $request->input('bankAccountId');

        $bankReconciliationRules = $this->bankReconciliationRulesRepository->getBankStatementUploadRules($bankAccountId, $search, $companyId);
        return \DataTables::eloquent($bankReconciliationRules)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('ruleId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getMatchingRuleDetails(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'ruleId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $matchingRuleDetail = $this->bankReconciliationRulesRepository->findWhere(['ruleId' => $input['ruleId']])->first();
        return $this->sendResponse($matchingRuleDetail, trans('custom.rule_details_retrieved_successfully'));
    }

    public function updateDefaultRule(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'ruleId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $ruleId = $input['ruleId'];

        $bankReconciliationRules = $this->bankReconciliationRulesRepository->findWithoutFail($ruleId);
        if (empty($bankReconciliationRules)) {
            return $this->sendError(trans('custom.bank_reconciliation_rules_not_found'));
        }

        $matchingInProgress = BankStatementMaster::where('bankAccountAutoID', $bankReconciliationRules->bankAccountAutoID)
                                                ->where('documentStatus', 1)
                                                ->whereIn('matchingInprogress', [1, 2, 4])
                                                ->first();
        if(!empty($matchingInProgress)) {
            return $this->sendError('Matching is in progress.');
        }

        $isDefault['isDefault'] = !$bankReconciliationRules->isDefault;
        if($bankReconciliationRules->isDefault == 0)
        {
           $alreadyExist = $this->bankReconciliationRulesRepository->findWhere(
               [
                   'isDefault' => 1,
                   'transactionType' => $bankReconciliationRules->transactionType,
                   'matchType' => $bankReconciliationRules->matchType,
                   'bankAccountAutoID' => $bankReconciliationRules->bankAccountAutoID
               ])->first();

           if($alreadyExist) {
               return $this->sendError(trans('custom.a_default_rule_already_exists_for_same_transaction'));
           }
        }
        $bankReconciliationRules = $this->bankReconciliationRulesRepository->update($isDefault, $ruleId);
        return $this->sendResponse($bankReconciliationRules->toArray(), trans('custom.default_status_updated_successfully'));
    }
}