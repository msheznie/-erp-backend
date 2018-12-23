<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankAccountRefferedBackRequest;
use App\Http\Requests\UpdateBankAccountRefferedBackRequest;
use App\Repositories\BankAccountRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankAccountRefferedBackController extends AppBaseController
{
    /** @var  BankAccountRefferedBackRepository */
    private $bankAccountRefferedBackRepository;

    public function __construct(BankAccountRefferedBackRepository $bankAccountRefferedBackRepo)
    {
        $this->bankAccountRefferedBackRepository = $bankAccountRefferedBackRepo;
    }

    /**
     * Display a listing of the BankAccountRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankAccountRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $bankAccountRefferedBacks = $this->bankAccountRefferedBackRepository->all();

        return view('bank_account_reffered_backs.index')
            ->with('bankAccountRefferedBacks', $bankAccountRefferedBacks);
    }

    /**
     * Show the form for creating a new BankAccountRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_account_reffered_backs.create');
    }

    /**
     * Store a newly created BankAccountRefferedBack in storage.
     *
     * @param CreateBankAccountRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateBankAccountRefferedBackRequest $request)
    {
        $input = $request->all();

        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->create($input);

        Flash::success('Bank Account Reffered Back saved successfully.');

        return redirect(route('bankAccountRefferedBacks.index'));
    }

    /**
     * Display the specified BankAccountRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->findWithoutFail($id);

        if (empty($bankAccountRefferedBack)) {
            Flash::error('Bank Account Reffered Back not found');

            return redirect(route('bankAccountRefferedBacks.index'));
        }

        return view('bank_account_reffered_backs.show')->with('bankAccountRefferedBack', $bankAccountRefferedBack);
    }

    /**
     * Show the form for editing the specified BankAccountRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->findWithoutFail($id);

        if (empty($bankAccountRefferedBack)) {
            Flash::error('Bank Account Reffered Back not found');

            return redirect(route('bankAccountRefferedBacks.index'));
        }

        return view('bank_account_reffered_backs.edit')->with('bankAccountRefferedBack', $bankAccountRefferedBack);
    }

    /**
     * Update the specified BankAccountRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateBankAccountRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankAccountRefferedBackRequest $request)
    {
        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->findWithoutFail($id);

        if (empty($bankAccountRefferedBack)) {
            Flash::error('Bank Account Reffered Back not found');

            return redirect(route('bankAccountRefferedBacks.index'));
        }

        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->update($request->all(), $id);

        Flash::success('Bank Account Reffered Back updated successfully.');

        return redirect(route('bankAccountRefferedBacks.index'));
    }

    /**
     * Remove the specified BankAccountRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->findWithoutFail($id);

        if (empty($bankAccountRefferedBack)) {
            Flash::error('Bank Account Reffered Back not found');

            return redirect(route('bankAccountRefferedBacks.index'));
        }

        $this->bankAccountRefferedBackRepository->delete($id);

        Flash::success('Bank Account Reffered Back deleted successfully.');

        return redirect(route('bankAccountRefferedBacks.index'));
    }
}
