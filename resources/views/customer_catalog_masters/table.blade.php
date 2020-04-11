<div class="table-responsive">
    <table class="table" id="customerCatalogMasters-table">
        <thead>
            <tr>
                <th>Catalogid</th>
        <th>Catalogname</th>
        <th>Fromdate</th>
        <th>Todate</th>
        <th>Customerid</th>
        <th>Companysystemid</th>
        <th>Documentsystemid</th>
        <th>Createdby</th>
        <th>Createddate</th>
        <th>Modifiedby</th>
        <th>Modifieddate</th>
        <th>Isdelete</th>
        <th>Isactive</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($customerCatalogMasters as $customerCatalogMaster)
            <tr>
                <td>{!! $customerCatalogMaster->catalogID !!}</td>
            <td>{!! $customerCatalogMaster->catalogName !!}</td>
            <td>{!! $customerCatalogMaster->fromDate !!}</td>
            <td>{!! $customerCatalogMaster->toDate !!}</td>
            <td>{!! $customerCatalogMaster->customerID !!}</td>
            <td>{!! $customerCatalogMaster->companySystemID !!}</td>
            <td>{!! $customerCatalogMaster->documentSystemID !!}</td>
            <td>{!! $customerCatalogMaster->createdBy !!}</td>
            <td>{!! $customerCatalogMaster->createdDate !!}</td>
            <td>{!! $customerCatalogMaster->modifiedBy !!}</td>
            <td>{!! $customerCatalogMaster->modifiedDate !!}</td>
            <td>{!! $customerCatalogMaster->isDelete !!}</td>
            <td>{!! $customerCatalogMaster->isActive !!}</td>
                <td>
                    {!! Form::open(['route' => ['customerCatalogMasters.destroy', $customerCatalogMaster->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('customerCatalogMasters.show', [$customerCatalogMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('customerCatalogMasters.edit', [$customerCatalogMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
