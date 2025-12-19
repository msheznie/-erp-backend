<?php
/**
 * =============================================
 * -- File Name : LogisticModeOfImportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic Mode of Import
 * -- REVISION HISTORY
 * -- Date: 12-September 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticModeOfImportAPIRequest;
use App\Http\Requests\API\UpdateLogisticModeOfImportAPIRequest;
use App\Models\LogisticModeOfImport;
use App\Repositories\LogisticModeOfImportRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticModeOfImportController
 * @package App\Http\Controllers\API
 */

class LogisticModeOfImportAPIController extends AppBaseController
{
    /** @var  LogisticModeOfImportRepository */
    private $logisticModeOfImportRepository;

    public function __construct(LogisticModeOfImportRepository $logisticModeOfImportRepo)
    {
        $this->logisticModeOfImportRepository = $logisticModeOfImportRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticModeOfImports",
     *      summary="Get a listing of the LogisticModeOfImports.",
     *      tags={"LogisticModeOfImport"},
     *      description="Get all LogisticModeOfImports",
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
     *                  @SWG\Items(ref="#/definitions/LogisticModeOfImport")
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
        $this->logisticModeOfImportRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticModeOfImportRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticModeOfImports = $this->logisticModeOfImportRepository->all();

        return $this->sendResponse($logisticModeOfImports->toArray(), trans('custom.logistic_mode_of_imports_retrieved_successfully'));
    }

    /**
     * @param CreateLogisticModeOfImportAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logisticModeOfImports",
     *      summary="Store a newly created LogisticModeOfImport in storage",
     *      tags={"LogisticModeOfImport"},
     *      description="Store LogisticModeOfImport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticModeOfImport that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticModeOfImport")
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
     *                  ref="#/definitions/LogisticModeOfImport"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticModeOfImportAPIRequest $request)
    {
        $input = $request->all();

        $logisticModeOfImports = $this->logisticModeOfImportRepository->create($input);

        return $this->sendResponse($logisticModeOfImports->toArray(), trans('custom.logistic_mode_of_import_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticModeOfImports/{id}",
     *      summary="Display the specified LogisticModeOfImport",
     *      tags={"LogisticModeOfImport"},
     *      description="Get LogisticModeOfImport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticModeOfImport",
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
     *                  ref="#/definitions/LogisticModeOfImport"
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
        /** @var LogisticModeOfImport $logisticModeOfImport */
        $logisticModeOfImport = $this->logisticModeOfImportRepository->findWithoutFail($id);

        if (empty($logisticModeOfImport)) {
            return $this->sendError(trans('custom.logistic_mode_of_import_not_found'));
        }

        return $this->sendResponse($logisticModeOfImport->toArray(), trans('custom.logistic_mode_of_import_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLogisticModeOfImportAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logisticModeOfImports/{id}",
     *      summary="Update the specified LogisticModeOfImport in storage",
     *      tags={"LogisticModeOfImport"},
     *      description="Update LogisticModeOfImport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticModeOfImport",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticModeOfImport that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticModeOfImport")
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
     *                  ref="#/definitions/LogisticModeOfImport"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticModeOfImportAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticModeOfImport $logisticModeOfImport */
        $logisticModeOfImport = $this->logisticModeOfImportRepository->findWithoutFail($id);

        if (empty($logisticModeOfImport)) {
            return $this->sendError(trans('custom.logistic_mode_of_import_not_found'));
        }

        $logisticModeOfImport = $this->logisticModeOfImportRepository->update($input, $id);

        return $this->sendResponse($logisticModeOfImport->toArray(), trans('custom.logisticmodeofimport_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logisticModeOfImports/{id}",
     *      summary="Remove the specified LogisticModeOfImport from storage",
     *      tags={"LogisticModeOfImport"},
     *      description="Delete LogisticModeOfImport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticModeOfImport",
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
        /** @var LogisticModeOfImport $logisticModeOfImport */
        $logisticModeOfImport = $this->logisticModeOfImportRepository->findWithoutFail($id);

        if (empty($logisticModeOfImport)) {
            return $this->sendError(trans('custom.logistic_mode_of_import_not_found'));
        }

        $logisticModeOfImport->delete();

        return $this->sendResponse($id, trans('custom.logistic_mode_of_import_deleted_successfully'));
    }
}
