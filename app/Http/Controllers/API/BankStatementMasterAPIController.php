<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankStatementMasterAPIRequest;
use App\Http\Requests\API\UpdateBankStatementMasterAPIRequest;
use App\Models\BankStatementDetail;
use App\Models\BankStatementMaster;
use App\Repositories\BankStatementMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

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
}
