<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateERPLanguageMasterAPIRequest;
use App\Http\Requests\API\UpdateERPLanguageMasterAPIRequest;
use App\Models\ERPLanguageMaster;
use App\Models\EmployeeLanguage;
use App\Models\ThirdPartyIntegrationKeys;
use App\Models\Employee;
use App\Repositories\ERPLanguageMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Jobs\UserWebHook;

/**
 * Class ERPLanguageMasterController
 * @package App\Http\Controllers\API
 */

class ERPLanguageMasterAPIController extends AppBaseController
{
    /** @var  ERPLanguageMasterRepository */
    private $eRPLanguageMasterRepository;

    public function __construct(ERPLanguageMasterRepository $eRPLanguageMasterRepo)
    {
        $this->eRPLanguageMasterRepository = $eRPLanguageMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/eRPLanguageMasters",
     *      summary="getERPLanguageMasterList",
     *      tags={"ERPLanguageMaster"},
     *      description="Get all ERPLanguageMasters",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/ERPLanguageMaster")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->eRPLanguageMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->eRPLanguageMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eRPLanguageMasters = $this->eRPLanguageMasterRepository->select(['languageShortCode','isActive','icon','languageID'])->where('isActive',1)->get();
        return $this->sendResponse($eRPLanguageMasters->toArray(), trans('custom.languages_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/eRPLanguageMasters",
     *      summary="createERPLanguageMaster",
     *      tags={"ERPLanguageMaster"},
     *      description="Create ERPLanguageMaster",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/ERPLanguageMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $eRPLanguageMaster = $this->eRPLanguageMasterRepository->create($input);

        return $this->sendResponse($eRPLanguageMaster->toArray(), trans('custom.language_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/eRPLanguageMasters/{id}",
     *      summary="getERPLanguageMasterItem",
     *      tags={"ERPLanguageMaster"},
     *      description="Get ERPLanguageMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ERPLanguageMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/ERPLanguageMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var ERPLanguageMaster $eRPLanguageMaster */
        $eRPLanguageMaster = $this->eRPLanguageMasterRepository->findWithoutFail($id);

        if (empty($eRPLanguageMaster)) {
            return $this->sendError(trans('custom.e_r_p_language_master_not_found'));
        }

        return $this->sendResponse($eRPLanguageMaster->toArray(), trans('custom.e_r_p_language_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/eRPLanguageMasters/{id}",
     *      summary="updateERPLanguageMaster",
     *      tags={"ERPLanguageMaster"},
     *      description="Update ERPLanguageMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ERPLanguageMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/ERPLanguageMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateERPLanguageMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ERPLanguageMaster $eRPLanguageMaster */
        $eRPLanguageMaster = $this->eRPLanguageMasterRepository->findWithoutFail($id);

        if (empty($eRPLanguageMaster)) {
            return $this->sendError(trans('custom.e_r_p_language_master_not_found'));
        }

        $eRPLanguageMaster = $this->eRPLanguageMasterRepository->update($input, $id);

        return $this->sendResponse($eRPLanguageMaster->toArray(), trans('custom.erplanguagemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/eRPLanguageMasters/{id}",
     *      summary="deleteERPLanguageMaster",
     *      tags={"ERPLanguageMaster"},
     *      description="Delete ERPLanguageMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ERPLanguageMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var ERPLanguageMaster $eRPLanguageMaster */
        $eRPLanguageMaster = $this->eRPLanguageMasterRepository->findWithoutFail($id);

        if (empty($eRPLanguageMaster)) {
            return $this->sendError(trans('custom.e_r_p_language_master_not_found'));
        }

        $eRPLanguageMaster->delete();

        return $this->sendSuccess('E R P Language Master deleted successfully');
    }

    public function storeEmployeeLanguage(Request $request) {
        $input = $request->input();
        $employee = Employee::find($input['employeeID']);

        if($this->checkRecordExists($input)) {
            $data = $this->updateRecord($input);
            if(!$data) {
                return $this->sendError(trans('custom.cannot_update_data'));
            }
            return $this->sendResponse($data->toArray(), trans('custom.language_updated_successfully'));
        }else {
            $createRecord = EmployeeLanguage::create($input);
            return $this->sendResponse($createRecord->toArray(), trans('custom.language_saved_successfully'));

        }



    }

    public function updateRecord($input) {
        $record = EmployeeLanguage::where('employeeID',$input['employeeID'])->first();
        $record->languageID = $input['languageID'];
        $record->save();

        $db = isset($input['db']) ? $input['db'] : "";
        $thirdParty = ThirdPartyIntegrationKeys::where('third_party_system_id', 5)->where('status', 'Active')->first();

        if(!empty($thirdParty)){
            UserWebHook::dispatch($db, $input['employeeID'], $thirdParty->api_external_key, $thirdParty->api_external_url);
        } 

        return ($record) ? $record : false;
    }

    public function checkRecordExists($input) {
        $record = EmployeeLanguage::where('employeeID',$input['employeeID'])->first();
        return ($record) ? true : false;
    }
}
