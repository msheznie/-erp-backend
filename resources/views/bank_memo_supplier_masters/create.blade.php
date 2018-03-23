@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Memo Supplier Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'bankMemoSupplierMasters.store']) !!}

                        @include('bank_memo_supplier_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
