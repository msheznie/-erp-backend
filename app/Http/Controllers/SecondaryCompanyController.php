<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSecondaryCompanyRequest;
use App\Http\Requests\UpdateSecondaryCompanyRequest;
use App\Repositories\SecondaryCompanyRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SecondaryCompanyController extends AppBaseController
{
    /** @var  SecondaryCompanyRepository */
    private $secondaryCompanyRepository;

    public function __construct(SecondaryCompanyRepository $secondaryCompanyRepo)
    {
        $this->secondaryCompanyRepository = $secondaryCompanyRepo;
    }

    /**
     * Display a listing of the SecondaryCompany.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->secondaryCompanyRepository->pushCriteria(new RequestCriteria($request));
        $secondaryCompanies = $this->secondaryCompanyRepository->all();

        return view('secondary_companies.index')
            ->with('secondaryCompanies', $secondaryCompanies);
    }

    /**
     * Show the form for creating a new SecondaryCompany.
     *
     * @return Response
     */
    public function create()
    {
        return view('secondary_companies.create');
    }

    /**
     * Store a newly created SecondaryCompany in storage.
     *
     * @param CreateSecondaryCompanyRequest $request
     *
     * @return Response
     */
    public function store(CreateSecondaryCompanyRequest $request)
    {
        $input = $request->all();

        $secondaryCompany = $this->secondaryCompanyRepository->create($input);

        Flash::success('Secondary Company saved successfully.');

        return redirect(route('secondaryCompanies.index'));
    }

    /**
     * Display the specified SecondaryCompany.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $secondaryCompany = $this->secondaryCompanyRepository->findWithoutFail($id);

        if (empty($secondaryCompany)) {
            Flash::error('Secondary Company not found');

            return redirect(route('secondaryCompanies.index'));
        }

        return view('secondary_companies.show')->with('secondaryCompany', $secondaryCompany);
    }

    /**
     * Show the form for editing the specified SecondaryCompany.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $secondaryCompany = $this->secondaryCompanyRepository->findWithoutFail($id);

        if (empty($secondaryCompany)) {
            Flash::error('Secondary Company not found');

            return redirect(route('secondaryCompanies.index'));
        }

        return view('secondary_companies.edit')->with('secondaryCompany', $secondaryCompany);
    }

    /**
     * Update the specified SecondaryCompany in storage.
     *
     * @param  int              $id
     * @param UpdateSecondaryCompanyRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSecondaryCompanyRequest $request)
    {
        $secondaryCompany = $this->secondaryCompanyRepository->findWithoutFail($id);

        if (empty($secondaryCompany)) {
            Flash::error('Secondary Company not found');

            return redirect(route('secondaryCompanies.index'));
        }

        $secondaryCompany = $this->secondaryCompanyRepository->update($request->all(), $id);

        Flash::success('Secondary Company updated successfully.');

        return redirect(route('secondaryCompanies.index'));
    }

    /**
     * Remove the specified SecondaryCompany from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $secondaryCompany = $this->secondaryCompanyRepository->findWithoutFail($id);

        if (empty($secondaryCompany)) {
            Flash::error('Secondary Company not found');

            return redirect(route('secondaryCompanies.index'));
        }

        $this->secondaryCompanyRepository->delete($id);

        Flash::success('Secondary Company deleted successfully.');

        return redirect(route('secondaryCompanies.index'));
    }
}
