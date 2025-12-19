<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateThirdPartyIntegrationKeysRequest;
use App\Http\Requests\UpdateThirdPartyIntegrationKeysRequest;
use App\Repositories\ThirdPartyIntegrationKeysRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ThirdPartyIntegrationKeysController extends AppBaseController
{
    /** @var  ThirdPartyIntegrationKeysRepository */
    private $thirdPartyIntegrationKeysRepository;

    public function __construct(ThirdPartyIntegrationKeysRepository $thirdPartyIntegrationKeysRepo)
    {
        $this->thirdPartyIntegrationKeysRepository = $thirdPartyIntegrationKeysRepo;
    }

    /**
     * Display a listing of the ThirdPartyIntegrationKeys.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->thirdPartyIntegrationKeysRepository->pushCriteria(new RequestCriteria($request));
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->all();

        return view('third_party_integration_keys.index')
            ->with('thirdPartyIntegrationKeys', $thirdPartyIntegrationKeys);
    }

    /**
     * Show the form for creating a new ThirdPartyIntegrationKeys.
     *
     * @return Response
     */
    public function create()
    {
        return view('third_party_integration_keys.create');
    }

    /**
     * Store a newly created ThirdPartyIntegrationKeys in storage.
     *
     * @param CreateThirdPartyIntegrationKeysRequest $request
     *
     * @return Response
     */
    public function store(CreateThirdPartyIntegrationKeysRequest $request)
    {
        $input = $request->all();

        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->create($input);

        Flash::success('Third Party Integration Keys saved successfully.');

        return redirect(route('thirdPartyIntegrationKeys.index'));
    }

    /**
     * Display the specified ThirdPartyIntegrationKeys.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->findWithoutFail($id);

        if (empty($thirdPartyIntegrationKeys)) {
            Flash::error('Third Party Integration Keys not found');

            return redirect(route('thirdPartyIntegrationKeys.index'));
        }

        return view('third_party_integration_keys.show')->with('thirdPartyIntegrationKeys', $thirdPartyIntegrationKeys);
    }

    /**
     * Show the form for editing the specified ThirdPartyIntegrationKeys.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->findWithoutFail($id);

        if (empty($thirdPartyIntegrationKeys)) {
            Flash::error('Third Party Integration Keys not found');

            return redirect(route('thirdPartyIntegrationKeys.index'));
        }

        return view('third_party_integration_keys.edit')->with('thirdPartyIntegrationKeys', $thirdPartyIntegrationKeys);
    }

    /**
     * Update the specified ThirdPartyIntegrationKeys in storage.
     *
     * @param  int              $id
     * @param UpdateThirdPartyIntegrationKeysRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateThirdPartyIntegrationKeysRequest $request)
    {
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->findWithoutFail($id);

        if (empty($thirdPartyIntegrationKeys)) {
            Flash::error('Third Party Integration Keys not found');

            return redirect(route('thirdPartyIntegrationKeys.index'));
        }

        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->update($request->all(), $id);

        Flash::success('Third Party Integration Keys updated successfully.');

        return redirect(route('thirdPartyIntegrationKeys.index'));
    }

    /**
     * Remove the specified ThirdPartyIntegrationKeys from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->findWithoutFail($id);

        if (empty($thirdPartyIntegrationKeys)) {
            Flash::error('Third Party Integration Keys not found');

            return redirect(route('thirdPartyIntegrationKeys.index'));
        }

        $this->thirdPartyIntegrationKeysRepository->delete($id);

        Flash::success('Third Party Integration Keys deleted successfully.');

        return redirect(route('thirdPartyIntegrationKeys.index'));
    }
}
