<?php
/**
 * =============================================
 * -- File Name : ServiceLineAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Segments
 * -- Author : Mohamed Zakeeul
 * -- Create date : 20 - February 2020
 * -- Description : This file contains the all CRUD for Service Lines
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateServiceLineAPIRequest;
use App\Http\Requests\API\UpdateServiceLineAPIRequest;
use App\Models\ServiceLine;
use App\Repositories\ServiceLineRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Company;


/**
 * Class ServiceLineController
 * @package App\Http\Controllers\API
 */

class ServiceLineAPIController extends AppBaseController
{
    /** @var  ServiceLineRepository */
    private $serviceLineRepository;

    public function __construct(ServiceLineRepository $serviceLineRepo)
    {
        $this->serviceLineRepository = $serviceLineRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/serviceLines",
     *      summary="Get a listing of the ServiceLines.",
     *      tags={"ServiceLine"},
     *      description="Get all ServiceLines",
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
     *                  @SWG\Items(ref="#/definitions/ServiceLine")
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
        $this->serviceLineRepository->pushCriteria(new RequestCriteria($request));
        $this->serviceLineRepository->pushCriteria(new LimitOffsetCriteria($request));
        $serviceLines = $this->serviceLineRepository->all();

        return $this->sendResponse($serviceLines->toArray(), 'Segment retrieved successfully');
    }

    /**
     * @param CreateServiceLineAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/serviceLines",
     *      summary="Store a newly created ServiceLine in storage",
     *      tags={"ServiceLine"},
     *      description="Store ServiceLine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceLine that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceLine")
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
     *                  ref="#/definitions/ServiceLine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateServiceLineAPIRequest $request)
    {
        $input = $request->all();

        $serviceLine = $this->serviceLineRepository->create($input);

        return $this->sendResponse($serviceLine->toArray(), 'Segment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/serviceLines/{id}",
     *      summary="Display the specified ServiceLine",
     *      tags={"ServiceLine"},
     *      description="Get ServiceLine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceLine",
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
     *                  ref="#/definitions/ServiceLine"
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
        /** @var ServiceLine $serviceLine */
        $serviceLine = $this->serviceLineRepository->findWithoutFail($id);

        if (empty($serviceLine)) {
            return $this->sendError('Segment not found');
        }

        return $this->sendResponse($serviceLine->toArray(), 'Segment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateServiceLineAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/serviceLines/{id}",
     *      summary="Update the specified ServiceLine in storage",
     *      tags={"ServiceLine"},
     *      description="Update ServiceLine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceLine",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceLine that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceLine")
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
     *                  ref="#/definitions/ServiceLine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateServiceLineAPIRequest $request)
    {
        $input = $request->all();

        /** @var ServiceLine $serviceLine */
        $serviceLine = $this->serviceLineRepository->findWithoutFail($id);

        if (empty($serviceLine)) {
            return $this->sendError('Segment not found');
        }

        $serviceLine = $this->serviceLineRepository->update($input, $id);

        return $this->sendResponse($serviceLine->toArray(), 'ServiceLine updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/serviceLines/{id}",
     *      summary="Remove the specified ServiceLine from storage",
     *      tags={"ServiceLine"},
     *      description="Delete ServiceLine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceLine",
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
        /** @var ServiceLine $serviceLine */
        $serviceLine = $this->serviceLineRepository->findWithoutFail($id);

        if (empty($serviceLine)) {
            return $this->sendError('Segment not found');
        }

        $serviceLine->delete();

        return $this->sendResponse($id, 'Segment deleted successfully');
    }

    public function getServiceLineByCompany(Request $request)
    {
        $companyID = $request->companyID;
        $serviceline = ServiceLine::where('companySystemID', $companyID)->where('isActive',1)->where('isFinalLevel',1)->where('isDeleted',0)->get();
        return $this->sendResponse($serviceline, 'Segment retrieved successfully');
    }

    public function getServiceLineByparent(Request $request) {
        $companyID = $request->companyID;
        $parentOnly = $request->parentOnly;

        $query = ServiceLine::where('companySystemID', $companyID)
            ->where('isDeleted',0)
            ->where('approved_yn',1);

        if(isset($parentOnly) && !is_null($parentOnly)) {
            $query->where('isFinalLevel', 0);
        }

        $serviceline = $query->get();

        $company = Company::find($companyID);
        $companyAsService = [
            'serviceLineSystemID' => 0,
            'ServiceLineCode' => $company->CompanyID ?? '',
            'ServiceLineDes' => $company->CompanyName ?? '',
        ];
        $serviceline->prepend((object) $companyAsService);

        return $this->sendResponse($serviceline, 'Segment retrieved successfully');
    }
}
