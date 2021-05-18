<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBookInvSuppDetRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBookInvSuppDetRefferedBackAPIRequest;
use App\Models\BookInvSuppDetRefferedBack;
use App\Repositories\BookInvSuppDetRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BookInvSuppDetRefferedBackController
 * @package App\Http\Controllers\API
 */
class BookInvSuppDetRefferedBackAPIController extends AppBaseController
{
    /** @var  BookInvSuppDetRefferedBackRepository */
    private $bookInvSuppDetRefferedBackRepository;

    public function __construct(BookInvSuppDetRefferedBackRepository $bookInvSuppDetRefferedBackRepo)
    {
        $this->bookInvSuppDetRefferedBackRepository = $bookInvSuppDetRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppDetRefferedBacks",
     *      summary="Get a listing of the BookInvSuppDetRefferedBacks.",
     *      tags={"BookInvSuppDetRefferedBack"},
     *      description="Get all BookInvSuppDetRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BookInvSuppDetRefferedBack")
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
        $this->bookInvSuppDetRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->bookInvSuppDetRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bookInvSuppDetRefferedBacks = $this->bookInvSuppDetRefferedBackRepository->all();

        return $this->sendResponse($bookInvSuppDetRefferedBacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
    }

    /**
     * @param CreateBookInvSuppDetRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bookInvSuppDetRefferedBacks",
     *      summary="Store a newly created BookInvSuppDetRefferedBack in storage",
     *      tags={"BookInvSuppDetRefferedBack"},
     *      description="Store BookInvSuppDetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppDetRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppDetRefferedBack")
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
     *                  ref="#/definitions/BookInvSuppDetRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBookInvSuppDetRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $bookInvSuppDetRefferedBacks = $this->bookInvSuppDetRefferedBackRepository->create($input);

        return $this->sendResponse($bookInvSuppDetRefferedBacks->toArray(), trans('custom.save', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppDetRefferedBacks/{id}",
     *      summary="Display the specified BookInvSuppDetRefferedBack",
     *      tags={"BookInvSuppDetRefferedBack"},
     *      description="Get BookInvSuppDetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDetRefferedBack",
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
     *                  ref="#/definitions/BookInvSuppDetRefferedBack"
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
        /** @var BookInvSuppDetRefferedBack $bookInvSuppDetRefferedBack */
        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppDetRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
        }

        return $this->sendResponse($bookInvSuppDetRefferedBack->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
    }

    /**
     * @param int $id
     * @param UpdateBookInvSuppDetRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bookInvSuppDetRefferedBacks/{id}",
     *      summary="Update the specified BookInvSuppDetRefferedBack in storage",
     *      tags={"BookInvSuppDetRefferedBack"},
     *      description="Update BookInvSuppDetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDetRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppDetRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppDetRefferedBack")
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
     *                  ref="#/definitions/BookInvSuppDetRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBookInvSuppDetRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BookInvSuppDetRefferedBack $bookInvSuppDetRefferedBack */
        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppDetRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
        }

        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->update($input, $id);

        return $this->sendResponse($bookInvSuppDetRefferedBack->toArray(), trans('custom.update', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bookInvSuppDetRefferedBacks/{id}",
     *      summary="Remove the specified BookInvSuppDetRefferedBack from storage",
     *      tags={"BookInvSuppDetRefferedBack"},
     *      description="Delete BookInvSuppDetRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDetRefferedBack",
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
        /** @var BookInvSuppDetRefferedBack $bookInvSuppDetRefferedBack */
        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppDetRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
        }

        $bookInvSuppDetRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.book_inv_supp_det_reffered_backs')]));
    }

    public function getSIDetailGRVAmendHistory(Request $request)
    {
        $input = $request->all();
        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = BookInvSuppDetRefferedBack::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['pomaster', 'grvmaster', 'suppinvmaster'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.purchase_order_details_reffered_history')]));
    }
}
