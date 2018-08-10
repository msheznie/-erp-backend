<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFreeBillingMasterPerformaRequest;
use App\Http\Requests\UpdateFreeBillingMasterPerformaRequest;
use App\Repositories\FreeBillingMasterPerformaRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FreeBillingMasterPerformaController extends AppBaseController
{
    /** @var  FreeBillingMasterPerformaRepository */
    private $freeBillingMasterPerformaRepository;

    public function __construct(FreeBillingMasterPerformaRepository $freeBillingMasterPerformaRepo)
    {
        $this->freeBillingMasterPerformaRepository = $freeBillingMasterPerformaRepo;
    }

    /**
     * Display a listing of the FreeBillingMasterPerforma.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->freeBillingMasterPerformaRepository->pushCriteria(new RequestCriteria($request));
        $freeBillingMasterPerformas = $this->freeBillingMasterPerformaRepository->all();

        return view('free_billing_master_performas.index')
            ->with('freeBillingMasterPerformas', $freeBillingMasterPerformas);
    }

    /**
     * Show the form for creating a new FreeBillingMasterPerforma.
     *
     * @return Response
     */
    public function create()
    {
        return view('free_billing_master_performas.create');
    }

    /**
     * Store a newly created FreeBillingMasterPerforma in storage.
     *
     * @param CreateFreeBillingMasterPerformaRequest $request
     *
     * @return Response
     */
    public function store(CreateFreeBillingMasterPerformaRequest $request)
    {
        $input = $request->all();

        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->create($input);

        Flash::success('Free Billing Master Performa saved successfully.');

        return redirect(route('freeBillingMasterPerformas.index'));
    }

    /**
     * Display the specified FreeBillingMasterPerforma.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->findWithoutFail($id);

        if (empty($freeBillingMasterPerforma)) {
            Flash::error('Free Billing Master Performa not found');

            return redirect(route('freeBillingMasterPerformas.index'));
        }

        return view('free_billing_master_performas.show')->with('freeBillingMasterPerforma', $freeBillingMasterPerforma);
    }

    /**
     * Show the form for editing the specified FreeBillingMasterPerforma.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->findWithoutFail($id);

        if (empty($freeBillingMasterPerforma)) {
            Flash::error('Free Billing Master Performa not found');

            return redirect(route('freeBillingMasterPerformas.index'));
        }

        return view('free_billing_master_performas.edit')->with('freeBillingMasterPerforma', $freeBillingMasterPerforma);
    }

    /**
     * Update the specified FreeBillingMasterPerforma in storage.
     *
     * @param  int              $id
     * @param UpdateFreeBillingMasterPerformaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFreeBillingMasterPerformaRequest $request)
    {
        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->findWithoutFail($id);

        if (empty($freeBillingMasterPerforma)) {
            Flash::error('Free Billing Master Performa not found');

            return redirect(route('freeBillingMasterPerformas.index'));
        }

        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->update($request->all(), $id);

        Flash::success('Free Billing Master Performa updated successfully.');

        return redirect(route('freeBillingMasterPerformas.index'));
    }

    /**
     * Remove the specified FreeBillingMasterPerforma from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $freeBillingMasterPerforma = $this->freeBillingMasterPerformaRepository->findWithoutFail($id);

        if (empty($freeBillingMasterPerforma)) {
            Flash::error('Free Billing Master Performa not found');

            return redirect(route('freeBillingMasterPerformas.index'));
        }

        $this->freeBillingMasterPerformaRepository->delete($id);

        Flash::success('Free Billing Master Performa deleted successfully.');

        return redirect(route('freeBillingMasterPerformas.index'));
    }
}
