<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankMasterRequest;
use App\Http\Requests\UpdateBankMasterRequest;
use App\Repositories\BankMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankMasterController extends AppBaseController
{
    /** @var  BankMasterRepository */
    private $bankMasterRepository;

    public function __construct(BankMasterRepository $bankMasterRepo)
    {
        $this->bankMasterRepository = $bankMasterRepo;
    }

    /**
     * Display a listing of the BankMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankMasterRepository->pushCriteria(new RequestCriteria($request));
        $bankMasters = $this->bankMasterRepository->all();

        return view('bank_masters.index')
            ->with('bankMasters', $bankMasters);
    }

    /**
     * Show the form for creating a new BankMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_masters.create');
    }

    /**
     * Store a newly created BankMaster in storage.
     *
     * @param CreateBankMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateBankMasterRequest $request)
    {
        $input = $request->all();

        $bankMaster = $this->bankMasterRepository->create($input);

        Flash::success('Bank Master saved successfully.');

        return redirect(route('bankMasters.index'));
    }

    /**
     * Display the specified BankMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankMaster = $this->bankMasterRepository->findWithoutFail($id);

        if (empty($bankMaster)) {
            Flash::error('Bank Master not found');

            return redirect(route('bankMasters.index'));
        }

        return view('bank_masters.show')->with('bankMaster', $bankMaster);
    }

    /**
     * Show the form for editing the specified BankMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankMaster = $this->bankMasterRepository->findWithoutFail($id);

        if (empty($bankMaster)) {
            Flash::error('Bank Master not found');

            return redirect(route('bankMasters.index'));
        }

        return view('bank_masters.edit')->with('bankMaster', $bankMaster);
    }

    /**
     * Update the specified BankMaster in storage.
     *
     * @param  int              $id
     * @param UpdateBankMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankMasterRequest $request)
    {
        $bankMaster = $this->bankMasterRepository->findWithoutFail($id);

        if (empty($bankMaster)) {
            Flash::error('Bank Master not found');

            return redirect(route('bankMasters.index'));
        }

        $bankMaster = $this->bankMasterRepository->update($request->all(), $id);

        Flash::success('Bank Master updated successfully.');

        return redirect(route('bankMasters.index'));
    }

    /**
     * Remove the specified BankMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankMaster = $this->bankMasterRepository->findWithoutFail($id);

        if (empty($bankMaster)) {
            Flash::error('Bank Master not found');

            return redirect(route('bankMasters.index'));
        }

        $this->bankMasterRepository->delete($id);

        Flash::success('Bank Master deleted successfully.');

        return redirect(route('bankMasters.index'));
    }
}
