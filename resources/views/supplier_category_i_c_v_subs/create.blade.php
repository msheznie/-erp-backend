@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Category I C V Sub
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'supplierCategoryICVSubs.store']) !!}

                        @include('supplier_category_i_c_v_subs.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
