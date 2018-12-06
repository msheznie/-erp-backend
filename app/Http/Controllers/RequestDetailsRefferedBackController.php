<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRequestDetailsRefferedBackRequest;
use App\Http\Requests\UpdateRequestDetailsRefferedBackRequest;
use App\Repositories\RequestDetailsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RequestDetailsRefferedBackController extends AppBaseController
{
    /** @var  RequestDetailsRefferedBackRepository */
    private $requestDetailsRefferedBackRepository;

    public function __construct(RequestDetailsRefferedBackRepository $requestDetailsRefferedBackRepo)
    {
        $this->requestDetailsRefferedBackRepository = $requestDetailsRefferedBackRepo;
    }

    /**
     * Display a listing of the RequestDetailsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->requestDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $requestDetailsRefferedBacks = $this->requestDetailsRefferedBackRepository->all();

        return view('request_details_reffered_backs.index')
            ->with('requestDetailsRefferedBacks', $requestDetailsRefferedBacks);
    }

    /**
     * Show the form for creating a new RequestDetailsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('request_details_reffered_backs.create');
    }

    /**
     * Store a newly created RequestDetailsRefferedBack in storage.
     *
     * @param CreateRequestDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateRequestDetailsRefferedBackRequest $request)
    {
        $input = $request->all();

        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->create($input);

        Flash::success('Request Details Reffered Back saved successfully.');

        return redirect(route('requestDetailsRefferedBacks.index'));
    }

    /**
     * Display the specified RequestDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($requestDetailsRefferedBack)) {
            Flash::error('Request Details Reffered Back not found');

            return redirect(route('requestDetailsRefferedBacks.index'));
        }

        return view('request_details_reffered_backs.show')->with('requestDetailsRefferedBack', $requestDetailsRefferedBack);
    }

    /**
     * Show the form for editing the specified RequestDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($requestDetailsRefferedBack)) {
            Flash::error('Request Details Reffered Back not found');

            return redirect(route('requestDetailsRefferedBacks.index'));
        }

        return view('request_details_reffered_backs.edit')->with('requestDetailsRefferedBack', $requestDetailsRefferedBack);
    }

    /**
     * Update the specified RequestDetailsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateRequestDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRequestDetailsRefferedBackRequest $request)
    {
        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($requestDetailsRefferedBack)) {
            Flash::error('Request Details Reffered Back not found');

            return redirect(route('requestDetailsRefferedBacks.index'));
        }

        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Request Details Reffered Back updated successfully.');

        return redirect(route('requestDetailsRefferedBacks.index'));
    }

    /**
     * Remove the specified RequestDetailsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($requestDetailsRefferedBack)) {
            Flash::error('Request Details Reffered Back not found');

            return redirect(route('requestDetailsRefferedBacks.index'));
        }

        $this->requestDetailsRefferedBackRepository->delete($id);

        Flash::success('Request Details Reffered Back deleted successfully.');

        return redirect(route('requestDetailsRefferedBacks.index'));
    }
}
