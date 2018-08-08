<?php
/**
 * =============================================
 * -- File Name : BookInvSuppDetAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppDet
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBookInvSuppDetAPIRequest;
use App\Http\Requests\API\UpdateBookInvSuppDetAPIRequest;
use App\Models\BookInvSuppDet;
use App\Repositories\BookInvSuppDetRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BookInvSuppDetController
 * @package App\Http\Controllers\API
 */

class BookInvSuppDetAPIController extends AppBaseController
{
    /** @var  BookInvSuppDetRepository */
    private $bookInvSuppDetRepository;

    public function __construct(BookInvSuppDetRepository $bookInvSuppDetRepo)
    {
        $this->bookInvSuppDetRepository = $bookInvSuppDetRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppDets",
     *      summary="Get a listing of the BookInvSuppDets.",
     *      tags={"BookInvSuppDet"},
     *      description="Get all BookInvSuppDets",
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
     *                  @SWG\Items(ref="#/definitions/BookInvSuppDet")
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
        $this->bookInvSuppDetRepository->pushCriteria(new RequestCriteria($request));
        $this->bookInvSuppDetRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bookInvSuppDets = $this->bookInvSuppDetRepository->all();

        return $this->sendResponse($bookInvSuppDets->toArray(), 'Book Inv Supp Dets retrieved successfully');
    }

    /**
     * @param CreateBookInvSuppDetAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bookInvSuppDets",
     *      summary="Store a newly created BookInvSuppDet in storage",
     *      tags={"BookInvSuppDet"},
     *      description="Store BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppDet that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppDet")
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
     *                  ref="#/definitions/BookInvSuppDet"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBookInvSuppDetAPIRequest $request)
    {
        $input = $request->all();

        $bookInvSuppDets = $this->bookInvSuppDetRepository->create($input);

        return $this->sendResponse($bookInvSuppDets->toArray(), 'Book Inv Supp Det saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppDets/{id}",
     *      summary="Display the specified BookInvSuppDet",
     *      tags={"BookInvSuppDet"},
     *      description="Get BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDet",
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
     *                  ref="#/definitions/BookInvSuppDet"
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
        /** @var BookInvSuppDet $bookInvSuppDet */
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return $this->sendError('Book Inv Supp Det not found');
        }

        return $this->sendResponse($bookInvSuppDet->toArray(), 'Book Inv Supp Det retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBookInvSuppDetAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bookInvSuppDets/{id}",
     *      summary="Update the specified BookInvSuppDet in storage",
     *      tags={"BookInvSuppDet"},
     *      description="Update BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDet",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppDet that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppDet")
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
     *                  ref="#/definitions/BookInvSuppDet"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBookInvSuppDetAPIRequest $request)
    {
        $input = $request->all();

        /** @var BookInvSuppDet $bookInvSuppDet */
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return $this->sendError('Book Inv Supp Det not found');
        }

        $bookInvSuppDet = $this->bookInvSuppDetRepository->update($input, $id);

        return $this->sendResponse($bookInvSuppDet->toArray(), 'BookInvSuppDet updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bookInvSuppDets/{id}",
     *      summary="Remove the specified BookInvSuppDet from storage",
     *      tags={"BookInvSuppDet"},
     *      description="Delete BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDet",
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
        /** @var BookInvSuppDet $bookInvSuppDet */
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return $this->sendError('Book Inv Supp Det not found');
        }

        $bookInvSuppDet->delete();

        return $this->sendResponse($id, 'Book Inv Supp Det deleted successfully');
    }
}
