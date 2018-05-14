<?php
/**
 * =============================================
 * -- File Name : GRVDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Details
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for GRV Details
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGRVDetailsAPIRequest;
use App\Http\Requests\API\UpdateGRVDetailsAPIRequest;
use App\Models\GRVDetails;
use App\Repositories\GRVDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GRVDetailsController
 * @package App\Http\Controllers\API
 */

class GRVDetailsAPIController extends AppBaseController
{
    /** @var  GRVDetailsRepository */
    private $gRVDetailsRepository;

    public function __construct(GRVDetailsRepository $gRVDetailsRepo)
    {
        $this->gRVDetailsRepository = $gRVDetailsRepo;
    }

    /**
     * Display a listing of the GRVDetails.
     * GET|HEAD /gRVDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->gRVDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gRVDetails = $this->gRVDetailsRepository->all();

        return $this->sendResponse($gRVDetails->toArray(), 'G R V Details retrieved successfully');
    }

    /**
     * Store a newly created GRVDetails in storage.
     * POST /gRVDetails
     *
     * @param CreateGRVDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVDetailsAPIRequest $request)
    {
        $input = $request->all();

        $gRVDetails = $this->gRVDetailsRepository->create($input);

        return $this->sendResponse($gRVDetails->toArray(), 'G R V Details saved successfully');
    }

    /**
     * Display the specified GRVDetails.
     * GET|HEAD /gRVDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var GRVDetails $gRVDetails */
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            return $this->sendError('G R V Details not found');
        }

        return $this->sendResponse($gRVDetails->toArray(), 'G R V Details retrieved successfully');
    }

    /**
     * Update the specified GRVDetails in storage.
     * PUT/PATCH /gRVDetails/{id}
     *
     * @param  int $id
     * @param UpdateGRVDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var GRVDetails $gRVDetails */
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            return $this->sendError('G R V Details not found');
        }

        $gRVDetails = $this->gRVDetailsRepository->update($input, $id);

        return $this->sendResponse($gRVDetails->toArray(), 'GRVDetails updated successfully');
    }

    /**
     * Remove the specified GRVDetails from storage.
     * DELETE /gRVDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var GRVDetails $gRVDetails */
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            return $this->sendError('G R V Details not found');
        }

        $gRVDetails->delete();

        return $this->sendResponse($id, 'G R V Details deleted successfully');
    }
}
