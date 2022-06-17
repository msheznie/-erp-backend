<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateThirdPartySystemsRequest;
use App\Http\Requests\UpdateThirdPartySystemsRequest;
use App\Repositories\ThirdPartySystemsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ThirdPartySystemsController extends AppBaseController
{
    /** @var  ThirdPartySystemsRepository */
    private $thirdPartySystemsRepository;

    public function __construct(ThirdPartySystemsRepository $thirdPartySystemsRepo)
    {
        $this->thirdPartySystemsRepository = $thirdPartySystemsRepo;
    }

    /**
     * Display a listing of the ThirdPartySystems.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->thirdPartySystemsRepository->pushCriteria(new RequestCriteria($request));
        $thirdPartySystems = $this->thirdPartySystemsRepository->all();

        return view('third_party_systems.index')
            ->with('thirdPartySystems', $thirdPartySystems);
    }

    /**
     * Show the form for creating a new ThirdPartySystems.
     *
     * @return Response
     */
    public function create()
    {
        return view('third_party_systems.create');
    }

    /**
     * Store a newly created ThirdPartySystems in storage.
     *
     * @param CreateThirdPartySystemsRequest $request
     *
     * @return Response
     */
    public function store(CreateThirdPartySystemsRequest $request)
    {
        $input = $request->all();

        $thirdPartySystems = $this->thirdPartySystemsRepository->create($input);

        Flash::success('Third Party Systems saved successfully.');

        return redirect(route('thirdPartySystems.index'));
    }

    /**
     * Display the specified ThirdPartySystems.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $thirdPartySystems = $this->thirdPartySystemsRepository->findWithoutFail($id);

        if (empty($thirdPartySystems)) {
            Flash::error('Third Party Systems not found');

            return redirect(route('thirdPartySystems.index'));
        }

        return view('third_party_systems.show')->with('thirdPartySystems', $thirdPartySystems);
    }

    /**
     * Show the form for editing the specified ThirdPartySystems.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $thirdPartySystems = $this->thirdPartySystemsRepository->findWithoutFail($id);

        if (empty($thirdPartySystems)) {
            Flash::error('Third Party Systems not found');

            return redirect(route('thirdPartySystems.index'));
        }

        return view('third_party_systems.edit')->with('thirdPartySystems', $thirdPartySystems);
    }

    /**
     * Update the specified ThirdPartySystems in storage.
     *
     * @param  int              $id
     * @param UpdateThirdPartySystemsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateThirdPartySystemsRequest $request)
    {
        $thirdPartySystems = $this->thirdPartySystemsRepository->findWithoutFail($id);

        if (empty($thirdPartySystems)) {
            Flash::error('Third Party Systems not found');

            return redirect(route('thirdPartySystems.index'));
        }

        $thirdPartySystems = $this->thirdPartySystemsRepository->update($request->all(), $id);

        Flash::success('Third Party Systems updated successfully.');

        return redirect(route('thirdPartySystems.index'));
    }

    /**
     * Remove the specified ThirdPartySystems from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $thirdPartySystems = $this->thirdPartySystemsRepository->findWithoutFail($id);

        if (empty($thirdPartySystems)) {
            Flash::error('Third Party Systems not found');

            return redirect(route('thirdPartySystems.index'));
        }

        $this->thirdPartySystemsRepository->delete($id);

        Flash::success('Third Party Systems deleted successfully.');

        return redirect(route('thirdPartySystems.index'));
    }
}
