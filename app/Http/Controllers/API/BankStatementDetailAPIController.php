<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankStatementDetailAPIRequest;
use App\Http\Requests\API\UpdateBankStatementDetailAPIRequest;
use App\Models\BankStatementDetail;
use App\Repositories\BankStatementDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankStatementDetailController
 * @package App\Http\Controllers\API
 */

class BankStatementDetailAPIController extends AppBaseController
{
    /** @var  BankStatementDetailRepository */
    private $bankStatementDetailRepository;

    public function __construct(BankStatementDetailRepository $bankStatementDetailRepo)
    {
        $this->bankStatementDetailRepository = $bankStatementDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/bankStatementDetails",
     *      summary="getBankStatementDetailList",
     *      tags={"BankStatementDetail"},
     *      description="Get all BankStatementDetails",
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
     *                  @OA\Items(ref="#/definitions/BankStatementDetail")
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
        $this->bankStatementDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->bankStatementDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankStatementDetails = $this->bankStatementDetailRepository->all();

        return $this->sendResponse($bankStatementDetails->toArray(), 'Bank Statement Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/bankStatementDetails",
     *      summary="createBankStatementDetail",
     *      tags={"BankStatementDetail"},
     *      description="Create BankStatementDetail",
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
     *                  ref="#/definitions/BankStatementDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankStatementDetailAPIRequest $request)
    {
        $input = $request->all();

        $bankStatementDetail = $this->bankStatementDetailRepository->create($input);

        return $this->sendResponse($bankStatementDetail->toArray(), 'Bank Statement Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/bankStatementDetails/{id}",
     *      summary="getBankStatementDetailItem",
     *      tags={"BankStatementDetail"},
     *      description="Get BankStatementDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankStatementDetail",
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
     *                  ref="#/definitions/BankStatementDetail"
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
        /** @var BankStatementDetail $bankStatementDetail */
        $bankStatementDetail = $this->bankStatementDetailRepository->findWithoutFail($id);

        if (empty($bankStatementDetail)) {
            return $this->sendError('Bank Statement Detail not found');
        }

        return $this->sendResponse($bankStatementDetail->toArray(), 'Bank Statement Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/bankStatementDetails/{id}",
     *      summary="updateBankStatementDetail",
     *      tags={"BankStatementDetail"},
     *      description="Update BankStatementDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankStatementDetail",
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
     *                  ref="#/definitions/BankStatementDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankStatementDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankStatementDetail $bankStatementDetail */
        $bankStatementDetail = $this->bankStatementDetailRepository->findWithoutFail($id);

        if (empty($bankStatementDetail)) {
            return $this->sendError('Bank Statement Detail not found');
        }

        $bankStatementDetail = $this->bankStatementDetailRepository->update($input, $id);

        return $this->sendResponse($bankStatementDetail->toArray(), 'BankStatementDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/bankStatementDetails/{id}",
     *      summary="deleteBankStatementDetail",
     *      tags={"BankStatementDetail"},
     *      description="Delete BankStatementDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankStatementDetail",
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
        /** @var BankStatementDetail $bankStatementDetail */
        $bankStatementDetail = $this->bankStatementDetailRepository->findWithoutFail($id);

        if (empty($bankStatementDetail)) {
            return $this->sendError('Bank Statement Detail not found');
        }

        $bankStatementDetail->delete();

        return $this->sendSuccess('Bank Statement Detail deleted successfully');
    }

    public function moveStatementMatchDetail(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'statementDetailId' => 'required',
            'matchTypeId' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $updateMatchType = $this->bankStatementDetailRepository->updateMatchType($input);
        return $this->sendResponse($updateMatchType, 'Match type updated successfully');
    }

    public function updateManualMatch(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'companyId' => 'required',
            'statementId' => 'required',
            'bankLedgerSelected' => 'required',
            'bankStatementSelected' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $matchId = $this->bankStatementDetailRepository->where('statementId', $input['statementId'])->where('matchType', 1)->count() + 1;

        $matchedDetails = [
            'bankLedgerAutoID' => $input['bankLedgerSelected'],
            'matchType' => 1,
            'matchedId' => $matchId
        ];
        $updateMatchType = $this->bankStatementDetailRepository->update($matchedDetails, $input['bankStatementSelected']);
        return $this->sendResponse($updateMatchType, 'Document matched successfully');
    }
}
