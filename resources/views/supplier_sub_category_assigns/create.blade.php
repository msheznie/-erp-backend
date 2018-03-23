@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Sub Category Assign
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'supplierSubCategoryAssigns.store']) !!}

                        @include('supplier_sub_category_assigns.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
