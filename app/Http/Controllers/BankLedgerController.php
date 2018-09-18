<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankLedgerRequest;
use App\Http\Requests\UpdateBankLedgerRequest;
use App\Repositories\BankLedgerRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankLedgerController extends AppBaseController
{
    /** @var  BankLedgerRepository */
    private $bankLedgerRepository;

    public function __construct(BankLedgerRepository $bankLedgerRepo)
    {
        $this->bankLedgerRepository = $bankLedgerRepo;
    }

    /**
     * Display a listing of the BankLedger.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankLedgerRepository->pushCriteria(new RequestCriteria($request));
        $bankLedgers = $this->bankLedgerRepository->all();

        return view('bank_ledgers.index')
            ->with('bankLedgers', $bankLedgers);
    }

    /**
     * Show the form for creating a new BankLedger.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_ledgers.create');
    }

    /**
     * Store a newly created BankLedger in storage.
     *
     * @param CreateBankLedgerRequest $request
     *
     * @return Response
     */
    public function store(CreateBankLedgerRequest $request)
    {
        $input = $request->all();

        $bankLedger = $this->bankLedgerRepository->create($input);

        Flash::success('Bank Ledger saved successfully.');

        return redirect(route('bankLedgers.index'));
    }

    /**
     * Display the specified BankLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            Flash::error('Bank Ledger not found');

            return redirect(route('bankLedgers.index'));
        }

        return view('bank_ledgers.show')->with('bankLedger', $bankLedger);
    }

    /**
     * Show the form for editing the specified BankLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            Flash::error('Bank Ledger not found');

            return redirect(route('bankLedgers.index'));
        }

        return view('bank_ledgers.edit')->with('bankLedger', $bankLedger);
    }

    /**
     * Update the specified BankLedger in storage.
     *
     * @param  int              $id
     * @param UpdateBankLedgerRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankLedgerRequest $request)
    {
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            Flash::error('Bank Ledger not found');

            return redirect(route('bankLedgers.index'));
        }

        $bankLedger = $this->bankLedgerRepository->update($request->all(), $id);

        Flash::success('Bank Ledger updated successfully.');

        return redirect(route('bankLedgers.index'));
    }

    /**
     * Remove the specified BankLedger from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            Flash::error('Bank Ledger not found');

            return redirect(route('bankLedgers.index'));
        }

        $this->bankLedgerRepository->delete($id);

        Flash::success('Bank Ledger deleted successfully.');

        return redirect(route('bankLedgers.index'));
    }
}
