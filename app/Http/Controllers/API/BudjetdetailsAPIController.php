<?php
/**
 * =============================================
 * -- File Name : BudjetdetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Mohamed Fayas
 * -- Create date : 16 - October 2018
 * -- Description : This file contains the all CRUD for Budget details
 * -- REVISION HISTORY
 * -- Date: 16 -October 2018 By: Fayas Description: Added new function
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudjetdetailsAPIRequest;
use App\Http\Requests\API\UpdateBudjetdetailsAPIRequest;
use App\Models\Budjetdetails;
use App\Repositories\BudjetdetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudjetdetailsController
 * @package App\Http\Controllers\API
 */

class BudjetdetailsAPIController extends AppBaseController
{
    /** @var  BudjetdetailsRepository */
    private $budjetdetailsRepository;

    public function __construct(BudjetdetailsRepository $budjetdetailsRepo)
    {
        $this->budjetdetailsRepository = $budjetdetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budjetdetails",
     *      summary="Get a listing of the Budjetdetails.",
     *      tags={"Budjetdetails"},
     *      description="Get all Budjetdetails",
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
     *                  @SWG\Items(ref="#/definitions/Budjetdetails")
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
        $this->budjetdetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->budjetdetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budjetdetails = $this->budjetdetailsRepository->all();

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails retrieved successfully');
    }

    /**
     * @param CreateBudjetdetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budjetdetails",
     *      summary="Store a newly created Budjetdetails in storage",
     *      tags={"Budjetdetails"},
     *      description="Store Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Budjetdetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Budjetdetails")
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
     *                  ref="#/definitions/Budjetdetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudjetdetailsAPIRequest $request)
    {
        $input = $request->all();

        $budjetdetails = $this->budjetdetailsRepository->create($input);

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budjetdetails/{id}",
     *      summary="Display the specified Budjetdetails",
     *      tags={"Budjetdetails"},
     *      description="Get Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Budjetdetails",
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
     *                  ref="#/definitions/Budjetdetails"
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
        /** @var Budjetdetails $budjetdetails */
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            return $this->sendError('Budjetdetails not found');
        }

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBudjetdetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budjetdetails/{id}",
     *      summary="Update the specified Budjetdetails in storage",
     *      tags={"Budjetdetails"},
     *      description="Update Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Budjetdetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Budjetdetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Budjetdetails")
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
     *                  ref="#/definitions/Budjetdetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudjetdetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Budjetdetails $budjetdetails */
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            return $this->sendError('Budjetdetails not found');
        }

        $budjetdetails = $this->budjetdetailsRepository->update($input, $id);

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budjetdetails/{id}",
     *      summary="Remove the specified Budjetdetails from storage",
     *      tags={"Budjetdetails"},
     *      description="Delete Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Budjetdetails",
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
        /** @var Budjetdetails $budjetdetails */
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            return $this->sendError('Budjetdetails not found');
        }

        $budjetdetails->delete();

        return $this->sendResponse($id, 'Budjetdetails deleted successfully');
    }
}
