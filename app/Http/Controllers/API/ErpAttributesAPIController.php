<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpAttributesAPIRequest;
use App\Http\Requests\API\UpdateErpAttributesAPIRequest;
use App\Models\ErpAttributes;
use App\Models\ErpAttributeValues;
use App\Repositories\ErpAttributesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ErpAttributesDropdown;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Traits\AuditLogsTrait;

/**
 * Class ErpAttributesController
 * @package App\Http\Controllers\API
 */

class ErpAttributesAPIController extends AppBaseController
{
    /** @var  ErpAttributesRepository */
    private $erpAttributesRepository;
    use AuditLogsTrait;
    
    public function __construct(ErpAttributesRepository $erpAttributesRepo)
    {
        $this->erpAttributesRepository = $erpAttributesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpAttributes",
     *      summary="Get a listing of the ErpAttributes.",
     *      tags={"ErpAttributes"},
     *      description="Get all ErpAttributes",
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
     *                  @SWG\Items(ref="#/definitions/ErpAttributes")
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
        $this->erpAttributesRepository->pushCriteria(new RequestCriteria($request));
        $this->erpAttributesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpAttributes = $this->erpAttributesRepository->all();

        return $this->sendResponse($erpAttributes->toArray(), 'Erp Attributes retrieved successfully');
    }

    /**
     * @param CreateErpAttributesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpAttributes",
     *      summary="Store a newly created ErpAttributes in storage",
     *      tags={"ErpAttributes"},
     *      description="Store ErpAttributes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpAttributes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpAttributes")
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
     *                  ref="#/definitions/ErpAttributes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpAttributesAPIRequest $request)
    {
        $input = $request->all();

        $erpAttributes = $this->erpAttributesRepository->create($input);

        return $this->sendResponse($erpAttributes->toArray(), 'Erp Attributes saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpAttributes/{id}",
     *      summary="Display the specified ErpAttributes",
     *      tags={"ErpAttributes"},
     *      description="Get ErpAttributes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributes",
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
     *                  ref="#/definitions/ErpAttributes"
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
        /** @var ErpAttributes $erpAttributes */
        $erpAttributes = $this->erpAttributesRepository->findWithoutFail($id);

        if (empty($erpAttributes)) {
            return $this->sendError('Erp Attributes not found');
        }

        return $this->sendResponse($erpAttributes->toArray(), 'Erp Attributes retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateErpAttributesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpAttributes/{id}",
     *      summary="Update the specified ErpAttributes in storage",
     *      tags={"ErpAttributes"},
     *      description="Update ErpAttributes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpAttributes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpAttributes")
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
     *                  ref="#/definitions/ErpAttributes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpAttributesAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpAttributes $erpAttributes */
        $erpAttributes = $this->erpAttributesRepository->findWithoutFail($id);

        if (empty($erpAttributes)) {
            return $this->sendError('Erp Attributes not found');
        }

        $erpAttributes = $this->erpAttributesRepository->update($input, $id);

        return $this->sendResponse($erpAttributes->toArray(), 'ErpAttributes updated successfully');
        
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpAttributes/{id}",
     *      summary="Remove the specified ErpAttributes from storage",
     *      tags={"ErpAttributes"},
     *      description="Delete ErpAttributes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributes",
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
        /** @var ErpAttributes $erpAttributes */
        $erpAttributes = $this->erpAttributesRepository->findWithoutFail($id);

        if (empty($erpAttributes)) {
            return $this->sendError('Erp Attributes not found');
        }

        $attributeActiveValidation = ErpAttributeValues::selectRaw('erp_fa_asset_master.faID')
            ->join('erp_fa_asset_master', 'erp_attribute_values.document_master_id', '=', 'erp_fa_asset_master.faID')
            ->where('erp_attribute_values.attribute_id', $id)
            ->where('erp_fa_asset_master.confirmedYN', 1)
            ->where('erp_fa_asset_master.approved', 0)
            ->count();

        if($attributeActiveValidation > 0){
            return $this->sendError('There are some pending assets awaiting approval', 500);
        }

        if ($erpAttributes->document_id == "SUBCAT") {

            $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
            $db = isset($input['db']) ? $input['db'] : '';

            $this->auditLog($db, $id,$uuid, "erp_attributes", "Attribute ".$erpAttributes->description." has deleted", "D", [], $erpAttributes->toArray(), $erpAttributes->document_master_id, 'financeitemcategorysub');
        }

        $erpAttributes->delete();

