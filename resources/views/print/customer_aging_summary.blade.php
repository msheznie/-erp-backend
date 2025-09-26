<style type="text/css">
    @if(isset($lang) && $lang === 'ar')
    body {
        direction: rtl;
        text-align: right;
    }

    .text-left {
        text-align: right !important;
    }

    .text-right {
        text-align: left !important;
    }

    table {
        direction: rtl;
    }

    .table th, .table td {
        text-align: right;
    }
    @endif

    body {
        font-size: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
    }

    h3 {
        font-size: 1.53125rem;
    }

    h6 {
        font-size: 0.875rem;
    }

    h6, h3 {
        margin-bottom: 0.1rem;
        font-weight: 500;
        line-height: 1.2;
        color: inherit;
    }

    table > tbody > th > tr > td {
        font-size: 10px;
    }

    table > thead > th {
        font-size: 10px;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    table {
        border-collapse: collapse;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .table th, .table td {
        padding: 0.4rem !important;
        vertical-align: top;
        border: 1px solid #dee2e6 !important;
        /* border-bottom: 1px solid rgb(127, 127, 127) !important;*/
    }

    .table th {
        background-color: #D7E4BD !important;
    }

    tfoot > tr > td {
        border: 1px solid rgb(127, 127, 127);
    }

    .text-right {
        text-align: right !important;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    hr {
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    th {
        text-align: inherit;
        font-weight: bold;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .white-space-pre-line {
        white-space: pre-line;
    }

    p {
        margin-top: 0 !important;
    }

    .content {
        margin-bottom: 45px;
    }

</style>
<div class="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        <?php $ageRangeGrandTotal = [] ?>
        {{ $grandTotal = 0 }}
        @foreach ($reportData as $key => $val)
            <tr>
                <td colspan="9"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <th width="10%">{{trans('custom.customer_code')}}</th>
                <th width="20%">{{trans('custom.customer_name')}}</th>
                <th width="7%">{{trans('custom.credit_days')}}</th>
                <th width="10%">{{trans('custom.currency')}}</th>
                <th width="10%">{{trans('custom.amount')}}</th>
                @foreach ($agingRange as $age)
                    <th width="10%">{{$age}}</th>
                @endforeach
            </tr>
            <tbody>
            {{ $ageTotal = 0 }}
            {{ $ageSubTotal = 0 }}
            @foreach ($val as $det)
                <?php $ageRangeSubTotal = [] ?>
                @foreach ($det as $det2)
                    {{ $ageTotal = 0 }}
                    @foreach ($agingRange as $age)
                        {{ $ageTotal += $det2->$age }}
                        {{$ageRangeSubTotal[$age][] = $det2->$age}}
                        {{$ageRangeGrandTotal[$age][] = $det2->$age}}
                    @endforeach
                    <tr>
                        <td>{{ $det2->CustomerCode }}</td>
                        <td>{{ $det2->CustomerName }}</td>
                        <td>{{ $det2->creditDays }}</td>
                        <td>{{ $det2->documentCurrency }}</td>
                        <td style="text-align: right">{{ number_format($ageTotal) }}</td>
                        @foreach ($agingRange as $age)
                            <td style="text-align: right">{{ number_format($det2->$age) }}</td>
                        @endforeach
                    </tr>
                    {{$ageSubTotal += $ageTotal}}
                @endforeach
                <tr>
                    <td colspan="3" style="border-bottom: none; border-left: none;"
                        class="text-right"><b>{{trans('custom.sub_total')}}:</b></td>

                     
                    <td style="text-align: left">
                        @foreach ($det as $det2)
                            @if ($det2 === reset($det))
                                <b>{{ $det2->documentCurrency}}</b>
                            @endif
                        @endforeach
                    </td>

                    <td style="text-align: right">
                        <b>{{ number_format($ageSubTotal) }}</b></td>
                    @foreach ($agingRange as $age)
                        <td style="text-align: right">
                            <b>{{ number_format(array_sum($ageRangeSubTotal[$age])) }}</b>
                        </td>
                    @endforeach
                </tr>
                {{$grandTotal += $ageSubTotal}}
            @endforeach
            </tbody>
        @endforeach
        <tfoot>
        <tr>
            <td colspan="3" style="border-bottom: none; border-left: none;"
                class="text-right"><b>{{trans('custom.grand_total')}}:</b></td>
            <td style="text-align: left">
                <b>N/A</b>
            </td>
            <td style="text-align: right"><b>{{ number_format($grandTotal) }}</b></td>
            @foreach ($agingRange as $age)
                <td style="text-align: right">
                    <b>{{ number_format(array_sum($ageRangeGrandTotal[$age])) }}</b></td>
            @endforeach
        </tr>
        </tfoot>
    </table>
</div>
