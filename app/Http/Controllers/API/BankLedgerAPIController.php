<?php
/**
 * =============================================
 * -- File Name : BankLedgerAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Ledger
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - September 2018
 * -- Description : This file contains the all CRUD for Bank Ledger
 * -- REVISION HISTORY
 * -- Date: 18-September 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankLedgerAPIRequest;
use App\Http\Requests\API\UpdateBankLedgerAPIRequest;
use App\Models\BankLedger;
use App\Repositories\BankLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankLedgerController
 * @package App\Http\Controllers\API
 */

class BankLedgerAPIController extends AppBaseController
{
    /** @var  BankLedgerRepository */
    private $bankLedgerRepository;

    public function __construct(BankLedgerRepository $bankLedgerRepo)
    {
        $this->bankLedgerRepository = $bankLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankLedgers",
     *      summary="Get a listing of the BankLedgers.",
     *      tags={"BankLedger"},
     *      description="Get all BankLedgers",
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
     *                  @SWG\Items(ref="#/definitions/BankLedger")
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
        $this->bankLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->bankLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankLedgers = $this->bankLedgerRepository->all();

        return $this->sendResponse($bankLedgers->toArray(), 'Bank Ledgers retrieved successfully');
    }

    /**
     * @param CreateBankLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankLedgers",
     *      summary="Store a newly created BankLedger in storage",
     *      tags={"BankLedger"},
     *      description="Store BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankLedger")
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
     *                  ref="#/definitions/BankLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankLedgerAPIRequest $request)
    {
        $input = $request->all();

        $bankLedgers = $this->bankLedgerRepository->create($input);

        return $this->sendResponse($bankLedgers->toArray(), 'Bank Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankLedgers/{id}",
     *      summary="Display the specified BankLedger",
     *      tags={"BankLedger"},
     *      description="Get BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
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
     *                  ref="#/definitions/BankLedger"
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
        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError('Bank Ledger not found');
        }

        return $this->sendResponse($bankLedger->toArray(), 'Bank Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBankLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankLedgers/{id}",
     *      summary="Update the specified BankLedger in storage",
     *      tags={"BankLedger"},
     *      description="Update BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankLedger")
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
     *                  ref="#/definitions/BankLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError('Bank Ledger not found');
        }

        $bankLedger = $this->bankLedgerRepository->update($input, $id);

        return $this->sendResponse($bankLedger->toArray(), 'BankLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankLedgers/{id}",
     *      summary="Remove the specified BankLedger from storage",
     *      tags={"BankLedger"},
     *      description="Delete BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
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
        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError('Bank Ledger not found');
        }

        $bankLedger->delete();

        return $this->sendResponse($id, 'Bank Ledger deleted successfully');
    }
}
