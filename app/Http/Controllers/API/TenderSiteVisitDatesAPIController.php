<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderSiteVisitDatesAPIRequest;
use App\Http\Requests\API\UpdateTenderSiteVisitDatesAPIRequest;
use App\Models\TenderSiteVisitDates;
use App\Repositories\TenderSiteVisitDatesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderSiteVisitDatesController
 * @package App\Http\Controllers\API
 */

class TenderSiteVisitDatesAPIController extends AppBaseController
{
    /** @var  TenderSiteVisitDatesRepository */
    private $tenderSiteVisitDatesRepository;

    public function __construct(TenderSiteVisitDatesRepository $tenderSiteVisitDatesRepo)
    {
        $this->tenderSiteVisitDatesRepository = $tenderSiteVisitDatesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderSiteVisitDates",
     *      summary="Get a listing of the TenderSiteVisitDates.",
     *      tags={"TenderSiteVisitDates"},
     *      description="Get all TenderSiteVisitDates",
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
     *                  @SWG\Items(ref="#/definitions/TenderSiteVisitDates")
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
        $this->tenderSiteVisitDatesRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderSiteVisitDatesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderSiteVisitDates = $this->tenderSiteVisitDatesRepository->all();

        return $this->sendResponse($tenderSiteVisitDates->toArray(), trans('custom.tender_site_visit_dates_retrieved_successfully'));
    }

    /**
     * @param CreateTenderSiteVisitDatesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderSiteVisitDates",
     *      summary="Store a newly created TenderSiteVisitDates in storage",
     *      tags={"TenderSiteVisitDates"},
     *      description="Store TenderSiteVisitDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderSiteVisitDates that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderSiteVisitDates")
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
     *                  ref="#/definitions/TenderSiteVisitDates"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderSiteVisitDatesAPIRequest $request)
    {
        $input = $request->all();

        $tenderSiteVisitDates = $this->tenderSiteVisitDatesRepository->create($input);

        return $this->sendResponse($tenderSiteVisitDates->toArray(), trans('custom.tender_site_visit_dates_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderSiteVisitDates/{id}",
     *      summary="Display the specified TenderSiteVisitDates",
     *      tags={"TenderSiteVisitDates"},
     *      description="Get TenderSiteVisitDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderSiteVisitDates",
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
     *                  ref="#/definitions/TenderSiteVisitDates"
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
        /** @var TenderSiteVisitDates $tenderSiteVisitDates */
        $tenderSiteVisitDates = $this->tenderSiteVisitDatesRepository->findWithoutFail($id);

        if (empty($tenderSiteVisitDates)) {
            return $this->sendError(trans('custom.tender_site_visit_dates_not_found'));
        }

        return $this->sendResponse($tenderSiteVisitDates->toArray(), trans('custom.tender_site_visit_dates_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderSiteVisitDatesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderSiteVisitDates/{id}",
     *      summary="Update the specified TenderSiteVisitDates in storage",
     *      tags={"TenderSiteVisitDates"},
     *      description="Update TenderSiteVisitDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderSiteVisitDates",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderSiteVisitDates that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderSiteVisitDates")
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
     *                  ref="#/definitions/TenderSiteVisitDates"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderSiteVisitDatesAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderSiteVisitDates $tenderSiteVisitDates */
        $tenderSiteVisitDates = $this->tenderSiteVisitDatesRepository->findWithoutFail($id);

        if (empty($tenderSiteVisitDates)) {
            return $this->sendError(trans('custom.tender_site_visit_dates_not_found'));
        }

        $tenderSiteVisitDates = $this->tenderSiteVisitDatesRepository->update($input, $id);

        return $this->sendResponse($tenderSiteVisitDates->toArray(), trans('custom.tendersitevisitdates_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderSiteVisitDates/{id}",
     *      summary="Remove the specified TenderSiteVisitDates from storage",
     *      tags={"TenderSiteVisitDates"},
     *      description="Delete TenderSiteVisitDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderSiteVisitDates",
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
        /** @var TenderSiteVisitDates $tenderSiteVisitDates */
        $tenderSiteVisitDates = $this->tenderSiteVisitDatesRepository->findWithoutFail($id);

        if (empty($tenderSiteVisitDates)) {
            return $this->sendError(trans('custom.tender_site_visit_dates_not_found'));
        }

        $tenderSiteVisitDates->delete();

        return $this->sendSuccess('Tender Site Visit Dates deleted successfully');
    }
}
