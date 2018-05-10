<?php
/**
 * =============================================
 * -- File Name : CompanyPolicyMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Approval
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Company Policy.
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyPolicyMasterAPIRequest;
use App\Http\Requests\API\UpdateCompanyPolicyMasterAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Repositories\CompanyPolicyMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyPolicyMasterController
 * @package App\Http\Controllers\API
 */

class CompanyPolicyMasterAPIController extends AppBaseController
{
    /** @var  CompanyPolicyMasterRepository */
    private $companyPolicyMasterRepository;

    public function __construct(CompanyPolicyMasterRepository $companyPolicyMasterRepo)
    {
        $this->companyPolicyMasterRepository = $companyPolicyMasterRepo;
    }

    /**
     * Display a listing of the CompanyPolicyMaster.
     * GET|HEAD /companyPolicyMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyPolicyMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->companyPolicyMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyPolicyMasters = $this->companyPolicyMasterRepository->all();

        return $this->sendResponse($companyPolicyMasters->toArray(), 'Company Policy Masters retrieved successfully');
    }

    /**
     * Store a newly created CompanyPolicyMaster in storage.
     * POST /companyPolicyMasters
     *
     * @param CreateCompanyPolicyMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyPolicyMasterAPIRequest $request)
    {
        $input = $request->all();

        $companyPolicyMasters = $this->companyPolicyMasterRepository->create($input);

        return $this->sendResponse($companyPolicyMasters->toArray(), 'Company Policy Master saved successfully');
    }

    /**
     * Display the specified CompanyPolicyMaster.
     * GET|HEAD /companyPolicyMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CompanyPolicyMaster $companyPolicyMaster */
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            return $this->sendError('Company Policy Master not found');
        }

        return $this->sendResponse($companyPolicyMaster->toArray(), 'Company Policy Master retrieved successfully');
    }

    /**
     * Update the specified CompanyPolicyMaster in storage.
     * PUT/PATCH /companyPolicyMasters/{id}
     *
     * @param  int $id
     * @param UpdateCompanyPolicyMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyPolicyMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyPolicyMaster $companyPolicyMaster */
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            return $this->sendError('Company Policy Master not found');
        }

        $companyPolicyMaster = $this->companyPolicyMasterRepository->update($input, $id);

        return $this->sendResponse($companyPolicyMaster->toArray(), 'CompanyPolicyMaster updated successfully');
    }

    /**
     * Remove the specified CompanyPolicyMaster from storage.
     * DELETE /companyPolicyMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CompanyPolicyMaster $companyPolicyMaster */
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            return $this->sendError('Company Policy Master not found');
        }

        $companyPolicyMaster->delete();

        return $this->sendResponse($id, 'Company Policy Master deleted successfully');
    }
}
