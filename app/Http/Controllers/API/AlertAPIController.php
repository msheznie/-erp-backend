<?php
/**
 * =============================================
 * -- File Name : AlertAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Alert
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for Alert
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAlertAPIRequest;
use App\Http\Requests\API\UpdateAlertAPIRequest;
use App\Models\Alert;
use App\Repositories\AlertRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AlertController
 * @package App\Http\Controllers\API
 */

class AlertAPIController extends AppBaseController
{
    /** @var  AlertRepository */
    private $alertRepository;

    public function __construct(AlertRepository $alertRepo)
    {
        $this->alertRepository = $alertRepo;
    }

    /**
     * Display a listing of the Alert.
     * GET|HEAD /alerts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->alertRepository->pushCriteria(new RequestCriteria($request));
        $this->alertRepository->pushCriteria(new LimitOffsetCriteria($request));
        $alerts = $this->alertRepository->all();

        return $this->sendResponse($alerts->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.alerts')]));
    }

    /**
     * Store a newly created Alert in storage.
     * POST /alerts
     *
     * @param CreateAlertAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAlertAPIRequest $request)
    {
        $input = $request->all();

        $alerts = $this->alertRepository->create($input);

        return $this->sendResponse($alerts->toArray(), trans('custom.save', ['attribute' => trans('custom.alerts')]));
    }

    /**
     * Display the specified Alert.
     * GET|HEAD /alerts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Alert $alert */
        $alert = $this->alertRepository->findWithoutFail($id);

        if (empty($alert)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.alerts')]));
        }

        return $this->sendResponse($alert->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.alerts')]));
    }

    /**
     * Update the specified Alert in storage.
     * PUT/PATCH /alerts/{id}
     *
     * @param  int $id
     * @param UpdateAlertAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAlertAPIRequest $request)
    {
        $input = $request->all();

        /** @var Alert $alert */
        $alert = $this->alertRepository->findWithoutFail($id);

        if (empty($alert)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.alerts')]));
        }

        $alert = $this->alertRepository->update($input, $id);

        return $this->sendResponse($alert->toArray(), trans('custom.update', ['attribute' => trans('custom.alerts')]));
    }

    /**
     * Remove the specified Alert from storage.
     * DELETE /alerts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Alert $alert */
        $alert = $this->alertRepository->findWithoutFail($id);

        if (empty($alert)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.alerts')]));
        }

        $alert->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.alerts')]));
    }
}
