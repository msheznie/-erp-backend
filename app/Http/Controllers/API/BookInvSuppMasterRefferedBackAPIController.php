<?php
/**
 * =============================================
 * -- File Name : BookInvSuppMasterRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppMasterRefferedBack
 * -- Author : Mohamed Nazir
 * -- Create date : 01 - October 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 01-October 2018 By: Nazir Description: Added new function getSIMasterAmendHistory(),

 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBookInvSuppMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBookInvSuppMasterRefferedBackAPIRequest;
use App\Models\BookInvSuppMasterRefferedBack;
use App\Repositories\BookInvSuppMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BookInvSuppMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class BookInvSuppMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  BookInvSuppMasterRefferedBackRepository */
    private $bookInvSuppMasterRefferedBackRepository;

    public function __construct(BookInvSuppMasterRefferedBackRepository $bookInvSuppMasterRefferedBackRepo)
    {
        $this->bookInvSuppMasterRefferedBackRepository = $bookInvSuppMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasterRefferedBacks",
     *      summary="Get a listing of the BookInvSuppMasterRefferedBacks.",
     *      tags={"BookInvSuppMasterRefferedBack"},
     *      description="Get all BookInvSuppMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BookInvSuppMasterRefferedBack")
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
        $this->bookInvSuppMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->bookInvSuppMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bookInvSuppMasterRefferedBacks = $this->bookInvSuppMasterRefferedBackRepository->all();

        return $this->sendResponse($bookInvSuppMasterRefferedBacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.book_inv_supp_master_reffered_backs')]));
    }

    /**
     * @param CreateBookInvSuppMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bookInvSuppMasterRefferedBacks",
     *      summary="Store a newly created BookInvSuppMasterRefferedBack in storage",
     *      tags={"BookInvSuppMasterRefferedBack"},
     *      description="Store BookInvSuppMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMasterRefferedBack")
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
     *                  ref="#/definitions/BookInvSuppMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBookInvSuppMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $bookInvSuppMasterRefferedBacks = $this->bookInvSuppMasterRefferedBackRepository->create($input);

        return $this->sendResponse($bookInvSuppMasterRefferedBacks->toArray(), trans('custom.save', ['attribute' => trans('custom.book_inv_supp_master_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasterRefferedBacks/{id}",
     *      summary="Display the specified BookInvSuppMasterRefferedBack",
     *      tags={"BookInvSuppMasterRefferedBack"},
     *      description="Get BookInvSuppMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMasterRefferedBack",
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
     *                  ref="#/definitions/BookInvSuppMasterRefferedBack"
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
        /** @var BookInvSuppMasterRefferedBack $bookInvSuppMasterRefferedBack */
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->with(['created_by', 'confirmed_by', 'company', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.book_inv_supp_master_reffered_backs')]));
        }

        return $this->sendResponse($bookInvSuppMasterRefferedBack->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.book_inv_supp_master_reffered_backs')]));
    }

    /**
     * @param int $id
     * @param UpdateBookInvSuppMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bookInvSuppMasterRefferedBacks/{id}",
     *      summary="Update the specified BookInvSuppMasterRefferedBack in storage",
     *      tags={"BookInvSuppMasterRefferedBack"},
     *      description="Update BookInvSuppMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMasterRefferedBack")
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
     *                  ref="#/definitions/BookInvSuppMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBookInvSuppMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BookInvSuppMasterRefferedBack $bookInvSuppMasterRefferedBack */
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            return $this->sendError(trans('custom.book_inv_supp_master_reffered_back_not_found'));
        }

        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($bookInvSuppMasterRefferedBack->toArray(), trans('custom.update', ['attribute' => trans('custom.book_inv_supp_master_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bookInvSuppMasterRefferedBacks/{id}",
     *      summary="Remove the specified BookInvSuppMasterRefferedBack from storage",
     *      tags={"BookInvSuppMasterRefferedBack"},
     *      description="Delete BookInvSuppMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMasterRefferedBack",
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
        /** @var BookInvSuppMasterRefferedBack $bookInvSuppMasterRefferedBack */
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.book_inv_supp_master_reffered_backs')]));
        }

        $bookInvSuppMasterRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.book_inv_supp_master_reffered_backs')]));
    }

    public function getSIMasterAmendHistory(Request $request)
    {
        $input = $request->all();

        $supplierInvoiceHistory = BookInvSuppMasterRefferedBack::where('bookingSuppMasInvAutoID', $input['bookingSuppMasInvAutoID'])
            ->with(['created_by','confirmed_by','modified_by','supplier','approved_by', 'cancelled_by', 'transactioncurrency'])
            ->get();

        return $this->sendResponse($supplierInvoiceHistory, trans('custom.retrieve', ['attribute' => trans('custom.invoice_detail')]));
    }

}
