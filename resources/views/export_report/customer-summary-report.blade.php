
<div id="header">
    <div class="row">
        <div class="col-md-12">
            <table>
                <thead>
                    <tr></tr>
                    <tr>
                        <th colspan="17"  style="text-align: center;">
                            Customer Summary Report
                        </th>
                    </tr>
                    <tr>
                        <th colspan="17" style="text-align:center;">
                            {{$companyName}}
                        </th>
                    </tr>
                    <tr></tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">As of </th>
                        <th colspan="1" style="text-align:left;">{{ $fromDate }}</th>
                    </tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">Year </th>
                        <th colspan="1" style="text-align:left;">{{ $year }}</th>

                    </tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">Currency </th>
                        <th colspan="1" style="text-align:left;">{{ $currency }}</th>

                    </tr>
                    <tr></tr>
                    <tr></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h5>Revenue Summary</h5>
    </div>
</div>
<div >
    <div>
        <div >
            <table>
                <thead>
                    <tr style="background-color: #d6e6f4">
                        <th colspan="3">company</th>
                        <th colspan="1">jan</th>
                        <th colspan="1">feb</th>
                        <th colspan="1">mar</th>
                        <th colspan="1">apr</th>
                        <th colspan="1">may</th>
                        <th colspan="1">jun</th>
                        <th colspan="1">jul</th>
                        <th colspan="1">aug</th>
                        <th colspan="1">sep</th>
                        <th colspan="1">oct</th>
                        <th colspan="1">nov</th>
                        <th colspan="1">dec</th>
                        <th colspan="2">total</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($revenueData as $data )
                <tr>
                    <td colspan="3" class="text-right">{{ $data->CompanyName}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->Jan)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->Feb)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->March)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->April)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->May)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->June)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->July)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->Aug)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->Sept)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->Oct)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->Nov)}}</td>
                    <td colspan="1" class="text-right">{{ number_format($data->Dece)}}</td>
                    <td colspan="2" class="text-right">{{ number_format($data->Total)}}</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot>
                    @if (!empty($revenueTotal) )
                        <tr style="background-color: #d6e6f4;">
                            <td  colspan="3" class="text-right">
                                <b style="text-align: right;">Revenue Total:</b>
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['Jan']) ? number_format($revenueTotal['Jan']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['Feb']) ? number_format($revenueTotal['Feb']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['March']) ? number_format($revenueTotal['March']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['April']) ? number_format($revenueTotal['April']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['May']) ? number_format($revenueTotal['May']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['June']) ? number_format($revenueTotal['June']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['July']) ? number_format($revenueTotal['July']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['Aug']) ? number_format($revenueTotal['Aug']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['Sept']) ? number_format($revenueTotal['Sept']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['Oct']) ? number_format($revenueTotal['Oct']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['Nov']) ? number_format($revenueTotal['Nov']) : 0}}
                            </td>
                            <td colspan="1" class="text-right">
                                    {{isset($revenueTotal['Dece']) ? number_format($revenueTotal['Dece']) : 0}}
                            </td>
                            <td colspan="2" class="text-right">
                                    {{isset($revenueTotal['Total']) ? number_format($revenueTotal['Total']) : 0}}
                            </td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
    {{-- <div class="col-md-12" *ngIf="revenueData?.length == 0">
        <div class="alert alert-warning" role="alert">
            No records found
        </div>
    </div> --}}
</div>