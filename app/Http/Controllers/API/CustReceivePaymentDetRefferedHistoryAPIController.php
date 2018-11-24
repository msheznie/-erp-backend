<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustReceivePaymentDetRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdateCustReceivePaymentDetRefferedHistoryAPIRequest;
use App\Models\CustReceivePaymentDetRefferedHistory;
use App\Repositories\CustReceivePaymentDetRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustReceivePaymentDetRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class CustReceivePaymentDetRefferedHistoryAPIController extends AppBaseController
{
    /** @var  CustReceivePaymentDetRefferedHistoryRepository */
    private $custReceivePaymentDetRefferedHistoryRepository;

    public function __construct(CustReceivePaymentDetRefferedHistoryRepository $custReceivePaymentDetRefferedHistoryRepo)
    {
        $this->custReceivePaymentDetRefferedHistoryRepository = $custReceivePaymentDetRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/custReceivePaymentDetRefferedHistories",
     *      summary="Get a listing of the CustReceivePaymentDetRefferedHistories.",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Get all CustReceivePaymentDetRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/CustReceivePaymentDetRefferedHistory")
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
        $this->custReceivePaymentDetRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->custReceivePaymentDetRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $custReceivePaymentDetRefferedHistories = $this->custReceivePaymentDetRefferedHistoryRepository->all();

        return $this->sendResponse($custReceivePaymentDetRefferedHistories->toArray(), 'Cust Receive Payment Det Reffered Histories retrieved successfully');
    }

    /**
     * @param CreateCustReceivePaymentDetRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/custReceivePaymentDetRefferedHistories",
     *      summary="Store a newly created CustReceivePaymentDetRefferedHistory in storage",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Store CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustReceivePaymentDetRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustReceivePaymentDetRefferedHistory")
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
     *                  ref="#/definitions/CustReceivePaymentDetRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustReceivePaymentDetRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $custReceivePaymentDetRefferedHistories = $this->custReceivePaymentDetRefferedHistoryRepository->create($input);

        return $this->sendResponse($custReceivePaymentDetRefferedHistories->toArray(), 'Cust Receive Payment Det Reffered History saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/custReceivePaymentDetRefferedHistories/{id}",
     *      summary="Display the specified CustReceivePaymentDetRefferedHistory",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Get CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustReceivePaymentDetRefferedHistory",
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
     *                  ref="#/definitions/CustReceivePaymentDetRefferedHistory"
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
        /** @var CustReceivePaymentDetRefferedHistory $custReceivePaymentDetRefferedHistory */
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            return $this->sendError('Cust Receive Payment Det Reffered History not found');
        }

        return $this->sendResponse($custReceivePaymentDetRefferedHistory->toArray(), 'Cust Receive Payment Det Reffered History retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustReceivePaymentDetRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/custReceivePaymentDetRefferedHistories/{id}",
     *      summary="Update the specified CustReceivePaymentDetRefferedHistory in storage",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Update CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustReceivePaymentDetRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustReceivePaymentDetRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustReceivePaymentDetRefferedHistory")
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
     *                  ref="#/definitions/CustReceivePaymentDetRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustReceivePaymentDetRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustReceivePaymentDetRefferedHistory $custReceivePaymentDetRefferedHistory */
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            return $this->sendError('Cust Receive Payment Det Reffered History not found');
        }

        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($custReceivePaymentDetRefferedHistory->toArray(), 'CustReceivePaymentDetRefferedHistory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/custReceivePaymentDetRefferedHistories/{id}",
     *      summary="Remove the specified CustReceivePaymentDetRefferedHistory from storage",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Delete CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustReceivePaymentDetRefferedHistory",
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
        /** @var CustReceivePaymentDetRefferedHistory $custReceivePaymentDetRefferedHistory */
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            return $this->sendError('Cust Receive Payment Det Reffered History not found');
        }

        $custReceivePaymentDetRefferedHistory->delete();

        return $this->sendResponse($id, 'Cust Receive Payment Det Reffered History deleted successfully');
    }

    public function getRVDetailAmendHistory(Request $request)
    {
        $input = $request->all();
        $directReceiptAutoID = $input['custReceivePaymentAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = CustReceivePaymentDetRefferedHistory::where('custReceivePaymentAutoID', $directReceiptAutoID)
            ->where('timesReferred', $timesReferred)
            ->get();

        return $this->sendResponse($items->toArray(), 'Purchase Order Details Reffered History retrieved successfully');
    }
}
