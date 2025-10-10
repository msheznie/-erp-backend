<?php
/**
 * =============================================
 * -- File Name : TaxFormulaDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains all the CRUD for tax formula
 * -- REVISION HISTORY
 * --
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxFormulaDetailAPIRequest;
use App\Http\Requests\API\UpdateTaxFormulaDetailAPIRequest;
use App\Models\Tax;
use App\Models\TaxFormulaDetail;
use App\Repositories\TaxFormulaDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxFormulaDetailController
 * @package App\Http\Controllers\API
 */
class TaxFormulaDetailAPIController extends AppBaseController
{
    /** @var  TaxFormulaDetailRepository */
    private $taxFormulaDetailRepository;

    public function __construct(TaxFormulaDetailRepository $taxFormulaDetailRepo)
    {
        $this->taxFormulaDetailRepository = $taxFormulaDetailRepo;
    }

    /**
     * Display a listing of the TaxFormulaDetail.
     * GET|HEAD /taxFormulaDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxFormulaDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->taxFormulaDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxFormulaDetails = $this->taxFormulaDetailRepository->all();

        return $this->sendResponse($taxFormulaDetails->toArray(), trans('custom.tax_formula_details_retrieved_successfully'));
    }

    /**
     * Store a newly created TaxFormulaDetail in storage.
     * POST /taxFormulaDetails
     *
     * @param CreateTaxFormulaDetailAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxFormulaDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $taxFormulaDetails = $this->taxFormulaDetailRepository->create($input);

        return $this->sendResponse($taxFormulaDetails->toArray(), trans('custom.tax_formula_detail_saved_successfully'));
    }

    /**
     * Display the specified TaxFormulaDetail.
     * GET|HEAD /taxFormulaDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var TaxFormulaDetail $taxFormulaDetail */
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            return $this->sendError(trans('custom.tax_formula_detail_not_found'));
        }

        $taxMasters = TaxFormulaDetail::with(['taxmaster' => function ($query) {
            $query->select('taxMasterAutoID', 'taxDescription');
        }])->whereIn('formulaDetailID', explode(',', $taxFormulaDetail->taxMasters))->select('formulaDetailID', 'taxMasterAutoID')->get();
        $response = array('taxFormulaDetail' => $taxFormulaDetail, 'taxMasters' => $taxMasters);

        return $this->sendResponse($response, trans('custom.tax_formula_detail_retrieved_successfully'));
    }

    /**
     * Update the specified TaxFormulaDetail in storage.
     * PUT/PATCH /taxFormulaDetails/{id}
     *
     * @param  int $id
     * @param UpdateTaxFormulaDetailAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxFormulaDetailAPIRequest $request)
    {
        $input = $request->all();
        $formula = $input['formula'];
        if ($formula) {
            $input['formula'] = implode('~', $formula);
            if ($input['formula']) {
                $taxMaster = [];
                foreach ($formula as $val) {
                    $firstChar = substr($val, 0, 1);
                    if ($firstChar == '#') {
                        $taxMaster[] = ltrim($val, '#');
                    }
                }
                $input['taxMasters'] = join(',', $taxMaster);
            }
        } else {
            $input['taxMasters'] = null;
            $input['formula'] = null;
        }
        unset($input['taxmaster']);
        $input = $this->convertArrayToValue($input);
        /** @var TaxFormulaDetail $taxFormulaDetail */
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            return $this->sendError(trans('custom.tax_formula_detail_not_found'));
        }

        $taxFormulaDetail = $this->taxFormulaDetailRepository->update($input, $id);

        return $this->sendResponse($taxFormulaDetail->toArray(), trans('custom.tax_formula_detail_updated_successfully'));
    }

    /**
     * Remove the specified TaxFormulaDetail from storage.
     * DELETE /taxFormulaDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var TaxFormulaDetail $taxFormulaDetail */
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            return $this->sendError(trans('custom.tax_formula_detail_not_found'));
        }

        $taxFormulaDetail->delete();

        return $this->sendResponse($id, trans('custom.tax_formula_detail_deleted_successfully'));
    }

    public function getTaxFormulaDetailDatatable(Request $request)
    {
        $input = $request->all();
        $formulaDetail = TaxFormulaDetail::with('taxmaster')->where('taxCalculationformulaID', $request->taxCalculationformulaID);

        return \DataTables::eloquent($formulaDetail)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('formulaDetailID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getOtherTax(Request $request)
    {
        $formulaDetail = TaxFormulaDetail::with('taxmaster')->where('taxCalculationformulaID', $request->taxCalculationformulaID)->where('sortOrder', '<', $request->sortOrder)->get();
        return $this->sendResponse($formulaDetail, trans('custom.tax_formula_detail_deleted_successfully'));
    }

    public function test(){
        $result = \Formula::taxFormulaDecode(15,1000);
        return $this->sendResponse($result, trans('custom.tax_formula_detail_deleted_successfully'));
    }
}
