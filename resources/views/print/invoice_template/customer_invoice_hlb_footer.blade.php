<div id="footer">
    <div class="" style="margin-top: 10px">
        <table style="width: 100%; text-align: right; ">
            <tr>
                <td>
                    -----------------------------
                </td>
            </tr>
            <tr>
                <td>
                    <span class="font-weight-bold">Authorized Signatory</span>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <table style="width: 100%; text-align: left; ">
        <tr>
            <td>
                <b>{{$request->company->CompanyURL}}</b>
            </td>
        </tr>
         <tr>
            <td>
                {{$request->CompanyAddress}}
            </td>
        </tr>
        <tr>
            <td>
                <b>TEL : </b> {{$request->CompanyTelephone}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>FAX : </b>{{$request->CompanyFax}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>EMAIL : </b>{{$request->company->CompanyEmail}}
            </td>
        </tr>
    </table>
</div>
