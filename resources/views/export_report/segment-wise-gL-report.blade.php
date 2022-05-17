<html>

<table>
    <thead> 
        <tr>
            <th colspan="10" ></th>
            <th  colspan="{{$length}}" class="text-center">Segments</th>
            <th rowspan="2" ></th>
        </tr>
        <tr>
            <th class=""></th>
            <th class="border_rem text-center">Particulars</th>
            <th class=""></th>
            @foreach($segment as $data1)

                <th colspan="3" >
                {{$data1}}
                </th>
             @endforeach
        </tr>   

        <tr>
            <th class=""></th>
            <th class=""></th>
            <th class=""></th>
            @foreach($segment as $data1)
             @foreach($deb_cred as $info)
                 <th >
                    {{$info}}
                </th>
             @endforeach
         
            @endforeach

        </tr>


    </thead>
    <tbody>
        @foreach($data as $dt)
            <tr>
            <td colspan="3">
                {{$dt['glAccountId']}}
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
                <td colspan="3" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"><b> Total:</b></td>
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