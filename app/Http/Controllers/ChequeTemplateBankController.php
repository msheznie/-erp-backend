<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChequeTemplateBankRequest;
use App\Http\Requests\UpdateChequeTemplateBankRequest;
use App\Repositories\ChequeTemplateBankRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ChequeTemplateBankController extends AppBaseController
{
    /** @var  ChequeTemplateBankRepository */
    private $chequeTemplateBankRepository;

    public function __construct(ChequeTemplateBankRepository $chequeTemplateBankRepo)
    {
        $this->chequeTemplateBankRepository = $chequeTemplateBankRepo;
    }

    /**
     * Display a listing of the ChequeTemplateBank.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->chequeTemplateBankRepository->pushCriteria(new RequestCriteria($request));
        $chequeTemplateBanks = $this->chequeTemplateBankRepository->all();

        return view('cheque_template_banks.index')
            ->with('chequeTemplateBanks', $chequeTemplateBanks);
    }

    /**
     * Show the form for creating a new ChequeTemplateBank.
     *
     * @return Response
     */
    public function create()
    {
        return view('cheque_template_banks.create');
    }

    /**
     * Store a newly created ChequeTemplateBank in storage.
     *
     * @param CreateChequeTemplateBankRequest $request
     *
     * @return Response
     */
    public function store(CreateChequeTemplateBankRequest $request)
    {
        $input = $request->all();

        $chequeTemplateBank = $this->chequeTemplateBankRepository->create($input);

        Flash::success('Cheque Template Bank saved successfully.');

        return redirect(route('chequeTemplateBanks.index'));
    }

    /**
     * Display the specified ChequeTemplateBank.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $chequeTemplateBank = $this->chequeTemplateBankRepository->findWithoutFail($id);

        if (empty($chequeTemplateBank)) {
            Flash::error('Cheque Template Bank not found');

            return redirect(route('chequeTemplateBanks.index'));
        }

        return view('cheque_template_banks.show')->with('chequeTemplateBank', $chequeTemplateBank);
    }

    /**
     * Show the form for editing the specified ChequeTemplateBank.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $chequeTemplateBank = $this->chequeTemplateBankRepository->findWithoutFail($id);

        if (empty($chequeTemplateBank)) {
            Flash::error('Cheque Template Bank not found');

            return redirect(route('chequeTemplateBanks.index'));
        }

        return view('cheque_template_banks.edit')->with('chequeTemplateBank', $chequeTemplateBank);
    }

    /**
     * Update the specified ChequeTemplateBank in storage.
     *
     * @param  int              $id
     * @param UpdateChequeTemplateBankRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChequeTemplateBankRequest $request)
    {
        $chequeTemplateBank = $this->chequeTemplateBankRepository->findWithoutFail($id);

        if (empty($chequeTemplateBank)) {
            Flash::error('Cheque Template Bank not found');

            return redirect(route('chequeTemplateBanks.index'));
        }

        $chequeTemplateBank = $this->chequeTemplateBankRepository->update($request->all(), $id);

        Flash::success('Cheque Template Bank updated successfully.');

        return redirect(route('chequeTemplateBanks.index'));
    }

    /**
     * Remove the specified ChequeTemplateBank from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $chequeTemplateBank = $this->chequeTemplateBankRepository->findWithoutFail($id);

        if (empty($chequeTemplateBank)) {
            Flash::error('Cheque Template Bank not found');

            return redirect(route('chequeTemplateBanks.index'));
        }

        $this->chequeTemplateBankRepository->delete($id);

        Flash::success('Cheque Template Bank deleted successfully.');

        return redirect(route('chequeTemplateBanks.index'));
    }
}
