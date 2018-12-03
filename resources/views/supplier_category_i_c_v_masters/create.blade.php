@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Category I C V Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'supplierCategoryICVMasters.store']) !!}

                        @include('supplier_category_i_c_v_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
