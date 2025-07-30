<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankStatementMasterAPIRequest;
use App\Http\Requests\API\UpdateBankStatementMasterAPIRequest;
use App\Models\BankReconciliation;
use App\Models\BankStatementDetail;
use App\Models\BankStatementMaster;
use App\Repositories\BankStatementMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Jobs\BankStatementMatch;
use App\Models\BankReconciliationRules;

/**
 * Class BankStatementMasterController
 * @package App\Http\Controllers\API
 */

class BankStatementMasterAPIController extends AppBaseController
{
    /** @var  BankStatementMasterRepository */
    private $bankStatementMasterRepository;

    public function __construct(BankStatementMasterRepository $bankStatementMasterRepo)
    {
        $this->bankStatementMasterRepository = $bankStatementMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/bankStatementMasters",
     *      summary="getBankStatementMasterList",
     *      tags={"BankStatementMaster"},
     *      description="Get all BankStatementMasters",
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
     *                  @OA\Items(ref="#/definitions/BankStatementMaster")
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
        $this->bankStatementMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bankStatementMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankStatementMasters = $this->bankStatementMasterRepository->all();

        return $this->sendResponse($bankStatementMasters->toArray(), 'Bank Statement Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/bankStatementMasters",
     *      summary="createBankStatementMaster",
     *      tags={"BankStatementMaster"},
     *      description="Create BankStatementMaster",
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
     *                  ref="#/definitions/BankStatementMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankStatementMasterAPIRequest $request)
    {
        $input = $request->all();

        $bankStatementMaster = $this->bankStatementMasterRepository->create($input);

        return $this->sendResponse($bankStatementMaster->toArray(), 'Bank Statement Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/bankStatementMasters/{id}",
     *      summary="getBankStatementMasterItem",
     *      tags={"BankStatementMaster"},
     *      description="Get BankStatementMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankStatementMaster",
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
     *                  ref="#/definitions/BankStatementMaster"
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
        /** @var BankStatementMaster $bankStatementMaster */
        $bankStatementMaster = $this->bankStatementMasterRepository->findWithoutFail($id);

        if (empty($bankStatementMaster)) {
            return $this->sendError('Bank Statement Master not found');
        }

        return $this->sendResponse($bankStatementMaster->toArray(), 'Bank Statement Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/bankStatementMasters/{id}",
     *      summary="updateBankStatementMaster",
     *      tags={"BankStatementMaster"},
     *      description="Update BankStatementMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankStatementMaster",
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
     *                  ref="#/definitions/BankStatementMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankStatementMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankStatementMaster $bankStatementMaster */
        $bankStatementMaster = $this->bankStatementMasterRepository->findWithoutFail($id);

        if (empty($bankStatementMaster)) {
            return $this->sendError('Bank Statement Master not found');
        }

        $bankStatementMaster = $this->bankStatementMasterRepository->update($input, $id);

        return $this->sendResponse($bankStatementMaster->toArray(), 'BankStatementMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/bankStatementMasters/{id}",
     *      summary="deleteBankStatementMaster",
     *      tags={"BankStatementMaster"},
     *      description="Delete BankStatementMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankStatementMaster",
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
        /** @var BankStatementMaster $bankStatementMaster */
        $bankStatementMaster = $this->bankStatementMasterRepository->findWithoutFail($id);

        if (empty($bankStatementMaster)) {
            return $this->sendError('Bank Statement Master not found');
        }

        $bankStatementMaster->delete();

        return $this->sendSuccess('Bank Statement Master deleted successfully');
    }

    public function getBankStatementImportHistory(Request $request)
    {
        $input = $request->all();
        $companyId = $request['companyId'];
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $searchValue = $request->input('search.value');

        $bankTransferMaster = $this->bankStatementMasterRepository->bankStatementImportHistory($searchValue, $companyId);
        return \DataTables::eloquent($bankTransferMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('statementId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function deleteBankStatement($statementId)
    {
        $bankStatementMaster = $this->bankStatementMasterRepository->findWithoutFail($statementId);

        if (empty($bankStatementMaster)) {
            return $this->sendError('Bank statement not found');
        }
        $bankStatementMaster->delete();
        BankStatementDetail::where('statementId', $statementId)->delete();
        return $this->sendResponse([], 'Bank statement deleted successfully');
    }

    public function getBankStatementWorkBook(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $searchValue = $request->input('search.value');
        $companyId = $request['companyId'];

        $bankTransferMaster = $this->bankStatementMasterRepository->bankStatementWorkBook($searchValue, $companyId);
        return \DataTables::eloquent($bankTransferMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('statementId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function validateWorkbookCreation(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'statementId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $statementId = $input['statementId'];
        $bankStatementMaster = $this->bankStatementMasterRepository->findWithoutFail($statementId);

        $exists = BankReconciliation::where('approvedYN', 0)->where('bankAccountAutoID', $bankStatementMaster->bankAccountAutoID)->first();
        if (!empty($exists)) {
            return $this->sendError('There is a bank reconciliation '. $exists->bankRecPrimaryCode .' pending for approval for this account. Please check.');
        }

        $validateAsOfDate = BankReconciliation::where('bankRecAsOf', '>=', Carbon::parse($bankStatementMaster->statementEndDate))->where('bankAccountAutoID', $bankStatementMaster->bankAccountAutoID)->first();
        if (!empty($validateAsOfDate)) {
            return $this->sendError('Bank reconciliation already available. Proceed for the as-of date.');
        }

        /** validate matching rule */
        $matchingRule = BankReconciliationRules::where('bankAccountAutoID', $bankStatementMaster->bankAccountAutoID)
                                    ->where('isDefault', 1)
                                    ->where('companySystemID', $bankStatementMaster->companySystemID)
                                    ->where('matchType', 1)
                                    ->whereIn('transactionType', [1, 2])
                                    ->pluck('transactionType')
                                    ->toArray();
                                    
        if (!in_array(1, $matchingRule) || !in_array(2, $matchingRule)) {
            return $this->sendError('The matching rules are not active to proceed.', 500, ['type' => 'rulesNotFound', 'bankAccountAutoID' => $bankStatementMaster->bankAccountAutoID]);

        }

        $update['documentStatus'] = 1;
        $update['matchingInprogress'] = 1;
        $this->bankStatementMasterRepository->update($update, $statementId);

        /** initiate worksheet process in a job */
        $db = isset($request->db) ? $request->db : "";
        BankStatementMatch::dispatch($db, $statementId);

        return $this->sendResponse([], 'Workbook validation success.');
    }

    public function getWorkBookHeaderData(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'statementId' => 'required',
            'companyId' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $statementId = $input['statementId'];
        $companySystemID = $input['companyId'];

        $bankRecDetails = $this->bankStatementMasterRepository->getBankWorkbookHeaderDetails($statementId, $companySystemID);
        return $this->sendResponse($bankRecDetails, 'Workbook details fetched successfully.');
    }

    public function getUnmatchedDetails(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'statementId' => 'required',
            'companyId' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $statementId = $input['statementId'];
        $companySystemID = $input['companyId'];

        $bankRecDetails = $this->bankStatementMasterRepository->getBankWorkbookDetails($statementId, $companySystemID);
        return $this->sendResponse($bankRecDetails, 'Workbook details fetched successfully.');
    }

    function fetchWrkbookJobStatus(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'statementId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $statementId = $input['statementId'];
        $bankStatementMaster = $this->bankStatementMasterRepository->findWithoutFail($statementId);

        if (empty($bankStatementMaster)) {
            return $this->sendError('Bank statement not found');
        } else {
            $data['status'] = $bankStatementMaster->matchingInprogress == 3? 1 : 0;
            return $this->sendResponse($data, 'Workbook job status fetched successfully.');
        }
    }

    function rematchWorkBook(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'statementId' => 'required',
            'companyId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $statementId = $input['statementId'];
        $companySystemID = $input['companyId'];
        
        $bankStatementMaster = $this->bankStatementMasterRepository->findWithoutFail($statementId);
        if (empty($bankStatementMaster)) {
            return $this->sendError('Bank statement not found');
        }

        $matchingRule = BankReconciliationRules::where('bankAccountAutoID', $bankStatementMaster->bankAccountAutoID)
                                                ->where('isDefault', 1)
                                                ->where('companySystemID', $bankStatementMaster->companySystemID)
                                                ->where('matchType', 1)
                                                ->whereIn('transactionType', [1, 2])
                                                ->pluck('transactionType')
                                                ->toArray();
        
        if (!in_array(1, $matchingRule) || !in_array(2, $matchingRule)) {
            return $this->sendError('The matching rules are not active to proceed');
        }

        /** updating matchingInprogress to 1 to start rematching */
        $update['matchingInprogress'] = 1;
        $this->bankStatementMasterRepository->update($update, $statementId);

        /** initiate worksheet process in a job */
        $db = isset($request->db) ? $request->db : "";
        BankStatementMatch::dispatch($db, $statementId);

        return $this->sendResponse([], 'Workbook validation success.');
    }

    function getWorkbookAdditionalEntries(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'statementId' => 'required',
            'companyId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $statementId = $input['statementId'];
        $companySystemID = $input['companyId'];
        $bankRecDetails = $this->bankStatementMasterRepository->getWorkbookAdditionalEntries($statementId, $companySystemID);
        return $this->sendResponse($bankRecDetails, 'Workbook additional entries fetched successfully.');
    }
}
