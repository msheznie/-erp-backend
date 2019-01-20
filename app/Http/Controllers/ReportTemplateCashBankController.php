<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateCashBankRequest;
use App\Http\Requests\UpdateReportTemplateCashBankRequest;
use App\Repositories\ReportTemplateCashBankRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateCashBankController extends AppBaseController
{
    /** @var  ReportTemplateCashBankRepository */
    private $reportTemplateCashBankRepository;

    public function __construct(ReportTemplateCashBankRepository $reportTemplateCashBankRepo)
    {
        $this->reportTemplateCashBankRepository = $reportTemplateCashBankRepo;
    }

    /**
     * Display a listing of the ReportTemplateCashBank.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateCashBankRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateCashBanks = $this->reportTemplateCashBankRepository->all();

        return view('report_template_cash_banks.index')
            ->with('reportTemplateCashBanks', $reportTemplateCashBanks);
    }

    /**
     * Show the form for creating a new ReportTemplateCashBank.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_cash_banks.create');
    }

    /**
     * Store a newly created ReportTemplateCashBank in storage.
     *
     * @param CreateReportTemplateCashBankRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateCashBankRequest $request)
    {
        $input = $request->all();

        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->create($input);

        Flash::success('Report Template Cash Bank saved successfully.');

        return redirect(route('reportTemplateCashBanks.index'));
    }

    /**
     * Display the specified ReportTemplateCashBank.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->findWithoutFail($id);

        if (empty($reportTemplateCashBank)) {
            Flash::error('Report Template Cash Bank not found');

            return redirect(route('reportTemplateCashBanks.index'));
        }

        return view('report_template_cash_banks.show')->with('reportTemplateCashBank', $reportTemplateCashBank);
    }

    /**
     * Show the form for editing the specified ReportTemplateCashBank.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->findWithoutFail($id);

        if (empty($reportTemplateCashBank)) {
            Flash::error('Report Template Cash Bank not found');

            return redirect(route('reportTemplateCashBanks.index'));
        }

        return view('report_template_cash_banks.edit')->with('reportTemplateCashBank', $reportTemplateCashBank);
    }

    /**
     * Update the specified ReportTemplateCashBank in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateCashBankRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateCashBankRequest $request)
    {
        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->findWithoutFail($id);

        if (empty($reportTemplateCashBank)) {
            Flash::error('Report Template Cash Bank not found');

            return redirect(route('reportTemplateCashBanks.index'));
        }

        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->update($request->all(), $id);

        Flash::success('Report Template Cash Bank updated successfully.');

        return redirect(route('reportTemplateCashBanks.index'));
    }

    /**
     * Remove the specified ReportTemplateCashBank from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->findWithoutFail($id);

        if (empty($reportTemplateCashBank)) {
            Flash::error('Report Template Cash Bank not found');

            return redirect(route('reportTemplateCashBanks.index'));
        }

        $this->reportTemplateCashBankRepository->delete($id);

        Flash::success('Report Template Cash Bank deleted successfully.');

        return redirect(route('reportTemplateCashBanks.index'));
    }
}
