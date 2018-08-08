<?php
/**
 * =============================================
 * -- File Name : BookInvSuppMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBookInvSuppMasterAPIRequest;
use App\Http\Requests\API\UpdateBookInvSuppMasterAPIRequest;
use App\Models\BookInvSuppMaster;
use App\Repositories\BookInvSuppMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BookInvSuppMasterController
 * @package App\Http\Controllers\API
 */

class BookInvSuppMasterAPIController extends AppBaseController
{
    /** @var  BookInvSuppMasterRepository */
    private $bookInvSuppMasterRepository;

    public function __construct(BookInvSuppMasterRepository $bookInvSuppMasterRepo)
    {
        $this->bookInvSuppMasterRepository = $bookInvSuppMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasters",
     *      summary="Get a listing of the BookInvSuppMasters.",
     *      tags={"BookInvSuppMaster"},
     *      description="Get all BookInvSuppMasters",
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
     *                  @SWG\Items(ref="#/definitions/BookInvSuppMaster")
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
        $this->bookInvSuppMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bookInvSuppMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bookInvSuppMasters = $this->bookInvSuppMasterRepository->all();

        return $this->sendResponse($bookInvSuppMasters->toArray(), 'Book Inv Supp Masters retrieved successfully');
    }

    /**
     * @param CreateBookInvSuppMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bookInvSuppMasters",
     *      summary="Store a newly created BookInvSuppMaster in storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Store BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMaster")
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
     *                  ref="#/definitions/BookInvSuppMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBookInvSuppMasterAPIRequest $request)
    {
        $input = $request->all();

        $bookInvSuppMasters = $this->bookInvSuppMasterRepository->create($input);

        return $this->sendResponse($bookInvSuppMasters->toArray(), 'Book Inv Supp Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Display the specified BookInvSuppMaster",
     *      tags={"BookInvSuppMaster"},
     *      description="Get BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
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
     *                  ref="#/definitions/BookInvSuppMaster"
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
        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Book Inv Supp Master not found');
        }

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'Book Inv Supp Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBookInvSuppMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Update the specified BookInvSuppMaster in storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Update BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMaster")
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
     *                  ref="#/definitions/BookInvSuppMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBookInvSuppMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Book Inv Supp Master not found');
        }

        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->update($input, $id);

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'BookInvSuppMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Remove the specified BookInvSuppMaster from storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Delete BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
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
        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Book Inv Supp Master not found');
        }

        $bookInvSuppMaster->delete();

        return $this->sendResponse($id, 'Book Inv Supp Master deleted successfully');
    }


    public function getInvoiceMasterRecord(Request $request)
    {
        $id = $request->get('id');
        $outputRecord = $this->bookInvSuppMasterRepository->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 3);
            },'details','company_by','currency_by', 'companydocumentattachment_by' => function ($query) {
                $query->where('documentSystemID', 3);
            }])->findWithoutFail($id);

        return $this->sendResponse($outputRecord, 'Data retrieved successfully');

    }
}
