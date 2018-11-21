<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDirectPaymentReferbackRequest;
use App\Http\Requests\UpdateDirectPaymentReferbackRequest;
use App\Repositories\DirectPaymentReferbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DirectPaymentReferbackController extends AppBaseController
{
    /** @var  DirectPaymentReferbackRepository */
    private $directPaymentReferbackRepository;

    public function __construct(DirectPaymentReferbackRepository $directPaymentReferbackRepo)
    {
        $this->directPaymentReferbackRepository = $directPaymentReferbackRepo;
    }

    /**
     * Display a listing of the DirectPaymentReferback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->directPaymentReferbackRepository->pushCriteria(new RequestCriteria($request));
        $directPaymentReferbacks = $this->directPaymentReferbackRepository->all();

        return view('direct_payment_referbacks.index')
            ->with('directPaymentReferbacks', $directPaymentReferbacks);
    }

    /**
     * Show the form for creating a new DirectPaymentReferback.
     *
     * @return Response
     */
    public function create()
    {
        return view('direct_payment_referbacks.create');
    }

    /**
     * Store a newly created DirectPaymentReferback in storage.
     *
     * @param CreateDirectPaymentReferbackRequest $request
     *
     * @return Response
     */
    public function store(CreateDirectPaymentReferbackRequest $request)
    {
        $input = $request->all();

        $directPaymentReferback = $this->directPaymentReferbackRepository->create($input);

        Flash::success('Direct Payment Referback saved successfully.');

        return redirect(route('directPaymentReferbacks.index'));
    }

    /**
     * Display the specified DirectPaymentReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            Flash::error('Direct Payment Referback not found');

            return redirect(route('directPaymentReferbacks.index'));
        }

        return view('direct_payment_referbacks.show')->with('directPaymentReferback', $directPaymentReferback);
    }

    /**
     * Show the form for editing the specified DirectPaymentReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            Flash::error('Direct Payment Referback not found');

            return redirect(route('directPaymentReferbacks.index'));
        }

        return view('direct_payment_referbacks.edit')->with('directPaymentReferback', $directPaymentReferback);
    }

    /**
     * Update the specified DirectPaymentReferback in storage.
     *
     * @param  int              $id
     * @param UpdateDirectPaymentReferbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDirectPaymentReferbackRequest $request)
    {
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            Flash::error('Direct Payment Referback not found');

            return redirect(route('directPaymentReferbacks.index'));
        }

        $directPaymentReferback = $this->directPaymentReferbackRepository->update($request->all(), $id);

        Flash::success('Direct Payment Referback updated successfully.');

        return redirect(route('directPaymentReferbacks.index'));
    }

    /**
     * Remove the specified DirectPaymentReferback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            Flash::error('Direct Payment Referback not found');

            return redirect(route('directPaymentReferbacks.index'));
        }

        $this->directPaymentReferbackRepository->delete($id);

        Flash::success('Direct Payment Referback deleted successfully.');

        return redirect(route('directPaymentReferbacks.index'));
    }
}
