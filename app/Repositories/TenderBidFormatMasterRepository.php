<?php

namespace App\Repositories;

use App\Models\TenderBidFormatDetail;
use App\Models\TenderBidFormatMaster;
use App\Models\TenderFieldType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderBidFormatMasterRepository
 * @package App\Repositories
 * @version March 4, 2022, 10:32 am +04
 *
 * @method TenderBidFormatMaster findWithoutFail($id, $columns = ['*'])
 * @method TenderBidFormatMaster find($id, $columns = ['*'])
 * @method TenderBidFormatMaster first($columns = ['*'])
*/
class TenderBidFormatMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_name',
        'boq_applicable',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBidFormatMaster::class;
    }

    public function uploadPriceBidFormatDetails($input, $excelUpload){
        DB::beginTransaction();
        try {

            $validation = $this->checkValidUploadRequestParams($input);
            if(!$validation['success']) {
                return ['success' => false, 'message' => $validation['message']];
            }

            $fileValidation = $this->validateExcelUpload($excelUpload);
            if (!$fileValidation['success']) {
                return $fileValidation;
            }

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];

            $fileNameWithoutExt = pathinfo($originalFileName, PATHINFO_FILENAME);
            if (strtolower($fileNameWithoutExt) !== 'price_bid_format_template') {
                return [
                    'success' => false,
                    'message' => trans('srm_tender_rfx.invalid_file_name', ['expected' => 'Price_Bid_Format_Template'])
                ];
            }

            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);
            $sheetData = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->path($originalFileName))->get()->toArray();

            if (empty($sheetData)) {
                if (Storage::disk($disk)->exists($originalFileName)) {
                    Storage::disk($disk)->delete($originalFileName);
                }
                return [
                    'success' => false,
                    'message' => trans('srm_tender_rfx.excel_file_empty_or_invalid')
                ];
            }

            $firstRow = reset($sheetData);
            $requiredColumns = ['price_bid_item', 'field_type', 'is_enabled', 'is_boq_applicable'];
            $missingColumns = [];

            foreach ($requiredColumns as $column) {
                if (!array_key_exists($column, $firstRow)) {
                    $missingColumns[] = $column;
                }
            }

            if (!empty($missingColumns)) {
                if (Storage::disk($disk)->exists($originalFileName)) {
                    Storage::disk($disk)->delete($originalFileName);
                }
                return [
                    'success' => false,
                    'message' => trans(
                        'srm_tender_rfx.excel_missing_columns',
                        ['columns' => implode(', ', $missingColumns)]
                    )
                ];
            }

            $uniqueData = array_filter(collect($sheetData)->toArray());
            foreach ($uniqueData as $key => $value) {

                $priceBidItem = $value['price_bid_item'] ?? null;
                $fieldType = $value['field_type'] ?? null;

                if(empty($priceBidItem) || empty($fieldType))
                {
                    if (Storage::disk($disk)->exists($originalFileName)) {
                        Storage::disk($disk)->delete($originalFileName);
                    }
                    return ['success' => false, 'message' => trans('srm_tender_rfx.items_null_values')];
                }
                
                $isEnabled = isset($value['is_enabled']) ? strtolower(trim($value['is_enabled'])) : '';
                $isBoqApplicable = isset($value['is_boq_applicable']) ? strtolower(trim($value['is_boq_applicable'])) : '';

                if ($isEnabled === 'yes' && $isBoqApplicable === 'yes') {
                    if (Storage::disk($disk)->exists($originalFileName)) {
                        Storage::disk($disk)->delete($originalFileName);
                    }
                    $errorMessage = $priceBidItem . ' - ' . trans('srm_tender_rfx.both_fields_cannot_be_yes');
                    return [
                        'success' => false,
                        'message' => $errorMessage
                    ];
                }
            }

            $record = array_map(function ($row) {
                return [
                    'price_bid_item'     => $row['price_bid_item'] ?? null,
                    'field_type'         => $row['field_type'] ?? null,
                    'is_enabled'         => $row['is_enabled'] ?? null,
                    'is_boq_applicable'  => $row['is_boq_applicable'] ?? null,
                ];
            }, $sheetData);
            if (count($record) > 0) {

                $items = array_column($record, 'price_bid_item');
                $duplicates = array_diff_assoc($items, array_unique($items));
                if (!empty($duplicates)) {
                    if (Storage::disk($disk)->exists($originalFileName)) {
                        Storage::disk($disk)->delete($originalFileName);
                    }
                    return [
                        'success' => false,
                        'message' => trans(
                            'srm_tender_rfx.item_duplicated',
                            ['item' => reset($duplicates)]
                        )
                    ];
                }

                $duplicateEntries = [];
                $employee = \Helper::getEmployeeInfo();
                foreach ($record as $vl){
                    $exist = TenderBidFormatDetail::checkItemExists($input['priceBidFormatId'], $vl['price_bid_item']);
                    if(empty($exist)){
                        $fieldType = TenderFieldType::getFieldTypeId($vl['field_type']);
                        $data = [
                            'tender_id' => $input['priceBidFormatId'],
                            'label' => $vl['price_bid_item'],
                            'field_type' => $fieldType['id'] ?? null,
                            'is_disabled' => (
                                isset($vl['is_enabled']) &&
                                strtolower(trim($vl['is_enabled'])) === 'yes'
                            ) ? 1 : 0,
                            'boq_applicable' => (
                                isset($vl['is_boq_applicable']) &&
                                strtolower(trim($vl['is_boq_applicable'])) === 'yes'
                            ) ? 1 : 0,
                            'created_by' => $employee->employeeSystemID,
                        ];
                        TenderBidFormatDetail::create($data);
                    }else{
                        array_push($duplicateEntries,$vl);
                        if (!empty($duplicateEntries)) {
                            if (Storage::disk($disk)->exists($originalFileName)) {
                                Storage::disk($disk)->delete($originalFileName);
                            }
                            foreach ($duplicateEntries as $key => $dupl) {
                                return ['success' => false, 'message' => trans('srm_tender_rfx.item_already_exist', ['item' => $dupl['price_bid_item']])];
                            }
                        }
                    }
                }
            } else {
                if (Storage::disk($disk)->exists($originalFileName)) {
                    Storage::disk($disk)->delete($originalFileName);
                }
                return ['success' => false, 'message' => trans('srm_tender_rfx.no_records_found')];
            }
            DB::commit();

            if (Storage::disk($disk)->exists($originalFileName)) {
                Storage::disk($disk)->delete($originalFileName);
            }

            return [
                'success' => true,
                'message' => trans('srm_tender_rfx.items_uploaded_successfully'),
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            if (isset($disk) && isset($originalFileName) && Storage::disk($disk)->exists($originalFileName)) {
                Storage::disk($disk)->delete($originalFileName);
            }
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $exception->getMessage()])
            ];
        }
    }

    public function checkValidUploadRequestParams($input){
        $validator = Validator::make($input, [
            'priceBidFormatId' => 'required',
            'companySystemID' => 'required',
            'itemExcel' => 'required',
        ], [
            'priceBidFormatId.required' => trans('srm_tender_rfx.price_bid_format_master_id_required'),
            'companySystemID.required' => trans('srm_tender_rfx.company_id_required'),
            'itemExcel.required' => trans('srm_tender_rfx.attachment_not_found'),
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => implode(', ', $validator->errors()->all())];
        }
        return ['success' => true, 'message' => trans('srm_tender_rfx.success')];
    }

    private function validateExcelUpload(array $excelUpload): array
    {
        if (empty($excelUpload)) {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.attachment_not_found')
            ];
        }

        $file = $excelUpload[0];

        $extension = $file['filetype'] ?? null;
        $size      = $file['size'] ?? 0;

        $allowedExtensions = ['xlsx', 'xls'];

        if (!in_array($extension, $allowedExtensions)) {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.file_type_not_allowed')
            ];
        }

        if ($size > 20000000) {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.file_size_exceeded')
            ];
        }

        return ['success' => true];
    }

}
