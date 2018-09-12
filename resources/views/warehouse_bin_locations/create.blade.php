@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Warehouse Bin Location
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'warehouseBinLocations.store']) !!}

                        @include('warehouse_bin_locations.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
