@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Finance Category
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'assetFinanceCategories.store']) !!}

                        @include('asset_finance_categories.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
