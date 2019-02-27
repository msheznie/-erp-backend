@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Master Category
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('customer_master_categories.show_fields')
                    <a href="{!! route('customerMasterCategories.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
