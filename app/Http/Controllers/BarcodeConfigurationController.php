<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBarcodeConfigurationRequest;
use App\Http\Requests\UpdateBarcodeConfigurationRequest;
use App\Repositories\BarcodeConfigurationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BarcodeConfigurationController extends AppBaseController
{
    /** @var  BarcodeConfigurationRepository */
    private $barcodeConfigurationRepository;

    public function __construct(BarcodeConfigurationRepository $barcodeConfigurationRepo)
    {
        $this->barcodeConfigurationRepository = $barcodeConfigurationRepo;
    }

    /**
     * Display a listing of the BarcodeConfiguration.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->barcodeConfigurationRepository->pushCriteria(new RequestCriteria($request));
        $barcodeConfigurations = $this->barcodeConfigurationRepository->all();

        return view('barcode_configurations.index')
            ->with('barcodeConfigurations', $barcodeConfigurations);
    }

    /**
     * Show the form for creating a new BarcodeConfiguration.
     *
     * @return Response
     */
    public function create()
    {
        return view('barcode_configurations.create');
    }

    /**
     * Store a newly created BarcodeConfiguration in storage.
     *
     * @param CreateBarcodeConfigurationRequest $request
     *
     * @return Response
     */
    public function store(CreateBarcodeConfigurationRequest $request)
    {
        $input = $request->all();

        $barcodeConfiguration = $this->barcodeConfigurationRepository->create($input);

        Flash::success('Barcode Configuration saved successfully.');

        return redirect(route('barcodeConfigurations.index'));
    }

    /**
     * Display the specified BarcodeConfiguration.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $barcodeConfiguration = $this->barcodeConfigurationRepository->findWithoutFail($id);

        if (empty($barcodeConfiguration)) {
            Flash::error('Barcode Configuration not found');

            return redirect(route('barcodeConfigurations.index'));
        }

        return view('barcode_configurations.show')->with('barcodeConfiguration', $barcodeConfiguration);
    }

    /**
     * Show the form for editing the specified BarcodeConfiguration.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $barcodeConfiguration = $this->barcodeConfigurationRepository->findWithoutFail($id);

        if (empty($barcodeConfiguration)) {
            Flash::error('Barcode Configuration not found');

            return redirect(route('barcodeConfigurations.index'));
        }

        return view('barcode_configurations.edit')->with('barcodeConfiguration', $barcodeConfiguration);
    }

    /**
     * Update the specified BarcodeConfiguration in storage.
     *
     * @param  int              $id
     * @param UpdateBarcodeConfigurationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBarcodeConfigurationRequest $request)
    {
        $barcodeConfiguration = $this->barcodeConfigurationRepository->findWithoutFail($id);

        if (empty($barcodeConfiguration)) {
            Flash::error('Barcode Configuration not found');

            return redirect(route('barcodeConfigurations.index'));
        }

        $barcodeConfiguration = $this->barcodeConfigurationRepository->update($request->all(), $id);

        Flash::success('Barcode Configuration updated successfully.');

        return redirect(route('barcodeConfigurations.index'));
    }

    /**
     * Remove the specified BarcodeConfiguration from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $barcodeConfiguration = $this->barcodeConfigurationRepository->findWithoutFail($id);

        if (empty($barcodeConfiguration)) {
            Flash::error('Barcode Configuration not found');

            return redirect(route('barcodeConfigurations.index'));
        }

        $this->barcodeConfigurationRepository->delete($id);

        Flash::success('Barcode Configuration deleted successfully.');

        return redirect(route('barcodeConfigurations.index'));
    }
}
