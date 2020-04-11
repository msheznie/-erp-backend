<div class="table-responsive">
    <table class="table" id="customerCatalogDetails-table">
        <thead>
            <tr>
                <th>Customercatalogmasterid</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemunitofmeasure</th>
        <th>Partno</th>
        <th>Localcurrencyid</th>
        <th>Localprice</th>
        <th>Reportingcurrencyid</th>
        <th>Reportingprice</th>
        <th>Leadtime</th>
        <th>Isdelete</th>
        <th>Timstamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($customerCatalogDetails as $customerCatalogDetail)
            <tr>
                <td>{!! $customerCatalogDetail->customerCatalogMasterID !!}</td>
            <td>{!! $customerCatalogDetail->itemCodeSystem !!}</td>
            <td>{!! $customerCatalogDetail->itemPrimaryCode !!}</td>
            <td>{!! $customerCatalogDetail->itemDescription !!}</td>
            <td>{!! $customerCatalogDetail->itemUnitOfMeasure !!}</td>
            <td>{!! $customerCatalogDetail->partNo !!}</td>
            <td>{!! $customerCatalogDetail->localCurrencyID !!}</td>
            <td>{!! $customerCatalogDetail->localPrice !!}</td>
            <td>{!! $customerCatalogDetail->reportingCurrencyID !!}</td>
            <td>{!! $customerCatalogDetail->reportingPrice !!}</td>
            <td>{!! $customerCatalogDetail->leadTime !!}</td>
            <td>{!! $customerCatalogDetail->isDelete !!}</td>
            <td>{!! $customerCatalogDetail->timstamp !!}</td>
                <td>
                    {!! Form::open(['route' => ['customerCatalogDetails.destroy', $customerCatalogDetail->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('customerCatalogDetails.show', [$customerCatalogDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('customerCatalogDetails.edit', [$customerCatalogDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
