<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdvancePaymentDetailsAPIRequest;
use App\Http\Requests\API\UpdateAdvancePaymentDetailsAPIRequest;
use App\Models\AdvancePaymentDetails;
use App\Repositories\AdvancePaymentDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AdvancePaymentDetailsController
 * @package App\Http\Controllers\API
 */

class AdvancePaymentDetailsAPIController extends AppBaseController
{
    /** @var  AdvancePaymentDetailsRepository */
    private $advancePaymentDetailsRepository;

    public function __construct(AdvancePaymentDetailsRepository $advancePaymentDetailsRepo)
    {
        $this->advancePaymentDetailsRepository = $advancePaymentDetailsRepo;
    }

    /**
     * Display a listing of the AdvancePaymentDetails.
     * GET|HEAD /advancePaymentDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->advancePaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->advancePaymentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->all();

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details retrieved successfully');
    }

    /**
     * Store a newly created AdvancePaymentDetails in storage.
     * POST /advancePaymentDetails
     *
     * @param CreateAdvancePaymentDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAdvancePaymentDetailsAPIRequest $request)
    {
        $input = $request->all();

        $advancePaymentDetails = $this->advancePaymentDetailsRepository->create($input);

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details saved successfully');
    }

    /**
     * Display the specified AdvancePaymentDetails.
     * GET|HEAD /advancePaymentDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AdvancePaymentDetails $advancePaymentDetails */
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            return $this->sendError('Advance Payment Details not found');
        }

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details retrieved successfully');
    }

    /**
     * Update the specified AdvancePaymentDetails in storage.
     * PUT/PATCH /advancePaymentDetails/{id}
     *
     * @param  int $id
     * @param UpdateAdvancePaymentDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAdvancePaymentDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var AdvancePaymentDetails $advancePaymentDetails */
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            return $this->sendError('Advance Payment Details not found');
        }

        $advancePaymentDetails = $this->advancePaymentDetailsRepository->update($input, $id);

        return $this->sendResponse($advancePaymentDetails->toArray(), 'AdvancePaymentDetails updated successfully');
    }

    /**
     * Remove the specified AdvancePaymentDetails from storage.
     * DELETE /advancePaymentDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AdvancePaymentDetails $advancePaymentDetails */
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            return $this->sendError('Advance Payment Details not found');
        }

        $advancePaymentDetails->delete();

        return $this->sendResponse($id, 'Advance Payment Details deleted successfully');
    }
}
