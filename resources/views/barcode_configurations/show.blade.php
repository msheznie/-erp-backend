@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Barcode Configuration
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('barcode_configurations.show_fields')
                    <a href="{{ route('barcodeConfigurations.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
