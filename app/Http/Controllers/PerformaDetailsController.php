<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePerformaDetailsRequest;
use App\Http\Requests\UpdatePerformaDetailsRequest;
use App\Repositories\PerformaDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PerformaDetailsController extends AppBaseController
{
    /** @var  PerformaDetailsRepository */
    private $performaDetailsRepository;

    public function __construct(PerformaDetailsRepository $performaDetailsRepo)
    {
        $this->performaDetailsRepository = $performaDetailsRepo;
    }

    /**
     * Display a listing of the PerformaDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->performaDetailsRepository->pushCriteria(new RequestCriteria($request));
        $performaDetails = $this->performaDetailsRepository->all();

        return view('performa_details.index')
            ->with('performaDetails', $performaDetails);
    }

    /**
     * Show the form for creating a new PerformaDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('performa_details.create');
    }

    /**
     * Store a newly created PerformaDetails in storage.
     *
     * @param CreatePerformaDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreatePerformaDetailsRequest $request)
    {
        $input = $request->all();

        $performaDetails = $this->performaDetailsRepository->create($input);

        Flash::success('Performa Details saved successfully.');

        return redirect(route('performaDetails.index'));
    }

    /**
     * Display the specified PerformaDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $performaDetails = $this->performaDetailsRepository->findWithoutFail($id);

        if (empty($performaDetails)) {
            Flash::error('Performa Details not found');

            return redirect(route('performaDetails.index'));
        }

        return view('performa_details.show')->with('performaDetails', $performaDetails);
    }

    /**
     * Show the form for editing the specified PerformaDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $performaDetails = $this->performaDetailsRepository->findWithoutFail($id);

        if (empty($performaDetails)) {
            Flash::error('Performa Details not found');

            return redirect(route('performaDetails.index'));
        }

        return view('performa_details.edit')->with('performaDetails', $performaDetails);
    }

    /**
     * Update the specified PerformaDetails in storage.
     *
     * @param  int              $id
     * @param UpdatePerformaDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePerformaDetailsRequest $request)
    {
        $performaDetails = $this->performaDetailsRepository->findWithoutFail($id);

        if (empty($performaDetails)) {
            Flash::error('Performa Details not found');

            return redirect(route('performaDetails.index'));
        }

        $performaDetails = $this->performaDetailsRepository->update($request->all(), $id);

        Flash::success('Performa Details updated successfully.');

        return redirect(route('performaDetails.index'));
    }

    /**
     * Remove the specified PerformaDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $performaDetails = $this->performaDetailsRepository->findWithoutFail($id);

        if (empty($performaDetails)) {
            Flash::error('Performa Details not found');

            return redirect(route('performaDetails.index'));
        }

        $this->performaDetailsRepository->delete($id);

        Flash::success('Performa Details deleted successfully.');

        return redirect(route('performaDetails.index'));
    }
}
