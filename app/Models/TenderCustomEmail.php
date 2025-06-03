<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TenderCustomEmail extends Model
{
    protected $table = 'tender_custom_emails';

    protected $fillable = [
        'tender_id',
        'supplier_id',
        'company_id',
        'user_id',
        'user_group',
        'pc_id',
        'document_code',
        'created_by',
        'cc_email',
        'email_subject',
        'email_body',
        'document_id',
        'negotiation_id'
    ];

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierRegistrationLink', 'supplier_id', 'id');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\DocumentAttachments','attachmentID', 'document_id');
    }

    public static function createOrUpdateCustomEmail($list, $data)
    {
        return self::updateOrCreate($list, $data);
    }

    public static function getCustomEmailData($tenderId, $negotiationId)
    {
        $records = self::where('tender_id', $tenderId)
            ->with(['supplier'])
            ->where('negotiation_id', $negotiationId)
            ->get();

        $responseData = [];
        foreach ($records as $record) {

            $supplier = $record->supplier;
            $responseData[] = [
                'id' => $record->id,
                'supplier_uuid' => $supplier->uuid,
                'supplier_name' => $supplier ? $supplier->name : null
            ];
        }

        $supplierList = collect(SupplierTenderNegotiation::getSupplierList($negotiationId, $tenderId))
            ->filter(function ($record) {
                return isset($record['id']);
            })
            ->values();

        $existingSupplierIds = collect($responseData)->pluck('supplier_uuid')->all();

        foreach ($supplierList as $record) {
            if (!in_array($record['id'], $existingSupplierIds, true)) {
                $responseData[] = [
                    'id' => null,
                    'supplier_uuid' => $record['id'],
                    'supplier_name' => $record['name'] ?? null,
                ];
            }
        }

        return $responseData;
    }

    public static function getCustomEmailSupplier($tenderId, $supplierId, $documentCode)
    {
        return self::where('tender_id', $tenderId)
            ->with(['attachment' => function ($query) {
                $query->select('attachmentID', 'path', 'originalFileName');
            }])
            ->where('supplier_id', $supplierId)
            ->where('document_code', $documentCode)
            ->first();
    }

    public static function getSupplierCustomEmailBody($tenderId, $supplierMasterId, $documentCode)
    {
        return TenderCustomEmail::select('email_body', 'cc_email', 'document_id', 'supplier_id', 'email_subject')
            ->with(['attachment' => function ($q) {
                $q->select('attachmentID', 'path', 'originalFileName');
            }])
            ->where('tender_Id', $tenderId)
            ->where('supplier_id', $supplierMasterId)
            ->where('document_code', $documentCode)
            ->first();
    }
}
