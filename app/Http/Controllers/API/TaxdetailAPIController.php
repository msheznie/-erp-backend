<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxdetailAPIRequest;
use App\Http\Requests\API\UpdateTaxdetailAPIRequest;
use App\Models\Taxdetail;
use App\Models\CustomerInvoiceDirect;
use App\Repositories\TaxdetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxdetailController
 * @package App\Http\Controllers\API
 */
class TaxdetailAPIController extends AppBaseController
{
    /** @var  TaxdetailRepository */
    private $taxdetailRepository;

    public function __construct(TaxdetailRepository $taxdetailRepo)
    {
        $this->taxdetailRepository = $taxdetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxdetails",
     *      summary="Get a listing of the Taxdetails.",
     *      tags={"Taxdetail"},
     *      description="Get all Taxdetails",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Taxdetail")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->taxdetailRepository->pushCriteria(new RequestCriteria($request));
        $this->taxdetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxdetails = $this->taxdetailRepository->all();

        return $this->sendResponse($taxdetails->toArray(), 'VAT details retrieved successfully');
    }

    /**
     * @param CreateTaxdetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxdetails",
     *      summary="Store a newly created Taxdetail in storage",
     *      tags={"Taxdetail"},
     *      description="Store Taxdetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Taxdetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Taxdetail")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Taxdetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxdetailAPIRequest $request)
    {
        $input = $request->all();

        $taxdetails = $this->taxdetailRepository->create($input);

        return $this->sendResponse($taxdetails->toArray(), 'VAT detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxdetails/{id}",
     *      summary="Display the specified Taxdetail",
     *      tags={"Taxdetail"},
     *      description="Get Taxdetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Taxdetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Taxdetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Taxdetail $taxdetail */
        $taxdetail = $this->taxdetailRepository->findWithoutFail($id);

        if (empty($taxdetail)) {
            return $this->sendError('VATdetail not found');
        }

        return $this->sendResponse($taxdetail->toArray(), 'VAT detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTaxdetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxdetails/{id}",
     *      summary="Update the specified Taxdetail in storage",
     *      tags={"Taxdetail"},
     *      description="Update Taxdetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Taxdetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Taxdetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Taxdetail")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Taxdetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxdetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var Taxdetail $taxdetail */
        $taxdetail = $this->taxdetailRepository->findWithoutFail($id);

        if (empty($taxdetail)) {
            return $this->sendError('VATdetail not found');
        }

        $taxdetail = $this->taxdetailRepository->update($input, $id);

        return $this->sendResponse($taxdetail->toArray(), 'VAT detail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxdetails/{id}",
     *      summary="Remove the specified Taxdetail from storage",
     *      tags={"Taxdetail"},
     *      description="Delete Taxdetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Taxdetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {

        /** @var Taxdetail $taxdetail */
        $taxdetail = $this->taxdetailRepository->findWithoutFail($id);

        if (empty($taxdetail)) {
            return $this->sendError('e', 'VAT detail not found');
        }

        $master['vatOutputGLCodeSystemID'] = NULL;
        $master['vatOutputGLCode'] = NULL;
        $master['VATPercentage'] = NULL;
        $master['VATAmount'] = 0;
        $master['VATAmountLocal'] = 0;
        $master['VATAmountRpt'] = 0;

        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $taxdetail->documentSystemCode)->update($master);

        $taxdetail->delete();

        return $this->sendResponse('s', 'VAT detail deleted successfully');
    }

    public function customerInvoiceTaxDetail(Request $request)
    {
        $id = $request['id'];
        $documentSystemID = $request['documentSystemID'];
        $tax = Taxdetail::select('*')
            ->where('documentSystemCode', $id)
            ->where('documentSystemID', $documentSystemID)
            ->get();
        return $this->sendResponse($tax, 'VAT detail retrieved successfully');
    }
}
