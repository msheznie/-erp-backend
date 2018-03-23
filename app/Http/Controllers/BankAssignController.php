<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankAssignRequest;
use App\Http\Requests\UpdateBankAssignRequest;
use App\Repositories\BankAssignRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankAssignController extends AppBaseController
{
    /** @var  BankAssignRepository */
    private $bankAssignRepository;

    public function __construct(BankAssignRepository $bankAssignRepo)
    {
        $this->bankAssignRepository = $bankAssignRepo;
    }

    /**
     * Display a listing of the BankAssign.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankAssignRepository->pushCriteria(new RequestCriteria($request));
        $bankAssigns = $this->bankAssignRepository->all();

        return view('bank_assigns.index')
            ->with('bankAssigns', $bankAssigns);
    }

    /**
     * Show the form for creating a new BankAssign.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_assigns.create');
    }

    /**
     * Store a newly created BankAssign in storage.
     *
     * @param CreateBankAssignRequest $request
     *
     * @return Response
     */
    public function store(CreateBankAssignRequest $request)
    {
        $input = $request->all();

        $bankAssign = $this->bankAssignRepository->create($input);

        Flash::success('Bank Assign saved successfully.');

        return redirect(route('bankAssigns.index'));
    }

    /**
     * Display the specified BankAssign.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankAssign = $this->bankAssignRepository->findWithoutFail($id);

        if (empty($bankAssign)) {
            Flash::error('Bank Assign not found');

            return redirect(route('bankAssigns.index'));
        }

        return view('bank_assigns.show')->with('bankAssign', $bankAssign);
    }

    /**
     * Show the form for editing the specified BankAssign.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankAssign = $this->bankAssignRepository->findWithoutFail($id);

        if (empty($bankAssign)) {
            Flash::error('Bank Assign not found');

            return redirect(route('bankAssigns.index'));
        }

        return view('bank_assigns.edit')->with('bankAssign', $bankAssign);
    }

    /**
     * Update the specified BankAssign in storage.
     *
     * @param  int              $id
     * @param UpdateBankAssignRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankAssignRequest $request)
    {
        $bankAssign = $this->bankAssignRepository->findWithoutFail($id);

        if (empty($bankAssign)) {
            Flash::error('Bank Assign not found');

            return redirect(route('bankAssigns.index'));
        }

        $bankAssign = $this->bankAssignRepository->update($request->all(), $id);

        Flash::success('Bank Assign updated successfully.');

        return redirect(route('bankAssigns.index'));
    }

    /**
     * Remove the specified BankAssign from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankAssign = $this->bankAssignRepository->findWithoutFail($id);

        if (empty($bankAssign)) {
            Flash::error('Bank Assign not found');

            return redirect(route('bankAssigns.index'));
        }

        $this->bankAssignRepository->delete($id);

        Flash::success('Bank Assign deleted successfully.');

        return redirect(route('bankAssigns.index'));
    }
}
