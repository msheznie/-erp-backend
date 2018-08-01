<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Repositories\ContractRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ContractController extends AppBaseController
{
    /** @var  ContractRepository */
    private $contractRepository;

    public function __construct(ContractRepository $contractRepo)
    {
        $this->contractRepository = $contractRepo;
    }

    /**
     * Display a listing of the Contract.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->contractRepository->pushCriteria(new RequestCriteria($request));
        $contracts = $this->contractRepository->all();

        return view('contracts.index')
            ->with('contracts', $contracts);
    }

    /**
     * Show the form for creating a new Contract.
     *
     * @return Response
     */
    public function create()
    {
        return view('contracts.create');
    }

    /**
     * Store a newly created Contract in storage.
     *
     * @param CreateContractRequest $request
     *
     * @return Response
     */
    public function store(CreateContractRequest $request)
    {
        $input = $request->all();

        $contract = $this->contractRepository->create($input);

        Flash::success('Contract saved successfully.');

        return redirect(route('contracts.index'));
    }

    /**
     * Display the specified Contract.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $contract = $this->contractRepository->findWithoutFail($id);

        if (empty($contract)) {
            Flash::error('Contract not found');

            return redirect(route('contracts.index'));
        }

        return view('contracts.show')->with('contract', $contract);
    }

    /**
     * Show the form for editing the specified Contract.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $contract = $this->contractRepository->findWithoutFail($id);

        if (empty($contract)) {
            Flash::error('Contract not found');

            return redirect(route('contracts.index'));
        }

        return view('contracts.edit')->with('contract', $contract);
    }

    /**
     * Update the specified Contract in storage.
     *
     * @param  int              $id
     * @param UpdateContractRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateContractRequest $request)
    {
        $contract = $this->contractRepository->findWithoutFail($id);

        if (empty($contract)) {
            Flash::error('Contract not found');

            return redirect(route('contracts.index'));
        }

        $contract = $this->contractRepository->update($request->all(), $id);

        Flash::success('Contract updated successfully.');

        return redirect(route('contracts.index'));
    }

    /**
     * Remove the specified Contract from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $contract = $this->contractRepository->findWithoutFail($id);

        if (empty($contract)) {
            Flash::error('Contract not found');

            return redirect(route('contracts.index'));
        }

        $this->contractRepository->delete($id);

        Flash::success('Contract deleted successfully.');

        return redirect(route('contracts.index'));
    }
}
