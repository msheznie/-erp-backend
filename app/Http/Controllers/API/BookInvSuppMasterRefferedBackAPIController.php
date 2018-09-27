<?php

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

        return $this->sendResponse($bookInvSuppMasterRefferedBacks->toArray(), 'Book Inv Supp Master Reffered Backs retrieved successfully');
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

        return $this->sendResponse($bookInvSuppMasterRefferedBacks->toArray(), 'Book Inv Supp Master Reffered Back saved successfully');
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
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            return $this->sendError('Book Inv Supp Master Reffered Back not found');
        }

        return $this->sendResponse($bookInvSuppMasterRefferedBack->toArray(), 'Book Inv Supp Master Reffered Back retrieved successfully');
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
            return $this->sendError('Book Inv Supp Master Reffered Back not found');
        }

        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($bookInvSuppMasterRefferedBack->toArray(), 'BookInvSuppMasterRefferedBack updated successfully');
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
            return $this->sendError('Book Inv Supp Master Reffered Back not found');
        }

        $bookInvSuppMasterRefferedBack->delete();

        return $this->sendResponse($id, 'Book Inv Supp Master Reffered Back deleted successfully');
    }
}
