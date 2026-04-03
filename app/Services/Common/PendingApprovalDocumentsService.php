<?php

namespace App\Services\Common;

use App\Models\CompanyFinancePeriod;
use App\Models\DepartmentMaster;
use App\Models\DocumentMaster;
use App\helper\StatusService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PendingApprovalDocumentsService
{
    /** @var array<string, bool> */
    private array $columnExistsCache = [];

    /**
     * Returns the configured registry grouped by module (department), with table name details.
     * Useful to verify "which models/tables are checked" for a selected module.
     *
     * @return array<int, array<string, mixed>>
     */
    public function registryByDepartment(?int $departmentSystemID = null): array
    {
        $definitions = $this->definitions();

        $docMasters = DocumentMaster::whereIn('documentSystemID', collect($definitions)->pluck('documentSystemID')->filter()->values()->all())
            ->get(['documentSystemID', 'departmentSystemID', 'documentID', 'documentDescription'])
            ->keyBy('documentSystemID');

        $departmentIds = $docMasters->pluck('departmentSystemID')->filter()->unique()->values()->all();
        $departments = DepartmentMaster::whereIn('departmentSystemID', $departmentIds)
            ->get(['departmentSystemID', 'DepartmentDescription'])
            ->keyBy('departmentSystemID');

        $grouped = [];

        foreach ($definitions as $def) {
            $docSystemId = (int)($def['documentSystemID'] ?? 0);
            $docMaster = $docSystemId ? ($docMasters[$docSystemId] ?? null) : null;
            $depSystemId = $docMaster ? (int)$docMaster->departmentSystemID : 0;

            if ($departmentSystemID && $depSystemId !== $departmentSystemID) {
                continue;
            }

            $modelClass = $def['model'];
            $table = null;
            try {
                $table = (new $modelClass())->getTable();
            } catch (\Throwable $e) {
                $table = null;
            }

            $grouped[$depSystemId ?: 0]['departmentSystemID'] = $depSystemId ?: null;
            $grouped[$depSystemId ?: 0]['department'] = ($depSystemId && isset($departments[$depSystemId]))
                ? $departments[$depSystemId]->DepartmentDescription
                : null;
            $grouped[$depSystemId ?: 0]['documents'][] = [
                'documentSystemID' => $docSystemId ?: null,
                'documentID' => $docMaster ? $docMaster->documentID : null,
                'documentDescription' => $docMaster ? $docMaster->documentDescription : null,
                'model' => $modelClass,
                'table' => $table,
                'primaryKey' => $def['primaryKey'] ?? null,
                'periodStrategy' => $def['periodStrategy'] ?? 'direct',
                'periodField' => $def['periodField'] ?? null,
                'codeField' => $def['codeField'] ?? null,
                'dateField' => $def['dateField'] ?? null,
                'confirmedField' => $def['confirmedField'] ?? null,
                'approvedField' => $def['approvedField'] ?? null,
            ];
        }

        // normalize to list
        return collect($grouped)
            ->values()
            ->map(function ($g) {
                $g['documents'] = collect($g['documents'] ?? [])
                    ->sortBy('documentSystemID')
                    ->values()
                    ->all();
                return $g;
            })
            ->all();
    }

    /**
     * Returns a normalized list of "confirmed & pending approval" documents in a finance period.
     *
     * Output row:
     * - documentSystemID
     * - documentCode
     * - documentDate
     * - status
     */
    public function listByFinancePeriod(int $companySystemID, int $companyFinancePeriodID, ?int $departmentSystemID = null): array
    {
        $rows = collect();

        $definitions = $this->definitions();

        $period = CompanyFinancePeriod::query()
            ->where('companyFinancePeriodID', $companyFinancePeriodID)
            ->where('companySystemID', $companySystemID)
            ->first();

        if (!$period) {
            return [];
        }

        if ($departmentSystemID !== null && (int)$period->departmentSystemID !== (int)$departmentSystemID) {
            return [];
        }

        $effectiveDepartmentSystemID = $departmentSystemID !== null
            ? (int)$departmentSystemID
            : (int)$period->departmentSystemID;

        $docMasterQuery = DocumentMaster::whereIn(
            'documentSystemID',
            collect($definitions)->pluck('documentSystemID')->filter()->values()->all()
        );

        if ($effectiveDepartmentSystemID) {
            $docMasterQuery->where('departmentSystemID', $effectiveDepartmentSystemID);
        }

        $docMasterBySystemId = $docMasterQuery
            ->get(['documentSystemID', 'departmentSystemID', 'documentDescription'])
            ->keyBy('documentSystemID');

        $definitions = array_values(array_filter($definitions, function ($def) use ($docMasterBySystemId) {
            $docSystemId = (int)($def['documentSystemID'] ?? 0);
            return $docSystemId && isset($docMasterBySystemId[$docSystemId]);
        }));

        $departmentBySystemId = DepartmentMaster::whereIn(
            'departmentSystemID',
            $docMasterBySystemId->pluck('departmentSystemID')->filter()->unique()->values()->all()
        )
            ->get(['departmentSystemID', 'DepartmentDescription'])
            ->keyBy('departmentSystemID');

        foreach ($definitions as $def) {
            /** @var class-string<\Illuminate\Database\Eloquent\Model> $model */
            $model = $def['model'];
            $table = (new $model())->getTable();

            if (!$this->hasRequiredColumns($table, $def)) {
                Log::warning('PendingApprovalDocumentsService definition skipped due to missing columns', [
                    'model' => $model,
                    'table' => $table,
                    'documentSystemID' => $def['documentSystemID'] ?? null,
                ]);
                continue;
            }

            $docSystemIdDef = (int)($def['documentSystemID'] ?? 0);
            $docSystemIdCol = $def['documentSystemIDField'] ?? 'documentSystemID';
            $skipDocSysCol = !empty($def['skipDocumentSystemIdRowFilter']);

            $query = $this->baseQueryForModel($model, $def);
            $this->applyCompanyScope($query, $def, $companySystemID);
            if (!$skipDocSysCol) {
                $query->where($docSystemIdCol, $docSystemIdDef);
            }
            foreach ($def['extraWheres'] ?? [] as $w) {
                if (count($w) >= 3) {
                    $query->where($w[0], $w[1], $w[2]);
                }
            }
            $this->applyPendingApprovalClause($query, $def);
            $this->applyPeriodStrategyForList($query, $table, $def, $period);

            $strategy = $def['periodStrategy'] ?? 'direct';
            $extraSelectFields = array_values(array_filter(($def['selectFields'] ?? [])));

            if ($strategy === 'year_month') {
                $y = $def['yearField'];
                $m = $def['monthField'];
                $docSysSelect = $skipDocSysCol
                    ? (string)$docSystemIdDef . ' as documentSystemID'
                    : '`' . $table . '`.`' . $docSystemIdCol . '` as documentSystemID';
                $extraSelectRaw = '';
                foreach ($extraSelectFields as $f) {
                    $extraSelectRaw .= ', `' . $table . '`.`' . $f . '` as `' . $f . '`';
                }
                $docs = $query
                    ->selectRaw(
                        '`' . $table . '`.`' . $def['primaryKey'] . '` as _id, '
                        . $docSysSelect . ', '
                        . '`' . $table . '`.`' . $def['codeField'] . '` as documentCode, '
                        . 'STR_TO_DATE(CONCAT(`' . $table . '`.`' . $y . '`, \'-\', LPAD(CAST(`' . $table . '`.`' . $m . '` AS CHAR), 2, \'0\'), \'-01\'), \'%Y-%m-%d\') as documentDate'
                        . $extraSelectRaw
                    )
                    ->orderBy($table . '.' . $y, 'asc')
                    ->orderBy($table . '.' . $m, 'asc')
                    ->get();
            } else {
                $orderCol = $def['dateField'] ?? $def['codeField'] ?? $def['primaryKey'];
                $dateSelect = $def['dateField'] ?? $def['codeField'];

                if ($skipDocSysCol) {
                    $extraSelectRaw = '';
                    foreach ($extraSelectFields as $f) {
                        $extraSelectRaw .= ', `' . $table . '`.`' . $f . '` as `' . $f . '`';
                    }
                    $docs = $query
                        ->selectRaw(
                            '`' . $table . '`.`' . $def['primaryKey'] . '` as _id, '
                            . (string)$docSystemIdDef . ' as documentSystemID, '
                            . '`' . $table . '`.`' . $def['codeField'] . '` as documentCode, '
                            . '`' . $table . '`.`' . $dateSelect . '` as documentDate'
                            . $extraSelectRaw
                        )
                        ->orderBy($orderCol, 'asc')
                        ->get();
                } else {
                    $select = [
                        $def['primaryKey'] . ' as _id',
                        $docSystemIdCol . ' as documentSystemID',
                        $def['codeField'] . ' as documentCode',
                        $dateSelect . ' as documentDate',
                    ];
                    foreach ($extraSelectFields as $f) {
                        $select[] = $f;
                    }
                    $docs = $query
                        ->select($select)
                        ->orderBy($orderCol, 'asc')
                        ->get();
                }
            }

            $rows = $rows->merge($docs->map(function ($d) use ($docMasterBySystemId, $departmentBySystemId, $extraSelectFields) {
                $docSystemId = (int)($d->documentSystemID ?? 0);
                $docMaster = $docSystemId ? ($docMasterBySystemId[$docSystemId] ?? null) : null;
                $depSystemId = $docMaster ? (int)($docMaster->departmentSystemID ?? 0) : 0;
                $dep = $depSystemId ? ($departmentBySystemId[$depSystemId] ?? null) : null;

                $row = [
                    'documentSystemID' => $docSystemId,
                    'documentCode' => $d->documentCode,
                    'documentDate' => $this->formatDocumentDate($d->documentDate),
                    'status' => StatusService::getStatus(null, null, 1, 0, 0),
                    'moduleDepartmentSystemID' => $depSystemId ?: null,
                    'module' => $dep ? $dep->DepartmentDescription : null,
                    'document' => $docMaster ? $docMaster->documentDescription : null,
                ];

                foreach ($extraSelectFields as $f) {
                    $row[$f] = $d->{$f} ?? null;
                }

                return $row;
            }));
        }

        return $rows
            ->sortBy('documentDate')
            ->values()
            ->all();
    }

    /**
     * When approving after the document-date finance period was closed, the UI must confirm
     * that GL posting will use the current (approval) date while the document date stays historical.
     *
     * @return array|null null = document type not covered by registry or could not be evaluated safely
     */
    public function closedPeriodApproveConfirmationPreview(int $documentSystemID, $documentSystemCode): ?array
    {
        foreach ($this->definitions() as $def) {
            if ((int)($def['documentSystemID'] ?? 0) !== $documentSystemID) {
                continue;
            }

            $modelClass = $def['model'];
            $table = (new $modelClass())->getTable();

            if (!$this->hasRequiredColumns($table, $def)) {
                return null;
            }

            $q = $modelClass::query();
            foreach ($def['withoutGlobalScopes'] ?? [] as $scopeName) {
                $q->withoutGlobalScope($scopeName);
            }
            if (!empty($def['companySystemIDFromRelation'])) {
                $q->with($def['companySystemIDFromRelation']['relation']);
            }

            /** @var \Illuminate\Database\Eloquent\Model|null $rec */
            $rec = $q->find($documentSystemCode);
            if (!$rec) {
                return null;
            }

            $companySystemID = $this->resolveCompanySystemIdForRecord($def, $rec);
            if ($companySystemID <= 0) {
                return null;
            }

            $period = $this->resolveFinancePeriodForRecord($table, $def, $rec, $documentSystemID, $companySystemID);
            if (!$period) {
                return null;
            }

            if ((int)$period->isClosed !== -1) {
                return ['needsConfirmation' => false];
            }

            $rawDate = $this->resolveDocumentDateForPreview($def, $rec);
            $documentDateFmt = '';
            if (!empty($rawDate)) {
                try {
                    $documentDateFmt = Carbon::parse($rawDate)->format('d/m/Y');
                } catch (\Throwable $e) {
                    $documentDateFmt = substr((string)$rawDate, 0, 10);
                }
            }

            $approvingDateFmt = Carbon::now()->format('d/m/Y');

            return [
                'needsConfirmation' => true,
                'documentDateFormatted' => $documentDateFmt,
                'approvingDateFormatted' => $approvingDateFmt,
            ];
        }

        return null;
    }

    /**
     * Registry keyed by `documentSystemID` — aligned with `DocumentApprove` / `DocumentApproveApi` approval types.
     * Definitions with missing DB columns are skipped at runtime (`hasRequiredColumns` + warning log).
     *
     * @return array<int, array<string, mixed>>
     */
    private function definitions(): array
    {
        return [
            // --- Inventory (period-linked) ---
            [
                'model' => \App\Models\GRVMaster::class,
                'primaryKey' => 'grvAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 3,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'grvPrimaryCode',
                'dateField' => 'grvDate',
                'confirmedField' => 'grvConfirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\ItemIssueMaster::class,
                'primaryKey' => 'itemIssueAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 8,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'itemIssueCode',
                'dateField' => 'issueDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\ItemReturnMaster::class,
                'primaryKey' => 'itemReturnAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 12,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'itemReturnCode',
                'dateField' => 'returnDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\StockTransfer::class,
                'primaryKey' => 'stockTransferAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 13,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'stockTransferCode',
                'dateField' => 'tranferDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\StockReceive::class,
                'primaryKey' => 'stockReceiveAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 10,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'stockReceiveCode',
                'dateField' => 'receivedDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\StockAdjustment::class,
                'primaryKey' => 'stockAdjustmentAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 7,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'stockAdjustmentCode',
                'dateField' => 'stockAdjustmentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\PurchaseReturn::class,
                'primaryKey' => 'purhaseReturnAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 24,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'purchaseReturnCode',
                'dateField' => 'purchaseReturnDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\StockCount::class,
                'primaryKey' => 'stockCountAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 97,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'stockCountCode',
                'dateField' => 'stockCountDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\InventoryReclassification::class,
                'primaryKey' => 'inventoryreclassificationID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 61,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'documentCode',
                'dateField' => 'documentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],

            // Inventory — document date vs finance period range (no stored companyFinancePeriodID)
            [
                'model' => \App\Models\PurchaseRequest::class,
                'primaryKey' => 'purchaseRequestID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 1,
                'periodStrategy' => 'date_range',
                'codeField' => 'purchaseRequestCode',
                'dateField' => 'PRRequestedDate',
                'confirmedField' => 'PRConfirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\PurchaseRequest::class,
                'primaryKey' => 'purchaseRequestID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 50,
                'periodStrategy' => 'date_range',
                'codeField' => 'purchaseRequestCode',
                'dateField' => 'PRRequestedDate',
                'confirmedField' => 'PRConfirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\PurchaseRequest::class,
                'primaryKey' => 'purchaseRequestID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 51,
                'periodStrategy' => 'date_range',
                'codeField' => 'purchaseRequestCode',
                'dateField' => 'PRRequestedDate',
                'confirmedField' => 'PRConfirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\ProcumentOrder::class,
                'primaryKey' => 'purchaseOrderID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 2,
                'periodStrategy' => 'date_range',
                'codeField' => 'purchaseOrderCode',
                'dateField' => 'POOrderedDate',
                'confirmedField' => 'poConfirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\ProcumentOrder::class,
                'primaryKey' => 'purchaseOrderID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 5,
                'periodStrategy' => 'date_range',
                'codeField' => 'purchaseOrderCode',
                'dateField' => 'POOrderedDate',
                'confirmedField' => 'poConfirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\ProcumentOrder::class,
                'primaryKey' => 'purchaseOrderID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 52,
                'periodStrategy' => 'date_range',
                'codeField' => 'purchaseOrderCode',
                'dateField' => 'POOrderedDate',
                'confirmedField' => 'poConfirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\MaterielRequest::class,
                'primaryKey' => 'RequestID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 9,
                'periodStrategy' => 'date_range',
                'codeField' => 'RequestCode',
                'dateField' => 'RequestedDate',
                'confirmedField' => 'ConfirmedYN',
                'approvedField' => 'approved',
            ],

            // --- AP ---
            [
                'model' => \App\Models\BookInvSuppMaster::class,
                'primaryKey' => 'bookingSuppMasInvAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 11,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'bookingInvCode',
                'dateField' => 'bookingDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\DebitNote::class,
                'primaryKey' => 'debitNoteAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 15,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'debitNoteCode',
                'dateField' => 'debitNoteDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\PaySupplierInvoiceMaster::class,
                'primaryKey' => 'PayMasterAutoId',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 4,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'BPVcode',
                'dateField' => 'BPVdate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\MatchDocumentMaster::class,
                'primaryKey' => 'matchDocumentMasterAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 70,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'matchingDocCode',
                'dateField' => 'matchingDocdate',
                'confirmedField' => 'matchingConfirmedYN',
                'approvedField' => 'approved',
            ],

            // --- AR / Sales ---
            [
                'model' => \App\Models\CustomerInvoiceDirect::class,
                'primaryKey' => 'custInvoiceDirectAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 20,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'bookingInvCode',
                'dateField' => 'bookingDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\CreditNote::class,
                'primaryKey' => 'creditNoteAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 19,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'creditNoteCode',
                'dateField' => 'creditNoteDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\CustomerReceivePayment::class,
                'primaryKey' => 'custReceivePaymentAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 21,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'custPaymentReceiveCode',
                'dateField' => 'custPaymentReceiveDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\DeliveryOrder::class,
                'primaryKey' => 'deliveryOrderID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 71,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'deliveryOrderCode',
                'dateField' => 'deliveryOrderDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\SalesReturn::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 87,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'salesReturnCode',
                'dateField' => 'salesReturnDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\QuotationMaster::class,
                'primaryKey' => 'quotationMasterID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 67,
                'periodStrategy' => 'date_range',
                'codeField' => 'quotationCode',
                'dateField' => 'documentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\QuotationMaster::class,
                'primaryKey' => 'quotationMasterID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 68,
                'periodStrategy' => 'date_range',
                'codeField' => 'quotationCode',
                'dateField' => 'documentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],

            // --- GL ---
            [
                'model' => \App\Models\JvMaster::class,
                'primaryKey' => 'jvMasterAutoId',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 17,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'JVcode',
                'dateField' => 'JVdate',
                // Needed to differentiate Salary JV (jvType == 3) for finance-period close blocking.
                'selectFields' => ['jvType'],
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\ConsoleJVMaster::class,
                'primaryKey' => 'consoleJvMasterAutoId',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 69,
                'periodStrategy' => 'date_range',
                'codeField' => 'consoleJVcode',
                'dateField' => 'consoleJVdate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\RecurringVoucherSetup::class,
                'primaryKey' => 'recurringVoucherAutoId',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 119,
                'periodStrategy' => 'date_range',
                'codeField' => 'RRVcode',
                'dateField' => 'processDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],

            // --- Treasury ---
            [
                'model' => \App\Models\BankReconciliation::class,
                'primaryKey' => 'bankRecAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 62,
                'periodStrategy' => 'date_range',
                'codeField' => 'bankRecPrimaryCode',
                'dateField' => 'bankRecAsOf',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\PaymentBankTransfer::class,
                'primaryKey' => 'paymentBankTransferID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 64,
                'periodStrategy' => 'date_range',
                'codeField' => 'bankTransferDocumentCode',
                'dateField' => 'documentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\BankAccount::class,
                'primaryKey' => 'bankAccountAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 66,
                'periodStrategy' => 'date_range',
                'codeField' => 'bankShortCode',
                'dateField' => 'createdDateTime',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],

            // --- Fixed assets ---
            [
                'model' => \App\Models\FixedAssetMaster::class,
                'primaryKey' => 'faID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 22,
                'periodStrategy' => 'date_range',
                'codeField' => 'faCode',
                'dateField' => 'documentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\AssetCapitalization::class,
                'primaryKey' => 'capitalizationID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 63,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'capitalizationCode',
                'dateField' => 'documentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\FixedAssetDepreciationMaster::class,
                'primaryKey' => 'depMasterAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 23,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'depCode',
                'dateField' => 'depDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\AssetDisposalMaster::class,
                'primaryKey' => 'assetdisposalMasterAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 41,
                'periodStrategy' => 'direct',
                'periodField' => 'companyFinancePeriodID',
                'codeField' => 'disposalDocumentCode',
                'dateField' => 'disposalDocumentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\AssetVerification::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 99,
                'periodStrategy' => 'date_range',
                'codeField' => 'verficationCode',
                'dateField' => 'documentDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\ERPAssetTransfer::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 103,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'company_id',
                'codeField' => 'document_code',
                'dateField' => 'document_date',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved_yn',
            ],

            // --- Budget ---
            [
                'model' => \App\Models\BudgetMaster::class,
                'primaryKey' => 'budgetmasterID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 65,
                'periodStrategy' => 'year_month',
                'yearField' => 'Year',
                'monthField' => 'month',
                'codeField' => 'documentID',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\BudgetTransferForm::class,
                'primaryKey' => 'budgetTransferFormAutoID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 46,
                'periodStrategy' => 'date_range',
                'codeField' => 'transferVoucherNo',
                'dateField' => 'createdDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\ContingencyBudgetPlan::class,
                'primaryKey' => 'ID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 100,
                'periodStrategy' => 'date_range',
                'codeField' => 'documentID',
                'dateField' => 'createdDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\ErpBudgetAddition::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 102,
                'periodStrategy' => 'date_range',
                'codeField' => 'additionVoucherNo',
                'dateField' => 'createdDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\CompanyBudgetPlanning::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 133,
                'periodStrategy' => 'date_range',
                'codeField' => 'planningCode',
                'dateField' => 'submissionDate',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved_yn',
            ],

            // --- Tax / compliance ---
            [
                'model' => \App\Models\VatReturnFillingMaster::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 104,
                'periodStrategy' => 'date_range',
                'codeField' => 'returnFillingCode',
                'dateField' => 'date',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],

            // --- HR payroll (monthly additions) ---
            [
                'model' => \App\Models\MonthlyAdditionsMaster::class,
                'primaryKey' => 'monthlyAdditionsMasterID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 28,
                'periodStrategy' => 'date_range',
                'codeField' => 'monthlyAdditionsCode',
                'dateField' => 'dateMA',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],

            // --- SRM (tender / RFX share one table; filter by document_system_id) ---
            [
                'model' => \App\Models\TenderMaster::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'document_system_id',
                'documentSystemID' => 108,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'company_id',
                'codeField' => 'tender_code',
                'dateField' => 'created_at',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved',
            ],
            [
                'model' => \App\Models\TenderMaster::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'document_system_id',
                'documentSystemID' => 113,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'company_id',
                'codeField' => 'tender_code',
                'dateField' => 'created_at',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved',
            ],

            // --- Master data (DocumentApprove 56–59, 86) — date vs finance period range ---
            [
                'model' => \App\Models\SupplierMaster::class,
                'primaryKey' => 'supplierCodeSystem',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 56,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'primaryCompanySystemID',
                'codeField' => 'supplierName',
                'dateField' => 'createdDateTime',
                'confirmedField' => 'supplierConfirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\ItemMaster::class,
                'primaryKey' => 'itemCodeSystem',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 57,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'primaryCompanySystemID',
                'codeField' => 'primaryCode',
                'dateField' => 'createdDateTime',
                'confirmedField' => 'itemConfirmedYN',
                'approvedField' => 'itemApprovedYN',
            ],
            [
                'model' => \App\Models\CustomerMaster::class,
                'primaryKey' => 'customerCodeSystem',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 58,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'primaryCompanySystemID',
                'codeField' => 'CutomerCode',
                'dateField' => 'createdDateTime',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],
            [
                'model' => \App\Models\ChartOfAccount::class,
                'primaryKey' => 'chartOfAccountSystemID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 59,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'primaryCompanySystemID',
                'codeField' => 'AccountCode',
                'dateField' => 'createdDateTime',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'isApproved',
            ],
            [
                'model' => \App\Models\RegisteredSupplier::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 86,
                'periodStrategy' => 'date_range',
                'skipDocumentSystemIdRowFilter' => true,
                'codeField' => 'registrationNumber',
                'dateField' => 'createdDate',
                'confirmedField' => 'supplierConfirmedYN',
                'approvedField' => 'approvedYN',
            ],

            // --- Treasury: currency conversion (company inferred from creator’s employee record) ---
            [
                'model' => \App\Models\CurrencyConversionMaster::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 96,
                'periodStrategy' => 'date_range',
                'skipDocumentSystemIdRowFilter' => true,
                'companyScopeViaCreatedByEmployee' => true,
                'companySystemIDFromRelation' => ['relation' => 'created_by', 'field' => 'empCompanySystemID'],
                'codeField' => 'conversionCode',
                'dateField' => 'conversionDate',
                'confirmedField' => 'confirmedYN',
                'approvedField' => 'approvedYN',
            ],

            // --- HR / SRM misc ---
            [
                'model' => \App\Models\Appointment::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'document_system_id',
                'documentSystemID' => 106,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'company_id',
                'codeField' => 'primary_code',
                'dateField' => 'created_at',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved_yn',
            ],
            [
                'model' => \App\Models\SupplierRegistrationLink::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 107,
                'periodStrategy' => 'date_range',
                'skipDocumentSystemIdRowFilter' => true,
                'companyIdField' => 'company_id',
                'codeField' => 'registration_number',
                'dateField' => 'created_at',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved_yn',
            ],

            // --- Document modify (same table; two approval stages) ---
            [
                'model' => \App\Models\DocumentModifyRequest::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 117,
                'periodStrategy' => 'date_range',
                'skipDocumentSystemIdRowFilter' => true,
                'codeField' => 'code',
                'dateField' => 'requested_date',
                'confirmedField' => 'requested',
                'confirmedValues' => [1],
                'approvedField' => 'approved',
                'pendingApprovedValues' => [0],
            ],
            [
                'model' => \App\Models\DocumentModifyRequest::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 118,
                'periodStrategy' => 'date_range',
                'skipDocumentSystemIdRowFilter' => true,
                'codeField' => 'code',
                'dateField' => 'requested_date',
                'confirmedField' => 'requested',
                'confirmedValues' => [1],
                'approvedField' => 'confirmation_approved',
                'pendingApprovedValues' => [0],
                'extraWheres' => [
                    ['approved', '<>', 0],
                ],
            ],

            // --- SRM payment proof ---
            [
                'model' => \App\Models\SRMTenderPaymentProof::class,
                'primaryKey' => 'id',
                'documentSystemIDField' => 'document_system_id',
                'documentSystemID' => 127,
                'periodStrategy' => 'date_range',
                'companyIdField' => 'company_id',
                'codeField' => 'document_code',
                'dateField' => 'timestamp',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved_yn',
            ],

            // --- Segment (service line) — global scopes bypassed for approval rows ---
            [
                'model' => \App\Models\SegmentMaster::class,
                'primaryKey' => 'serviceLineSystemID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 132,
                'periodStrategy' => 'date_range',
                'withoutGlobalScopes' => ['final_level', 'deleted_status'],
                'codeField' => 'ServiceLineCode',
                'dateField' => 'createdDateTime',
                'confirmedField' => 'confirmed_yn',
                'approvedField' => 'approved_yn',
            ],

            // --- POS (no documentSystemID on row; pos_type 1 = GPOS invoice, 2 = RPOS menu — matches GL 110 / 111) ---
            [
                'model' => \App\Models\POSInvoiceSource::class,
                'primaryKey' => 'invoiceID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 110,
                'periodStrategy' => 'date_range',
                'skipDocumentSystemIdRowFilter' => true,
                'companyIdField' => 'companyID',
                'pendingApprovalMode' => 'pos_void_only',
                'voidField' => 'isVoid',
                'codeField' => 'invoiceCode',
                'dateField' => 'invoiceDate',
                'extraWheres' => [
                    ['pos_type', '=', 1],
                ],
            ],
            [
                'model' => \App\Models\POSSourceMenuSalesMaster::class,
                'primaryKey' => 'menuSalesID',
                'documentSystemIDField' => 'documentSystemID',
                'documentSystemID' => 111,
                'periodStrategy' => 'date_range',
                'skipDocumentSystemIdRowFilter' => true,
                'companyIdField' => 'companyID',
                'pendingApprovalMode' => 'pos_void_only',
                'voidField' => 'isVoid',
                'codeField' => 'invoiceCode',
                'dateField' => 'menuSalesDate',
                'extraWheres' => [
                    ['pos_type', '=', 2],
                ],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $def
     */
    private function hasRequiredColumns(string $table, array $def): bool
    {
        $strategy = $def['periodStrategy'] ?? 'direct';

        if (empty($def['skipCompanyScope']) && empty($def['companyScopeViaCreatedByEmployee'])) {
            $companyField = $def['companyIdField'] ?? 'companySystemID';
            if (!$companyField || !$this->columnExists($table, (string)$companyField)) {
                return false;
            }
        }

        if (empty($def['skipDocumentSystemIdRowFilter'])) {
            $dsField = $def['documentSystemIDField'] ?? 'documentSystemID';
            if (!$dsField || !$this->columnExists($table, (string)$dsField)) {
                return false;
            }
        }

        if (!empty($def['companyScopeViaCreatedByEmployee']) && !$this->columnExists($table, 'createdBy')) {
            return false;
        }

        $pendingMode = $def['pendingApprovalMode'] ?? 'standard';

        $required = [
            $def['primaryKey'] ?? null,
            $def['codeField'] ?? null,
        ];

        if ($pendingMode === 'pos_void_only') {
            $required[] = $def['voidField'] ?? 'isVoid';
        } else {
            $required[] = $def['confirmedField'] ?? null;
            $required[] = $def['approvedField'] ?? null;
        }

        if ($strategy === 'direct') {
            $required[] = $def['periodField'] ?? null;
        } elseif ($strategy === 'date_range') {
            $required[] = $def['dateField'] ?? null;
        } elseif ($strategy === 'year_month') {
            $required[] = $def['yearField'] ?? null;
            $required[] = $def['monthField'] ?? null;
        } else {
            return false;
        }

        foreach ($required as $column) {
            if (!$column) {
                return false;
            }
            if (!$this->columnExists($table, (string)$column)) {
                return false;
            }
        }

        foreach (($def['selectFields'] ?? []) as $column) {
            if ($column && !$this->columnExists($table, (string)$column)) {
                return false;
            }
        }

        foreach ($def['extraWheres'] ?? [] as $w) {
            if (isset($w[0]) && !$this->columnExists($table, (string)$w[0])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $modelClass
     */
    private function baseQueryForModel(string $modelClass, array $def)
    {
        $q = $modelClass::query();
        foreach ($def['withoutGlobalScopes'] ?? [] as $scopeName) {
            $q->withoutGlobalScope($scopeName);
        }

        return $q;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $rec
     */
    private function resolveCompanySystemIdForRecord(array $def, $rec): int
    {
        if (!empty($def['companySystemIDFromRelation'])) {
            $cfg = $def['companySystemIDFromRelation'];
            $rel = $cfg['relation'];
            $field = $cfg['field'];
            $parent = $rec->{$rel} ?? null;
            if (!$parent && method_exists($rec, $rel)) {
                $parent = $rec->$rel()->first();
            }

            return $parent ? (int)($parent->{$field} ?? 0) : 0;
        }

        $companyField = $def['companyIdField'] ?? 'companySystemID';

        return (int)($rec->{$companyField} ?? 0);
    }

    /**
     * @param Builder $query
     */
    private function applyCompanyScope($query, array $def, int $companySystemID): void
    {
        if (!empty($def['skipCompanyScope'])) {
            return;
        }

        if (!empty($def['companyScopeViaCreatedByEmployee'])) {
            $query->whereHas('created_by', function ($q) use ($companySystemID) {
                $q->where('empCompanySystemID', $companySystemID);
            });
            return;
        }

        $field = $def['companyIdField'] ?? 'companySystemID';
        $query->where($field, $companySystemID);
    }

    /**
     * @param Builder $query
     */
    private function applyPendingApprovalClause($query, array $def): void
    {
        if (($def['pendingApprovalMode'] ?? 'standard') === 'pos_void_only') {
            $voidField = $def['voidField'] ?? 'isVoid';
            $query->where($voidField, 0);

            return;
        }

        $confirmedValues = $def['confirmedValues'] ?? [1];
        $pendingApprovedValues = $def['pendingApprovedValues'] ?? [0];

        $query->whereIn($def['confirmedField'], $confirmedValues)
            ->whereIn($def['approvedField'], $pendingApprovedValues);
    }

    /**
     * @param Builder $query
     */
    private function applyPeriodStrategyForList($query, string $table, array $def, CompanyFinancePeriod $period): void
    {
        $strategy = $def['periodStrategy'] ?? 'direct';

        if ($strategy === 'direct') {
            $query->where($def['periodField'], $period->companyFinancePeriodID);
            return;
        }

        if ($strategy === 'date_range') {
            $from = Carbon::parse($period->dateFrom)->startOfDay();
            $to = Carbon::parse($period->dateTo)->endOfDay();
            $query->whereBetween($def['dateField'], [$from, $to]);
            return;
        }

        if ($strategy === 'year_month') {
            $this->applyYearMonthPeriodOverlap($query, $table, $def, $period);
        }
    }

    /**
     * Budget-style rows: overlap between calendar month (Year/month) and finance period range.
     *
     * @param Builder $query
     */
    private function applyYearMonthPeriodOverlap($query, string $table, array $def, CompanyFinancePeriod $period): void
    {
        $y = $def['yearField'];
        $m = $def['monthField'];
        $dateFrom = Carbon::parse($period->dateFrom)->format('Y-m-d');
        $dateTo = Carbon::parse($period->dateTo)->format('Y-m-d');

        $query->whereRaw(
            "STR_TO_DATE(CONCAT(`{$table}`.`{$y}`, '-', LPAD(CAST(`{$table}`.`{$m}` AS CHAR), 2, '0'), '-01'), '%Y-%m-%d') <= ? "
            . "AND LAST_DAY(STR_TO_DATE(CONCAT(`{$table}`.`{$y}`, '-', LPAD(CAST(`{$table}`.`{$m}` AS CHAR), 2, '0'), '-01'), '%Y-%m-%d')) >= ?",
            [$dateTo, $dateFrom]
        );
    }

    private function resolveFinancePeriodForRecord(
        string $table,
        array $def,
        $rec,
        int $documentSystemID,
        int $companySystemID
    ): ?CompanyFinancePeriod {
        $strategy = $def['periodStrategy'] ?? 'direct';

        if ($strategy === 'direct') {
            $periodId = $rec->{$def['periodField']} ?? null;
            return $periodId ? CompanyFinancePeriod::find($periodId) : null;
        }

        $dm = DocumentMaster::where('documentSystemID', $documentSystemID)->first();
        $depId = $dm ? (int)$dm->departmentSystemID : 0;
        if ($depId <= 0) {
            return null;
        }

        if ($strategy === 'date_range') {
            $rawDate = $rec->{$def['dateField']} ?? null;
            if (empty($rawDate)) {
                return null;
            }
            try {
                $docDate = Carbon::parse($rawDate)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }

            return CompanyFinancePeriod::query()
                ->where('companySystemID', $companySystemID)
                ->where('departmentSystemID', $depId)
                ->whereDate('dateFrom', '<=', $docDate)
                ->whereDate('dateTo', '>=', $docDate)
                ->orderBy('companyFinancePeriodID')
                ->first();
        }

        if ($strategy === 'year_month') {
            $y = (int)($rec->{$def['yearField']} ?? 0);
            $m = (int)($rec->{$def['monthField']} ?? 0);
            if ($y < 1900 || $m < 1 || $m > 12) {
                return null;
            }
            $monthStart = Carbon::create($y, $m, 1)->format('Y-m-d');
            $monthEnd = Carbon::create($y, $m, 1)->endOfMonth()->format('Y-m-d');

            return CompanyFinancePeriod::query()
                ->where('companySystemID', $companySystemID)
                ->where('departmentSystemID', $depId)
                ->whereDate('dateFrom', '<=', $monthEnd)
                ->whereDate('dateTo', '>=', $monthStart)
                ->orderBy('companyFinancePeriodID')
                ->first();
        }

        return null;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $rec
     */
    private function resolveDocumentDateForPreview(array $def, $rec): ?string
    {
        $strategy = $def['periodStrategy'] ?? 'direct';

        if ($strategy === 'year_month') {
            $y = (int)($rec->{$def['yearField']} ?? 0);
            $m = (int)($rec->{$def['monthField']} ?? 0);
            if ($y < 1900 || $m < 1 || $m > 12) {
                return null;
            }

            return Carbon::create($y, $m, 1)->format('Y-m-d');
        }

        $field = $def['dateField'] ?? null;
        if (!$field) {
            return null;
        }

        $raw = $rec->{$field} ?? null;

        return $raw !== null && $raw !== '' ? (string)$raw : null;
    }

    private function columnExists(string $table, string $column): bool
    {
        $key = $table . '.' . $column;
        if (!array_key_exists($key, $this->columnExistsCache)) {
            $this->columnExistsCache[$key] = Schema::hasColumn($table, $column);
        }
        return $this->columnExistsCache[$key];
    }

    /**
     * Normalize document date to YYYY-MM-DD for frontend modal.
     */
    private function formatDocumentDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            // Fallback: return first 10 chars when value already resembles date-time text.
            return substr((string)$value, 0, 10);
        }
    }
}

