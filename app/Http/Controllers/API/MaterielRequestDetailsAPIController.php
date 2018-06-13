<?php
/**
 * =============================================
 * -- File Name : MaterielRequestDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Materiel Request Details
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Materiel Request Details
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMaterielRequestDetailsAPIRequest;
use App\Http\Requests\API\UpdateMaterielRequestDetailsAPIRequest;
use App\Models\MaterielRequestDetails;
use App\Repositories\MaterielRequestDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MaterielRequestDetailsController
 * @package App\Http\Controllers\API
 */

class MaterielRequestDetailsAPIController extends AppBaseController
{
    /** @var  MaterielRequestDetailsRepository */
    private $materielRequestDetailsRepository;

    public function __construct(MaterielRequestDetailsRepository $materielRequestDetailsRepo)
    {
        $this->materielRequestDetailsRepository = $materielRequestDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequestDetails",
     *      summary="Get a listing of the MaterielRequestDetails.",
     *      tags={"MaterielRequestDetails"},
     *      description="Get all MaterielRequestDetails",
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
     *                  @SWG\Items(ref="#/definitions/MaterielRequestDetails")
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
        $this->materielRequestDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->materielRequestDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $materielRequestDetails = $this->materielRequestDetailsRepository->all();

        return $this->sendResponse($materielRequestDetails->toArray(), 'Materiel Request Details retrieved successfully');
    }

    /**
     * @param CreateMaterielRequestDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/materielRequestDetails",
     *      summary="Store a newly created MaterielRequestDetails in storage",
     *      tags={"MaterielRequestDetails"},
     *      description="Store MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequestDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequestDetails")
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
     *                  ref="#/definitions/MaterielRequestDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMaterielRequestDetailsAPIRequest $request)
    {
        $input = $request->all();

        $materielRequestDetails = $this->materielRequestDetailsRepository->create($input);

        return $this->sendResponse($materielRequestDetails->toArray(), 'Materiel Request Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequestDetails/{id}",
     *      summary="Display the specified MaterielRequestDetails",
     *      tags={"MaterielRequestDetails"},
     *      description="Get MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequestDetails",
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
     *                  ref="#/definitions/MaterielRequestDetails"
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
        /** @var MaterielRequestDetails $materielRequestDetails */
        $materielRequestDetails = $this->materielRequestDetailsRepository->findWithoutFail($id);

        if (empty($materielRequestDetails)) {
            return $this->sendError('Materiel Request Details not found');
        }

        return $this->sendResponse($materielRequestDetails->toArray(), 'Materiel Request Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMaterielRequestDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/materielRequestDetails/{id}",
     *      summary="Update the specified MaterielRequestDetails in storage",
     *      tags={"MaterielRequestDetails"},
     *      description="Update MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequestDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequestDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequestDetails")
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
     *                  ref="#/definitions/MaterielRequestDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMaterielRequestDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var MaterielRequestDetails $materielRequestDetails */
        $materielRequestDetails = $this->materielRequestDetailsRepository->findWithoutFail($id);

        if (empty($materielRequestDetails)) {
            return $this->sendError('Materiel Request Details not found');
        }

        $materielRequestDetails = $this->materielRequestDetailsRepository->update($input, $id);

        return $this->sendResponse($materielRequestDetails->toArray(), 'MaterielRequestDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/materielRequestDetails/{id}",
     *      summary="Remove the specified MaterielRequestDetails from storage",
     *      tags={"MaterielRequestDetails"},
     *      description="Delete MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequestDetails",
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
        /** @var MaterielRequestDetails $materielRequestDetails */
        $materielRequestDetails = $this->materielRequestDetailsRepository->findWithoutFail($id);

        if (empty($materielRequestDetails)) {
            return $this->sendError('Materiel Request Details not found');
        }

        $materielRequestDetails->delete();

        return $this->sendResponse($id, 'Materiel Request Details deleted successfully');
    }
}
