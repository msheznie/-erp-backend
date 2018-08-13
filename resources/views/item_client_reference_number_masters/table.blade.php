<table class="table table-responsive" id="itemClientReferenceNumberMasters-table">
    <thead>
        <tr>
            <th>Iditemassigned</th>
        <th>Itemsystemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Customerid</th>
        <th>Contractuiid</th>
        <th>Contractid</th>
        <th>Clientreferencenumber</th>
        <th>Createdbyuserid</th>
        <th>Createddatetime</th>
        <th>Modifiedbyuserid</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemClientReferenceNumberMasters as $itemClientReferenceNumberMaster)
        <tr>
            <td>{!! $itemClientReferenceNumberMaster->idItemAssigned !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->itemSystemCode !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->itemPrimaryCode !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->itemDescription !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->unitOfMeasure !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->companySystemID !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->companyID !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->customerID !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->contractUIID !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->contractID !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->clientReferenceNumber !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->createdByUserID !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->createdDateTime !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->modifiedByUserID !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->modifiedDateTime !!}</td>
            <td>{!! $itemClientReferenceNumberMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemClientReferenceNumberMasters.destroy', $itemClientReferenceNumberMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemClientReferenceNumberMasters.show', [$itemClientReferenceNumberMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemClientReferenceNumberMasters.edit', [$itemClientReferenceNumberMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>