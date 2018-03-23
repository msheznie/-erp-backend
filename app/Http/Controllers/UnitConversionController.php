<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUnitConversionRequest;
use App\Http\Requests\UpdateUnitConversionRequest;
use App\Repositories\UnitConversionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UnitConversionController extends AppBaseController
{
    /** @var  UnitConversionRepository */
    private $unitConversionRepository;

    public function __construct(UnitConversionRepository $unitConversionRepo)
    {
        $this->unitConversionRepository = $unitConversionRepo;
    }

    /**
     * Display a listing of the UnitConversion.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->unitConversionRepository->pushCriteria(new RequestCriteria($request));
        $unitConversions = $this->unitConversionRepository->all();

        return view('unit_conversions.index')
            ->with('unitConversions', $unitConversions);
    }

    /**
     * Show the form for creating a new UnitConversion.
     *
     * @return Response
     */
    public function create()
    {
        return view('unit_conversions.create');
    }

    /**
     * Store a newly created UnitConversion in storage.
     *
     * @param CreateUnitConversionRequest $request
     *
     * @return Response
     */
    public function store(CreateUnitConversionRequest $request)
    {
        $input = $request->all();

        $unitConversion = $this->unitConversionRepository->create($input);

        Flash::success('Unit Conversion saved successfully.');

        return redirect(route('unitConversions.index'));
    }

    /**
     * Display the specified UnitConversion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $unitConversion = $this->unitConversionRepository->findWithoutFail($id);

        if (empty($unitConversion)) {
            Flash::error('Unit Conversion not found');

            return redirect(route('unitConversions.index'));
        }

        return view('unit_conversions.show')->with('unitConversion', $unitConversion);
    }

    /**
     * Show the form for editing the specified UnitConversion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $unitConversion = $this->unitConversionRepository->findWithoutFail($id);

        if (empty($unitConversion)) {
            Flash::error('Unit Conversion not found');

            return redirect(route('unitConversions.index'));
        }

        return view('unit_conversions.edit')->with('unitConversion', $unitConversion);
    }

    /**
     * Update the specified UnitConversion in storage.
     *
     * @param  int              $id
     * @param UpdateUnitConversionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUnitConversionRequest $request)
    {
        $unitConversion = $this->unitConversionRepository->findWithoutFail($id);

        if (empty($unitConversion)) {
            Flash::error('Unit Conversion not found');

            return redirect(route('unitConversions.index'));
        }

        $unitConversion = $this->unitConversionRepository->update($request->all(), $id);

        Flash::success('Unit Conversion updated successfully.');

        return redirect(route('unitConversions.index'));
    }

    /**
     * Remove the specified UnitConversion from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $unitConversion = $this->unitConversionRepository->findWithoutFail($id);

        if (empty($unitConversion)) {
            Flash::error('Unit Conversion not found');

            return redirect(route('unitConversions.index'));
        }

        $this->unitConversionRepository->delete($id);

        Flash::success('Unit Conversion deleted successfully.');

        return redirect(route('unitConversions.index'));
    }
}
