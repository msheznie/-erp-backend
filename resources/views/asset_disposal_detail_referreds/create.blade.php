@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Disposal Detail Referred
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'assetDisposalDetailReferreds.store']) !!}

                        @include('asset_disposal_detail_referreds.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
