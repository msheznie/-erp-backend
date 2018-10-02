<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankMemoTypesRequest;
use App\Http\Requests\UpdateBankMemoTypesRequest;
use App\Repositories\BankMemoTypesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankMemoTypesController extends AppBaseController
{
    /** @var  BankMemoTypesRepository */
    private $bankMemoTypesRepository;

    public function __construct(BankMemoTypesRepository $bankMemoTypesRepo)
    {
        $this->bankMemoTypesRepository = $bankMemoTypesRepo;
    }

    /**
     * Display a listing of the BankMemoTypes.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankMemoTypesRepository->pushCriteria(new RequestCriteria($request));
        $bankMemoTypes = $this->bankMemoTypesRepository->all();

        return view('bank_memo_types.index')
            ->with('bankMemoTypes', $bankMemoTypes);
    }

    /**
     * Show the form for creating a new BankMemoTypes.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_memo_types.create');
    }

    /**
     * Store a newly created BankMemoTypes in storage.
     *
     * @param CreateBankMemoTypesRequest $request
     *
     * @return Response
     */
    public function store(CreateBankMemoTypesRequest $request)
    {
        $input = $request->all();

        $bankMemoTypes = $this->bankMemoTypesRepository->create($input);

        Flash::success('Bank Memo Types saved successfully.');

        return redirect(route('bankMemoTypes.index'));
    }

    /**
     * Display the specified BankMemoTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankMemoTypes = $this->bankMemoTypesRepository->findWithoutFail($id);

        if (empty($bankMemoTypes)) {
            Flash::error('Bank Memo Types not found');

            return redirect(route('bankMemoTypes.index'));
        }

        return view('bank_memo_types.show')->with('bankMemoTypes', $bankMemoTypes);
    }

    /**
     * Show the form for editing the specified BankMemoTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankMemoTypes = $this->bankMemoTypesRepository->findWithoutFail($id);

        if (empty($bankMemoTypes)) {
            Flash::error('Bank Memo Types not found');

            return redirect(route('bankMemoTypes.index'));
        }

        return view('bank_memo_types.edit')->with('bankMemoTypes', $bankMemoTypes);
    }

    /**
     * Update the specified BankMemoTypes in storage.
     *
     * @param  int              $id
     * @param UpdateBankMemoTypesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankMemoTypesRequest $request)
    {
        $bankMemoTypes = $this->bankMemoTypesRepository->findWithoutFail($id);

        if (empty($bankMemoTypes)) {
            Flash::error('Bank Memo Types not found');

            return redirect(route('bankMemoTypes.index'));
        }

        $bankMemoTypes = $this->bankMemoTypesRepository->update($request->all(), $id);

        Flash::success('Bank Memo Types updated successfully.');

        return redirect(route('bankMemoTypes.index'));
    }

    /**
     * Remove the specified BankMemoTypes from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankMemoTypes = $this->bankMemoTypesRepository->findWithoutFail($id);

        if (empty($bankMemoTypes)) {
            Flash::error('Bank Memo Types not found');

            return redirect(route('bankMemoTypes.index'));
        }

        $this->bankMemoTypesRepository->delete($id);

        Flash::success('Bank Memo Types deleted successfully.');

        return redirect(route('bankMemoTypes.index'));
    }
}
