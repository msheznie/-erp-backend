<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuotationMasterVersionRequest;
use App\Http\Requests\UpdateQuotationMasterVersionRequest;
use App\Repositories\QuotationMasterVersionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class QuotationMasterVersionController extends AppBaseController
{
    /** @var  QuotationMasterVersionRepository */
    private $quotationMasterVersionRepository;

    public function __construct(QuotationMasterVersionRepository $quotationMasterVersionRepo)
    {
        $this->quotationMasterVersionRepository = $quotationMasterVersionRepo;
    }

    /**
     * Display a listing of the QuotationMasterVersion.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->quotationMasterVersionRepository->pushCriteria(new RequestCriteria($request));
        $quotationMasterVersions = $this->quotationMasterVersionRepository->all();

        return view('quotation_master_versions.index')
            ->with('quotationMasterVersions', $quotationMasterVersions);
    }

    /**
     * Show the form for creating a new QuotationMasterVersion.
     *
     * @return Response
     */
    public function create()
    {
        return view('quotation_master_versions.create');
    }

    /**
     * Store a newly created QuotationMasterVersion in storage.
     *
     * @param CreateQuotationMasterVersionRequest $request
     *
     * @return Response
     */
    public function store(CreateQuotationMasterVersionRequest $request)
    {
        $input = $request->all();

        $quotationMasterVersion = $this->quotationMasterVersionRepository->create($input);

        Flash::success('Quotation Master Version saved successfully.');

        return redirect(route('quotationMasterVersions.index'));
    }

    /**
     * Display the specified QuotationMasterVersion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $quotationMasterVersion = $this->quotationMasterVersionRepository->findWithoutFail($id);

        if (empty($quotationMasterVersion)) {
            Flash::error('Quotation Master Version not found');

            return redirect(route('quotationMasterVersions.index'));
        }

        return view('quotation_master_versions.show')->with('quotationMasterVersion', $quotationMasterVersion);
    }

    /**
     * Show the form for editing the specified QuotationMasterVersion.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $quotationMasterVersion = $this->quotationMasterVersionRepository->findWithoutFail($id);

        if (empty($quotationMasterVersion)) {
            Flash::error('Quotation Master Version not found');

            return redirect(route('quotationMasterVersions.index'));
        }

        return view('quotation_master_versions.edit')->with('quotationMasterVersion', $quotationMasterVersion);
    }

    /**
     * Update the specified QuotationMasterVersion in storage.
     *
     * @param  int              $id
     * @param UpdateQuotationMasterVersionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuotationMasterVersionRequest $request)
    {
        $quotationMasterVersion = $this->quotationMasterVersionRepository->findWithoutFail($id);

        if (empty($quotationMasterVersion)) {
            Flash::error('Quotation Master Version not found');

            return redirect(route('quotationMasterVersions.index'));
        }

        $quotationMasterVersion = $this->quotationMasterVersionRepository->update($request->all(), $id);

        Flash::success('Quotation Master Version updated successfully.');

        return redirect(route('quotationMasterVersions.index'));
    }

    /**
     * Remove the specified QuotationMasterVersion from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $quotationMasterVersion = $this->quotationMasterVersionRepository->findWithoutFail($id);

        if (empty($quotationMasterVersion)) {
            Flash::error('Quotation Master Version not found');

            return redirect(route('quotationMasterVersions.index'));
        }

        $this->quotationMasterVersionRepository->delete($id);

        Flash::success('Quotation Master Version deleted successfully.');

        return redirect(route('quotationMasterVersions.index'));
    }
}