        ErpAttributeValues::where('erp_attribute_values.attribute_id', $id)->update(['is_active' => 0]);

        return $this->sendResponse([],'Erp Attributes deleted successfully');
    }

    public function itemAttributesIsMandotaryUpdate(Request $request){
        $input = $request->all();
        $id = $input['id'];

        $is_mendatory = ($input['is_mendatory']) ? 1 : 0;

        $erpAttributes = $this->erpAttributesRepository->findWithoutFail($id);
        $previousValue = $erpAttributes->toArray();

        $attributesIsMandotaryUpdate = ErpAttributes::where('id', $id)
        ->update(['is_mendatory' => $is_mendatory]);

        $newValue = ['is_mendatory' => $is_mendatory];


        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';

        if ($erpAttributes->document_id == "SUBCAT") {
            $this->auditLog($db, $id, $uuid, "erp_attributes", "Attribute " . $erpAttributes->description . " has been updated", "U", $newValue, $previousValue, $erpAttributes->document_master_id, 'financeitemcategorysub');
        }

        return $this->sendResponse($attributesIsMandotaryUpdate, 'Erp Attributes updated successfully');

    }

    public function assetCostAttributesUpdate(Request $request){
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('field_type_id'));

        $id = $input['id'];

        $erpAttributes = $this->erpAttributesRepository->findWithoutFail($id);

        if($erpAttributes->is_active != $input['is_active'] && $input['is_active'] == 0){
            $attributeActiveValidation = ErpAttributeValues::selectRaw('erp_fa_asset_master.faID')
                ->join('erp_fa_asset_master', 'erp_attribute_values.document_master_id', '=', 'erp_fa_asset_master.faID')
                ->where('erp_attribute_values.attribute_id', $id)
                ->where('erp_fa_asset_master.confirmedYN', 1)
                ->where('erp_fa_asset_master.approved', 0)
                ->count();

            if($attributeActiveValidation > 0){
                return $this->sendError('There are some pending assets awaiting approval', 500);
            }
        }

        if($erpAttributes->field_type_id != $input['field_type_id']){
            if($erpAttributes->field_type_id == 3){
                $dropdownValues = ErpAttributesDropdown::where('attributes_id',$erpAttributes->id)->count();
                 if($dropdownValues > 0){
                     return $this->sendError('Unable to update. Dropdown value is added for the attribute', 500);
                 }
             }
        }

        if($erpAttributes->description != $input['description']){
            if(empty($input['description'])){
                return $this->sendError('Unable to update. Description is empty', 500);
            }
            $attributesValidateDescription = ErpAttributes::where('description', $input['description'])->count();
            if($attributesValidateDescription > 0){
                return $this->sendError('Unable to update. Description already exist', 500);
            }
             
        }

        $updateData = [
            'description' => $input['description'],
            'field_type_id' => $input['field_type_id'],
            'is_mendatory' => $input['is_mendatory'],
            'is_active' => $input['is_active']
        ];
        $attributesUpdate = ErpAttributes::where('id', $id)
        ->update($updateData);

        return $this->sendResponse($attributesUpdate, 'Erp Attributes updated successfully');

    }

    public function dropdownValuesUpdate(Request $request){
        $input = $request->all();
        $id = $input['id'];

        $dropdownValues = ErpAttributesDropdown::where('id',$id)->first();

        ErpAttributeValues::where('attribute_id',$dropdownValues->attributes_id)->where('value', $id)->update(['color' => $input['color']]);


        if($dropdownValues->description != $input['description']){
            if(empty($input['description'])){
                return $this->sendError('Unable to update. Description is empty', 500);
            }
            $dropdownValidateDescription = ErpAttributesDropdown::where('description', $input['description'])->count();
            if($dropdownValidateDescription > 0){
                return $this->sendError('Unable to update. Description already exist', 500);
            }
             
        }

        $updateData = [
            'description' => $input['description'],
            'color' => $input['color']
        ];
        $dropdownValuesUpdate = ErpAttributesDropdown::where('id', $id)
        ->update($updateData);

        return $this->sendResponse($dropdownValuesUpdate, 'Erp Attributes updated successfully');

    }

    public function itemAttributesDelete(Request $request){
        $input = $request->all();
        $id = $input['id'];
        $attributesIsMandotaryUpdate = ErpAttributes::where('id', $id)->delete();

        return $this->sendResponse($attributesIsMandotaryUpdate, 'Erp Attributes deleted successfully');

    }
}
