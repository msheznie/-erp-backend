<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRequestRefferedBackRequest;
use App\Http\Requests\UpdateRequestRefferedBackRequest;
use App\Repositories\RequestRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RequestRefferedBackController extends AppBaseController
{
    /** @var  RequestRefferedBackRepository */
    private $requestRefferedBackRepository;

    public function __construct(RequestRefferedBackRepository $requestRefferedBackRepo)
    {
        $this->requestRefferedBackRepository = $requestRefferedBackRepo;
    }

    /**
     * Display a listing of the RequestRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->requestRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $requestRefferedBacks = $this->requestRefferedBackRepository->all();

        return view('request_reffered_backs.index')
            ->with('requestRefferedBacks', $requestRefferedBacks);
    }

    /**
     * Show the form for creating a new RequestRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('request_reffered_backs.create');
    }

    /**
     * Store a newly created RequestRefferedBack in storage.
     *
     * @param CreateRequestRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateRequestRefferedBackRequest $request)
    {
        $input = $request->all();

        $requestRefferedBack = $this->requestRefferedBackRepository->create($input);

        Flash::success('Request Reffered Back saved successfully.');

        return redirect(route('requestRefferedBacks.index'));
    }

    /**
     * Display the specified RequestRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $requestRefferedBack = $this->requestRefferedBackRepository->findWithoutFail($id);

        if (empty($requestRefferedBack)) {
            Flash::error('Request Reffered Back not found');

            return redirect(route('requestRefferedBacks.index'));
        }

        return view('request_reffered_backs.show')->with('requestRefferedBack', $requestRefferedBack);
    }

    /**
     * Show the form for editing the specified RequestRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $requestRefferedBack = $this->requestRefferedBackRepository->findWithoutFail($id);

        if (empty($requestRefferedBack)) {
            Flash::error('Request Reffered Back not found');

            return redirect(route('requestRefferedBacks.index'));
        }

        return view('request_reffered_backs.edit')->with('requestRefferedBack', $requestRefferedBack);
    }

    /**
     * Update the specified RequestRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateRequestRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRequestRefferedBackRequest $request)
    {
        $requestRefferedBack = $this->requestRefferedBackRepository->findWithoutFail($id);

        if (empty($requestRefferedBack)) {
            Flash::error('Request Reffered Back not found');

            return redirect(route('requestRefferedBacks.index'));
        }

        $requestRefferedBack = $this->requestRefferedBackRepository->update($request->all(), $id);

        Flash::success('Request Reffered Back updated successfully.');

        return redirect(route('requestRefferedBacks.index'));
    }

    /**
     * Remove the specified RequestRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $requestRefferedBack = $this->requestRefferedBackRepository->findWithoutFail($id);

        if (empty($requestRefferedBack)) {
            Flash::error('Request Reffered Back not found');

            return redirect(route('requestRefferedBacks.index'));
        }

        $this->requestRefferedBackRepository->delete($id);

        Flash::success('Request Reffered Back deleted successfully.');

        return redirect(route('requestRefferedBacks.index'));
    }
}
