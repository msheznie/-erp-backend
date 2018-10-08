@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Cost
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'fixedAssetCosts.store']) !!}

                        @include('fixed_asset_costs.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
