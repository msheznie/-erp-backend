<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateErpItemLedgerRequest;
use App\Http\Requests\UpdateErpItemLedgerRequest;
use App\Repositories\ErpItemLedgerRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ErpItemLedgerController extends AppBaseController
{
    /** @var  ErpItemLedgerRepository */
    private $erpItemLedgerRepository;

    public function __construct(ErpItemLedgerRepository $erpItemLedgerRepo)
    {
        $this->erpItemLedgerRepository = $erpItemLedgerRepo;
    }

    /**
     * Display a listing of the ErpItemLedger.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->erpItemLedgerRepository->pushCriteria(new RequestCriteria($request));
        $erpItemLedgers = $this->erpItemLedgerRepository->all();

        return view('erp_item_ledgers.index')
            ->with('erpItemLedgers', $erpItemLedgers);
    }

    /**
     * Show the form for creating a new ErpItemLedger.
     *
     * @return Response
     */
    public function create()
    {
        return view('erp_item_ledgers.create');
    }

    /**
     * Store a newly created ErpItemLedger in storage.
     *
     * @param CreateErpItemLedgerRequest $request
     *
     * @return Response
     */
    public function store(CreateErpItemLedgerRequest $request)
    {
        $input = $request->all();

        $erpItemLedger = $this->erpItemLedgerRepository->create($input);

        Flash::success('Erp Item Ledger saved successfully.');

        return redirect(route('erpItemLedgers.index'));
    }

    /**
     * Display the specified ErpItemLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            Flash::error('Erp Item Ledger not found');

            return redirect(route('erpItemLedgers.index'));
        }

        return view('erp_item_ledgers.show')->with('erpItemLedger', $erpItemLedger);
    }

    /**
     * Show the form for editing the specified ErpItemLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            Flash::error('Erp Item Ledger not found');

            return redirect(route('erpItemLedgers.index'));
        }

        return view('erp_item_ledgers.edit')->with('erpItemLedger', $erpItemLedger);
    }

    /**
     * Update the specified ErpItemLedger in storage.
     *
     * @param  int              $id
     * @param UpdateErpItemLedgerRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateErpItemLedgerRequest $request)
    {
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            Flash::error('Erp Item Ledger not found');

            return redirect(route('erpItemLedgers.index'));
        }

        $erpItemLedger = $this->erpItemLedgerRepository->update($request->all(), $id);

        Flash::success('Erp Item Ledger updated successfully.');

        return redirect(route('erpItemLedgers.index'));
    }

    /**
     * Remove the specified ErpItemLedger from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            Flash::error('Erp Item Ledger not found');

            return redirect(route('erpItemLedgers.index'));
        }

        $this->erpItemLedgerRepository->delete($id);

        Flash::success('Erp Item Ledger deleted successfully.');

        return redirect(route('erpItemLedgers.index'));
    }
}
