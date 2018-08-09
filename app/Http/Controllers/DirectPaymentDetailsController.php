<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDirectPaymentDetailsRequest;
use App\Http\Requests\UpdateDirectPaymentDetailsRequest;
use App\Repositories\DirectPaymentDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DirectPaymentDetailsController extends AppBaseController
{
    /** @var  DirectPaymentDetailsRepository */
    private $directPaymentDetailsRepository;

    public function __construct(DirectPaymentDetailsRepository $directPaymentDetailsRepo)
    {
        $this->directPaymentDetailsRepository = $directPaymentDetailsRepo;
    }

    /**
     * Display a listing of the DirectPaymentDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->directPaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $directPaymentDetails = $this->directPaymentDetailsRepository->all();

        return view('direct_payment_details.index')
            ->with('directPaymentDetails', $directPaymentDetails);
    }

    /**
     * Show the form for creating a new DirectPaymentDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('direct_payment_details.create');
    }

    /**
     * Store a newly created DirectPaymentDetails in storage.
     *
     * @param CreateDirectPaymentDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateDirectPaymentDetailsRequest $request)
    {
        $input = $request->all();

        $directPaymentDetails = $this->directPaymentDetailsRepository->create($input);

        Flash::success('Direct Payment Details saved successfully.');

        return redirect(route('directPaymentDetails.index'));
    }

    /**
     * Display the specified DirectPaymentDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            Flash::error('Direct Payment Details not found');

            return redirect(route('directPaymentDetails.index'));
        }

        return view('direct_payment_details.show')->with('directPaymentDetails', $directPaymentDetails);
    }

    /**
     * Show the form for editing the specified DirectPaymentDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            Flash::error('Direct Payment Details not found');

            return redirect(route('directPaymentDetails.index'));
        }

        return view('direct_payment_details.edit')->with('directPaymentDetails', $directPaymentDetails);
    }

    /**
     * Update the specified DirectPaymentDetails in storage.
     *
     * @param  int              $id
     * @param UpdateDirectPaymentDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDirectPaymentDetailsRequest $request)
    {
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            Flash::error('Direct Payment Details not found');

            return redirect(route('directPaymentDetails.index'));
        }

        $directPaymentDetails = $this->directPaymentDetailsRepository->update($request->all(), $id);

        Flash::success('Direct Payment Details updated successfully.');

        return redirect(route('directPaymentDetails.index'));
    }

    /**
     * Remove the specified DirectPaymentDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            Flash::error('Direct Payment Details not found');

            return redirect(route('directPaymentDetails.index'));
        }

        $this->directPaymentDetailsRepository->delete($id);

        Flash::success('Direct Payment Details deleted successfully.');

        return redirect(route('directPaymentDetails.index'));
    }
}
