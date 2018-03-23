@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Contact Details
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('supplier_contact_details.show_fields')
                    <a href="{!! route('supplierContactDetails.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
