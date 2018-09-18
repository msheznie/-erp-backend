<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFreeBillingRequest;
use App\Http\Requests\UpdateFreeBillingRequest;
use App\Repositories\FreeBillingRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FreeBillingController extends AppBaseController
{
    /** @var  FreeBillingRepository */
    private $freeBillingRepository;

    public function __construct(FreeBillingRepository $freeBillingRepo)
    {
        $this->freeBillingRepository = $freeBillingRepo;
    }

    /**
     * Display a listing of the FreeBilling.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->freeBillingRepository->pushCriteria(new RequestCriteria($request));
        $freeBillings = $this->freeBillingRepository->all();

        return view('free_billings.index')
            ->with('freeBillings', $freeBillings);
    }

    /**
     * Show the form for creating a new FreeBilling.
     *
     * @return Response
     */
    public function create()
    {
        return view('free_billings.create');
    }

    /**
     * Store a newly created FreeBilling in storage.
     *
     * @param CreateFreeBillingRequest $request
     *
     * @return Response
     */
    public function store(CreateFreeBillingRequest $request)
    {
        $input = $request->all();

        $freeBilling = $this->freeBillingRepository->create($input);

        Flash::success('Free Billing saved successfully.');

        return redirect(route('freeBillings.index'));
    }

    /**
     * Display the specified FreeBilling.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $freeBilling = $this->freeBillingRepository->findWithoutFail($id);

        if (empty($freeBilling)) {
            Flash::error('Free Billing not found');

            return redirect(route('freeBillings.index'));
        }

        return view('free_billings.show')->with('freeBilling', $freeBilling);
    }

    /**
     * Show the form for editing the specified FreeBilling.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $freeBilling = $this->freeBillingRepository->findWithoutFail($id);

        if (empty($freeBilling)) {
            Flash::error('Free Billing not found');

            return redirect(route('freeBillings.index'));
        }

        return view('free_billings.edit')->with('freeBilling', $freeBilling);
    }

    /**
     * Update the specified FreeBilling in storage.
     *
     * @param  int              $id
     * @param UpdateFreeBillingRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFreeBillingRequest $request)
    {
        $freeBilling = $this->freeBillingRepository->findWithoutFail($id);

        if (empty($freeBilling)) {
            Flash::error('Free Billing not found');

            return redirect(route('freeBillings.index'));
        }

        $freeBilling = $this->freeBillingRepository->update($request->all(), $id);

        Flash::success('Free Billing updated successfully.');

        return redirect(route('freeBillings.index'));
    }

    /**
     * Remove the specified FreeBilling from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $freeBilling = $this->freeBillingRepository->findWithoutFail($id);

        if (empty($freeBilling)) {
            Flash::error('Free Billing not found');

            return redirect(route('freeBillings.index'));
        }

        $this->freeBillingRepository->delete($id);

        Flash::success('Free Billing deleted successfully.');

        return redirect(route('freeBillings.index'));
    }
}
