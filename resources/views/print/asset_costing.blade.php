<html>
<head>
    <meta charset="utf-8">
    <title>{{ trans('custom.asset_costing_print_title') }}</title>
    <style>
        
        @page { margin-left: 30px; margin-right: 30px; margin-top: 30px; margin-bottom: 16px; }
        body {
            margin: 0;
            padding: 0 0 12px;
            color: #1f1f1f;
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            line-height: 1.2;
        }
        @if(app()->getLocale() == 'ar')
        body {
            font-family: 'Noto Sans Arabic', sans-serif !important;
        }
        @endif
        h3 {
            font-size: 24.5px;
            margin-top: 0;
            margin-bottom: 0;
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }
        table > tbody > tr > td {
            font-size: 11.5px;
        }
        table > thead > tr > th {
            font-size: 11.5px;
        }
        .theme-tr-head {
            background-color: rgb(215, 215, 215) !important;
        }
        .text-center {
            text-align: center;
        }
        @if(app()->getLocale() == 'ar')
        table {
            direction: rtl;
        }
        .ac-table th,
        .ac-table td,
        .ac-header td,
        .ac-footer-row td,
        .ac-inline-row td {
            text-align: right;
        }
        .text-center {
            text-align: center !important;
        }
        @endif
        .font-weight-bold {
            font-weight: 700 !important;
        }
        .text-muted {
            color: #dedede !important;
        }
        .ac-header { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
        .ac-header-logo { width: 42%; vertical-align: top; }
        .ac-header-meta { width: 58%; vertical-align: top; }
        .company-logo { max-width: 180px; max-height: 60px; }
        
        .company-name {
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .doc-meta { width: 100%; border-collapse: collapse; }
        
        .meta-label {
            width: 100px;
            font-size: 11.5px;
            font-weight: 700 !important;
        }
        .meta-sep {
            width: 10px;
            text-align: center;
            font-size: 11.5px;
            font-weight: 700 !important;
        }
        .meta-value {
            font-size: 11.5px;
            font-weight: 400;
        }
        .ac-title,
        .ac-section,
        .ac-inline {
            border: 1px solid #e2e3e5;
            background-color: rgb(215, 215, 215) !important;
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
        }
        .ac-title {
            text-align: center;
            margin: 8px 0;
            padding: 6.4px 8px;
        }
        .ac-section {
            margin-top: 8px;
            padding: 6.4px 8px;
        }
        .ac-inline-row { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .ac-inline-row td { vertical-align: middle; border: 1px solid #e2e3e5; }
        .ac-inline { padding: 6.4px 8px; }
        .ac-inline-fg { width: 50%; }
        .ac-inline-audit {
            width: 50%;
            font-weight: 400;
            text-transform: none;
            font-size: 11.5px;
        }
        .audit-label { font-weight: 700; font-size: 11.5px; text-transform: uppercase; }
        .audit-value { font-weight: 400; font-size: 11.5px; }
        .audit-value * { font-weight: inherit; }
        table.ac-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 6px;
            border: 1px solid #e2e3e5;
        }
        .ac-table th,
        .ac-table td {
            border: 1px solid #e2e3e5;
            padding: 6.4px !important;
            font-size: 11.5px;
            vertical-align: middle;
        }
        .ac-table thead th {
            background-color: rgb(215, 215, 215) !important;
            font-weight: 700;
            text-align: center;
        }
        
        .label-cell {
            width: 36%;
            font-size: 11.5px;
            font-weight: 700 !important;
            background-color: rgb(215, 215, 215) !important;
        }
        .value-cell {
            width: 64%;
            font-size: 11.5px;
            font-weight: 400;
        }
        .ac-footer {
            margin-top: 16px;
            border-top: 1px solid #e2e3e5;
            padding-top: 8px;
        }
        .ac-footer-row { width: 100%; margin-bottom: 2px; border-collapse: collapse; }
        .ac-footer-row td { vertical-align: top; padding: 6.4px 6px 6.4px 0; font-size: 11.5px; }
        .footer-label { font-size: 11.5px; font-weight: 700 !important; white-space: nowrap; padding-right: 6px; }
        .footer-value { font-size: 11.5px; }
        .approved-wrap { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .approved-item { font-size: 11.5px; padding: 2px 12px 2px 0; vertical-align: top; }
       
        table.ac-table-depreciation {
            table-layout: fixed;
        }
        table.ac-table-depreciation th,
        table.ac-table-depreciation td {
            width: 14.2857% !important;
            max-width: 14.2857% !important;
            box-sizing: border-box;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        #watermark {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 200px;
            height: 200px;
            opacity: .1;
        }
        
        .ac-print-status {
            margin-top: 8px;
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
        }
        .ac-print-status h3 {
            margin: 0;
            font-size: 24.5px;
            font-weight: bold;
            font-family: inherit;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div id="watermark"></div>
    {{-- Header: logo + company + doc meta (matches frontend) --}}
    <table class="ac-header">
        <tr>
            <td class="ac-header-logo">
                @if($fixedAssetMaster->company_by->logo_url ?? null)
                    <img class="company-logo" src="{{ $fixedAssetMaster->company_by->logo_url}}" alt="">
                @endif
            </td>
            <td class="ac-header-meta">
                <div class="company-name">{{ $fixedAssetMaster->company_by->CompanyName ?? '-' }}</div>
                <table class="doc-meta">
                    <tr>
                        <td class="meta-label">{{ trans('custom.doc_code') }}</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">{{ $fixedAssetMaster->faCode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">{{ trans('custom.doc_date') }}</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">{{ \App\helper\Helper::dateFormat($fixedAssetMaster->documentDate) ?: '-' }}</td>
                    </tr>
                </table>
                <div class="ac-print-status">
                    <span class="font-weight-bold">
                        <h3 class="text-muted">
                            @if($fixedAssetMaster->confirmedYN == 0 && $fixedAssetMaster->approved == 0)
                                {{ trans('custom.not_confirmed') }}
                            @elseif($fixedAssetMaster->confirmedYN == 1 && $fixedAssetMaster->approved == 0 && (int) ($fixedAssetMaster->refferedBackYN ?? 0) !== -1)
                                {{ trans('custom.pending_approval') }}
                            @elseif($fixedAssetMaster->confirmedYN == 1 && $fixedAssetMaster->approved == 0 && (int) ($fixedAssetMaster->refferedBackYN ?? 0) === -1)
                                {{ trans('custom.referred_back') }}
                            @elseif($fixedAssetMaster->confirmedYN == 1 && ($fixedAssetMaster->approved == 1 || $fixedAssetMaster->approved == -1))
                                {{ trans('custom.fully_approved') }}
                            @endif
                        </h3>
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <div class="ac-title">{{ trans('custom.asset_cost_document') }}</div>

    {{-- Asset information --}}
    <div class="ac-section">{{ trans('custom.asset_information') }}</div>
    <table class="ac-table">
        <tbody>
            <tr>
                <td class="label-cell">{{ trans('custom.asset_code') }}</td>
                <td class="value-cell">{{ $fixedAssetMaster->faCode ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.group_to') }}</td>
                <td class="value-cell">{{ optional($fixedAssetMaster->group_to)->faCode ?? '-' }} - {{ optional($fixedAssetMaster->group_to)->assetDescription ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.description') }}</td>
                <td class="value-cell">{{ $fixedAssetMaster->assetDescription ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.serial_no') }}</td>
                <td class="value-cell">{{ $fixedAssetMaster->faUnitSerialNo ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.manufacture') }}</td>
                <td class="value-cell">{{ $fixedAssetMaster->MANUFACTURE ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.date_aquired') }}</td>
                <td class="value-cell">{{ \App\helper\Helper::dateFormat($fixedAssetMaster->dateAQ) ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.asset_type') }}</td>
                <td class="value-cell">{{ optional($fixedAssetMaster->assettypemaster)->typeDes ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.location') }}</td>
                <td class="value-cell">{{ optional($fixedAssetMaster->location)->locationName ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.last_physical_verified_date') }}</td>
                <td class="value-cell">{{ \App\helper\Helper::dateFormat($fixedAssetMaster->lastVerifiedDate) ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.supplier') }}</td>
                <td class="value-cell">{{ optional($fixedAssetMaster->supplier)->supplierName ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.asset_bar_code') }}</td>
                <td class="value-cell">{{ $fixedAssetMaster->faBarcode ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.comments') }}</td>
                <td class="value-cell">{{ $fixedAssetMaster->COMMENTS ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ trans('custom.asset_status') }}</td>
                <td class="value-cell">{{ $fixedAssetMaster->assetStatus ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Asset classification --}}
    <div class="ac-section">{{ trans('custom.asset_classification') }}</div>
    <table class="ac-table">
        <thead>
            <tr>
                <th>{{ trans('custom.main_category') }}</th>
                <th>{{ trans('custom.sub_category') }}</th>
                <th>{{ trans('custom.sub_category') }} 2</th>
                <th>{{ trans('custom.sub_category') }} 3</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ optional($fixedAssetMaster->category_by)->catDescription ?? '-' }}</td>
                <td class="text-center">{{ optional($fixedAssetMaster->sub_category_by)->catDescription ?? '-' }}</td>
                <td class="text-center">{{ optional($fixedAssetMaster->sub_category_by2)->catDescription ?? '-' }}</td>
                <td class="text-center">{{ optional($fixedAssetMaster->sub_category_by3)->catDescription ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Finance grouping + Audit category row --}}
    <table class="ac-inline-row">
        <tr>
            <td class="ac-inline ac-inline-fg">{{ trans('custom.finance_grouping') }}</td>
            <td class="ac-inline ac-inline-audit">
                <span class="audit-label">{{ trans('custom.audit_category') }} :</span>
                <span class="audit-value">{!! optional($fixedAssetMaster->finance_category)->financeCatDescription ?? '' !!}</span>
            </td>
        </tr>
    </table>

    <table class="ac-table">
        <thead>
            <tr>
                <th>{{ trans('custom.cost_account') }}</th>
                <th>{{ trans('custom.acc_dep_gl_code') }}</th>
                <th>{{ trans('custom.depreciation_gl') }}</th>
                <th>{{ trans('custom.disposal_gl') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ ($fixedAssetMaster->COSTGLCODE ?: '-') }} - {{ ($fixedAssetMaster->COSTGLCODEdes ?: '-') }}</td>
                <td>{{ ($fixedAssetMaster->ACCDEPGLCODE ?: '-') }} - {{ ($fixedAssetMaster->ACCDEPGLCODEdes ?: '-') }}</td>
                <td>{{ ($fixedAssetMaster->DEPGLCODE ?: '-') }} - {{ ($fixedAssetMaster->DEPGLCODEdes ?: '-') }}</td>
                <td>{{ ($fixedAssetMaster->DISPOGLCODE ?: '-') }} - {{ ($fixedAssetMaster->DISPOGLCODEdes ?: '-') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Depreciation details --}}
    <div class="ac-section">{{ trans('custom.depreciation_details') }}</div>
    <table class="ac-table ac-table-depreciation" width="100%">
        <colgroup>
            <col style="width: 14.2857%;">
            <col style="width: 14.2857%;">
            <col style="width: 14.2857%;">
            <col style="width: 14.2857%;">
            <col style="width: 14.2857%;">
            <col style="width: 14.2857%;">
            <col style="width: 14.2857%;">
        </colgroup>
        <thead>
            <tr>
                <th style="width: 14.2857%;">{{ trans('custom.dep_date_start') }}</th>
                <th style="width: 14.2857%;">{{ trans('custom.lifetime') }}</th>
                <th style="width: 14.2857%;">{{ trans('custom.dep_percent_short') }}</th>
                <th style="width: 14.2857%;">{{ trans('custom.unit_price') }}</th>
                <th style="width: 14.2857%;">{{ trans('custom.residual_value') }}</th>
                <th style="width: 14.2857%;">{{ trans('custom.acc_dep_date') }}</th>
                <th style="width: 14.2857%;">{{ trans('custom.acc_dep') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="width: 14.2857%;">{{ \App\helper\Helper::dateFormat($fixedAssetMaster->dateDEP) ?: '-' }}</td>
                <td class="text-center" style="width: 14.2857%;">{{ $fixedAssetMaster->depMonth ?? '-' }}</td>
                <td class="text-center" style="width: 14.2857%;">{{ ($fixedAssetMaster->DEPpercentage === null || $fixedAssetMaster->DEPpercentage === '') ? '-' : number_format((float) $fixedAssetMaster->DEPpercentage, 7, '.', '') }}</td>
                <td class="text-center" style="width: 14.2857%;">{{ ($fixedAssetMaster->COSTUNIT === null || $fixedAssetMaster->COSTUNIT === '') ? '-' : number_format((float) $fixedAssetMaster->COSTUNIT, 3, '.', '') }}</td>
                <td class="text-center" style="width: 14.2857%;">{{ ($fixedAssetMaster->salvage_value === null || $fixedAssetMaster->salvage_value === '') ? '-' : number_format((float) $fixedAssetMaster->salvage_value, 3, '.', '') }}</td>
                <td class="text-center" style="width: 14.2857%;">{{ \App\helper\Helper::dateFormat($fixedAssetMaster->accumulated_depreciation_date) ?: '-' }}</td>
                <td class="text-center" style="width: 14.2857%;">{{ ($fixedAssetMaster->accumulated_depreciation_amount_lcl === null || $fixedAssetMaster->accumulated_depreciation_amount_lcl === '') ? '-' : number_format((float) $fixedAssetMaster->accumulated_depreciation_amount_lcl, 3, '.', '') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="ac-footer">
        <table class="ac-footer-row">
            <tr>
                <td class="footer-label">{{ trans('custom.confirmed_by') }} :</td>
                <td class="footer-value">{{ optional($fixedAssetMaster->confirmed_by)->empFullName ?? '-' }}</td>
            </tr>
            <tr>
                <td class="footer-label">{{ trans('custom.electronically_approved_by') }} :</td>
                <td class="footer-value">@if(count($fixedAssetMaster->approved_by ?? []) === 0)-@endif</td>
            </tr>
        </table>
        @if(count($fixedAssetMaster->approved_by?? []) > 0)
            @foreach($fixedAssetMaster->approved_by as $det)
                <table class="approved-wrap">
                    <tr>
                        <td class="approved-item">
                            <div>{{ $det['employee']['empFullName'] ?? '-' }}</div>
                            <div>{{ \App\helper\Helper::dateFormat($det['approvedDate'] ?? null) ?: '-' }}</div>
                        </td>
                    </tr>
                </table>
            @endforeach
        @endif
    </div>
</body>
</html>
