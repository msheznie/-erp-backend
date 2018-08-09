<?php
/**
 * =============================================
 * -- File Name : DirectInvoiceDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DirectInvoiceDetails
 * -- Author : Mohamed Nazir
 * -- Create date : 09 - August 2018
 * -- Description : This file contains the all CRUD for Direct Invoice Details
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectInvoiceDetailsAPIRequest;
use App\Http\Requests\API\UpdateDirectInvoiceDetailsAPIRequest;
use App\Models\DirectInvoiceDetails;
use App\Repositories\DirectInvoiceDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectInvoiceDetailsController
 * @package App\Http\Controllers\API
 */

class DirectInvoiceDetailsAPIController extends AppBaseController
{
    /** @var  DirectInvoiceDetailsRepository */
    private $directInvoiceDetailsRepository;

    public function __construct(DirectInvoiceDetailsRepository $directInvoiceDetailsRepo)
    {
        $this->directInvoiceDetailsRepository = $directInvoiceDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetails",
     *      summary="Get a listing of the DirectInvoiceDetails.",
     *      tags={"DirectInvoiceDetails"},
     *      description="Get all DirectInvoiceDetails",
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
     *                  @SWG\Items(ref="#/definitions/DirectInvoiceDetails")
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
        $this->directInvoiceDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->directInvoiceDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->all();

        return $this->sendResponse($directInvoiceDetails->toArray(), 'Direct Invoice Details retrieved successfully');
    }

    /**
     * @param CreateDirectInvoiceDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directInvoiceDetails",
     *      summary="Store a newly created DirectInvoiceDetails in storage",
     *      tags={"DirectInvoiceDetails"},
     *      description="Store DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetails")
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
     *                  ref="#/definitions/DirectInvoiceDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectInvoiceDetailsAPIRequest $request)
    {
        $input = $request->all();

        $directInvoiceDetails = $this->directInvoiceDetailsRepository->create($input);

        return $this->sendResponse($directInvoiceDetails->toArray(), 'Direct Invoice Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetails/{id}",
     *      summary="Display the specified DirectInvoiceDetails",
     *      tags={"DirectInvoiceDetails"},
     *      description="Get DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetails",
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
     *                  ref="#/definitions/DirectInvoiceDetails"
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
        /** @var DirectInvoiceDetails $directInvoiceDetails */
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            return $this->sendError('Direct Invoice Details not found');
        }

        return $this->sendResponse($directInvoiceDetails->toArray(), 'Direct Invoice Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectInvoiceDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directInvoiceDetails/{id}",
     *      summary="Update the specified DirectInvoiceDetails in storage",
     *      tags={"DirectInvoiceDetails"},
     *      description="Update DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetails")
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
     *                  ref="#/definitions/DirectInvoiceDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectInvoiceDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectInvoiceDetails $directInvoiceDetails */
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            return $this->sendError('Direct Invoice Details not found');
        }

        $directInvoiceDetails = $this->directInvoiceDetailsRepository->update($input, $id);

        return $this->sendResponse($directInvoiceDetails->toArray(), 'DirectInvoiceDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directInvoiceDetails/{id}",
     *      summary="Remove the specified DirectInvoiceDetails from storage",
     *      tags={"DirectInvoiceDetails"},
     *      description="Delete DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetails",
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
        /** @var DirectInvoiceDetails $directInvoiceDetails */
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            return $this->sendError('Direct Invoice Details not found');
        }

        $directInvoiceDetails->delete();

        return $this->sendResponse($id, 'Direct Invoice Details deleted successfully');
    }
}
