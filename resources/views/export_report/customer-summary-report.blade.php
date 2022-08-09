
<div id="header">
    <div class="row">
        <div class="col-md-12">
            <table>
                <thead>
                    <tr></tr>
                    <tr>
                        <th colspan="22"  style="text-align: center;">
                            Customer Summary Report
                        </th>
                    </tr>
                    <tr>
                        <th colspan="22" style="text-align:center;">
                            {{$companyName}}
                        </th>
                    </tr>
                    <tr></tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">As of </th>
                        <th colspan="2" style="text-align:left;">{{ $fromDate }}</th>
                    </tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">Year </th>
                        <th colspan="2" style="text-align:left;">{{ $year }}</th>

                    </tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">Currency </th>
                        <th colspan="2" style="text-align:left;">{{ $currency }}</th>

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
        <h3>Revenue Summary</h3>
    </div>
</div>
<div >
    <table>
        <thead>
            <tr style="background-color: #d6e6f4">
                <th colspan="2">company</th>
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
                <th colspan="7">total</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($revenueData as $data )
        <tr>
            <td colspan="2" class="text-right">{{ $data->CompanyName}}</td>
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
            <td colspan="7" class="text-right">{{ number_format($data->Total)}}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            @if (!empty($revenueTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="2" class="text-right">
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
                    <td colspan="7" class="text-right">
                            {{isset($revenueTotal['Total']) ? number_format($revenueTotal['Total']) : 0}}
                    </td>
                </tr>
            @endif
        </tfoot>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

<div class="row">
    <div class="col-md-12">
        <h3>Collection Summary</h3>
    </div>
</div>
<div >
    <table>
        <thead>
            <tr style="background-color: #d6e6f4">
                <th colspan="2">company</th>
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
                <th colspan="7">total</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($outputCollection as $data )
        <tr>
            <td colspan="2" class="text-right">{{ $data->CompanyName}}</td>
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
            <td colspan="7" class="text-right">{{ number_format($data->Total)}}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            @if (!empty($collectionTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="2" class="text-right">
                        <b style="text-align: right;">Collection  Total:</b>
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Jan']) ? number_format($collectionTotal['Jan']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Feb']) ? number_format($collectionTotal['Feb']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['March']) ? number_format($collectionTotal['March']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['April']) ? number_format($collectionTotal['April']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['May']) ? number_format($collectionTotal['May']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['June']) ? number_format($collectionTotal['June']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['July']) ? number_format($collectionTotal['July']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Aug']) ? number_format($collectionTotal['Aug']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Sept']) ? number_format($collectionTotal['Sept']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Oct']) ? number_format($collectionTotal['Oct']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Nov']) ? number_format($collectionTotal['Nov']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Dece']) ? number_format($collectionTotal['Dece']) : 0}}
                    </td>
                    <td colspan="7" class="text-right">
                            {{isset($collectionTotal['Total']) ? number_format($collectionTotal['Total']) : 0}}
                    </td>
                </tr>
            @endif
        </tfoot>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>


<div >
    <table>
        <thead>
            <tr style="background-color: #d6e6f4">
                <th colspan="2">company</th>
                <th colspan="5">Segment</th>
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
        @foreach ($outputServiceLine as $data )
        <tr>
            <td colspan="2" class="text-right">{{ $data->CompanyName}}</td>
            <td colspan="5" class="text-right">{{ $data->ServiceLineDes}}</td>
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
            @if (!empty($serviceLineTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="7" class="text-right">
                        <b style="text-align: right;">Revenue Total:</b>
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['Jan']) ? number_format($serviceLineTotal['Jan']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['Feb']) ? number_format($serviceLineTotal['Feb']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['March']) ? number_format($serviceLineTotal['March']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['April']) ? number_format($serviceLineTotal['April']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['May']) ? number_format($serviceLineTotal['May']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['June']) ? number_format($serviceLineTotal['June']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['July']) ? number_format($serviceLineTotal['July']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['Aug']) ? number_format($serviceLineTotal['Aug']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['Sept']) ? number_format($serviceLineTotal['Sept']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['Oct']) ? number_format($serviceLineTotal['Oct']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['Nov']) ? number_format($serviceLineTotal['Nov']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($serviceLineTotal['Dece']) ? number_format($serviceLineTotal['Dece']) : 0}}
                    </td>
                    <td colspan="2" class="text-right">
                            {{isset($serviceLineTotal['Total']) ? number_format($serviceLineTotal['Total']) : 0}}
                    </td>
                </tr>
            @endif
            @if (!empty($collectionTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="7" class="text-right">
                        <b style="text-align: right;">Collection  Total:</b>
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Jan']) ? number_format($collectionTotal['Jan']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Feb']) ? number_format($collectionTotal['Feb']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['March']) ? number_format($collectionTotal['March']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['April']) ? number_format($collectionTotal['April']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['May']) ? number_format($collectionTotal['May']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['June']) ? number_format($collectionTotal['June']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['July']) ? number_format($collectionTotal['July']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Aug']) ? number_format($collectionTotal['Aug']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Sept']) ? number_format($collectionTotal['Sept']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Oct']) ? number_format($collectionTotal['Oct']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Nov']) ? number_format($collectionTotal['Nov']) : 0}}
                    </td>
                    <td colspan="1" class="text-right">
                            {{isset($collectionTotal['Dece']) ? number_format($collectionTotal['Dece']) : 0}}
                    </td>
                    <td colspan="2" class="text-right">
                            {{isset($collectionTotal['Total']) ? number_format($collectionTotal['Total']) : 0}}
                    </td>
                </tr>
            @endif
        </tfoot>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>
