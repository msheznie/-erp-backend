<html>
<table>
        <thead>
        <div>
                <td colspan="18"><h1 style="text-align: center">{{$company}} </h1></td>
            </div>
        </thead>
    </table>
    <table>
        <thead>
        <div>
                <td colspan="18"><h2 style="text-align: center">{{$Title}}</h2></td>
            </div>
        </thead>
    </table>
    <br>
<table>
    <thead>
    <tr>
        <td><B>{{ __('custom.period_from') }}: </B></td>
        <td><B>{{ date('d/m/Y', strtotime($fromDate)) }}</B></td>
        <td><B>{{ __('custom.period_to') }}:</B></td>
        <td><B>{{ date('d/m/Y', strtotime($toDate)) }}</B></td>
        <td><B>{{ __('custom.currency') }}:</B></td>
        <td><B>{{ $currency }}</B></td>
    </tr>
    </thead>
</table>


<table>
    <thead> 
        <tr>
            <th colspan="12" ></th>
            <th  colspan="{{$length}}" class="text-center">{{ __('custom.segments') }}</th>
            <th rowspan="2" ></th>
        </tr>
        <tr>
            <th colspan="2" class="border_rem text-center">{{ __('custom.account_code') }}</th>
            <th colspan="3" class="border_rem text-center">{{ __('custom.account_description') }}</th>
            @foreach($segment as $data1)

                <th colspan="3" >
                {{$data1}}
                </th>
             @endforeach
        </tr>   
        <tr>
            <th colspan="2" class=""></th>
            <th colspan="3" class=""></th>
            @foreach($segment as $data1)
             @foreach($deb_cred as $info)
                 <th>
                    {{$info}}
                </th>
             @endforeach
            @endforeach

        </tr>


    </thead>

    <tbody>
        @foreach($data as $dt)
            <tr>
            <td colspan="2">
                {{$dt['AccountCode']}}
            </td>
            <td colspan="3">
                {{$dt['AccountDescription']}}
            </td>
               @foreach($segment as $key=>$val)
                    <td>{{$dt[$key]['debit']}}</td>
                    <td>{{$dt[$key]['credit']}}</td>
                    <td>{{$dt[$key]['total']}}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>

    <tfoot>
    <tfoot>
        <tr >
                <td colspan="5" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"><b> {{ __('custom.total') }}:</b></td>
                    @foreach($segment as $key=>$val)
                        <td>
                        @php
                            echo App\Http\Controllers\API\GeneralLedgerAPIController::getToal($data,$key,'debit');
                        @endphp
                        </td>
                        <td>
                        @php
                            echo App\Http\Controllers\API\GeneralLedgerAPIController::getToal($data,$key,'credit');
                        @endphp
                        </td>
                        <td>
                        @php
                            echo App\Http\Controllers\API\GeneralLedgerAPIController::getToal($data,$key,'total');
                        @endphp
                        </td>
                    @endforeach

        </tr>
        </tfoot>
    <tfoot>
</table>

</html>