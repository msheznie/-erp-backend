<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGeneralLedgerRequest;
use App\Http\Requests\UpdateGeneralLedgerRequest;
use App\Repositories\GeneralLedgerRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GeneralLedgerController extends AppBaseController
{
    /** @var  GeneralLedgerRepository */
    private $generalLedgerRepository;

    public function __construct(GeneralLedgerRepository $generalLedgerRepo)
    {
        $this->generalLedgerRepository = $generalLedgerRepo;
    }

    /**
     * Display a listing of the GeneralLedger.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->generalLedgerRepository->pushCriteria(new RequestCriteria($request));
        $generalLedgers = $this->generalLedgerRepository->all();

        return view('general_ledgers.index')
            ->with('generalLedgers', $generalLedgers);
    }

    /**
     * Show the form for creating a new GeneralLedger.
     *
     * @return Response
     */
    public function create()
    {
        return view('general_ledgers.create');
    }

    /**
     * Store a newly created GeneralLedger in storage.
     *
     * @param CreateGeneralLedgerRequest $request
     *
     * @return Response
     */
    public function store(CreateGeneralLedgerRequest $request)
    {
        $input = $request->all();

        $generalLedger = $this->generalLedgerRepository->create($input);

        Flash::success('General Ledger saved successfully.');

        return redirect(route('generalLedgers.index'));
    }

    /**
     * Display the specified GeneralLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            Flash::error('General Ledger not found');

            return redirect(route('generalLedgers.index'));
        }

        return view('general_ledgers.show')->with('generalLedger', $generalLedger);
    }

    /**
     * Show the form for editing the specified GeneralLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            Flash::error('General Ledger not found');

            return redirect(route('generalLedgers.index'));
        }

        return view('general_ledgers.edit')->with('generalLedger', $generalLedger);
    }

    /**
     * Update the specified GeneralLedger in storage.
     *
     * @param  int              $id
     * @param UpdateGeneralLedgerRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGeneralLedgerRequest $request)
    {
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            Flash::error('General Ledger not found');

            return redirect(route('generalLedgers.index'));
        }

        $generalLedger = $this->generalLedgerRepository->update($request->all(), $id);

        Flash::success('General Ledger updated successfully.');

        return redirect(route('generalLedgers.index'));
    }

    /**
     * Remove the specified GeneralLedger from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            Flash::error('General Ledger not found');

            return redirect(route('generalLedgers.index'));
        }

        $this->generalLedgerRepository->delete($id);

        Flash::success('General Ledger deleted successfully.');

        return redirect(route('generalLedgers.index'));
    }
}
