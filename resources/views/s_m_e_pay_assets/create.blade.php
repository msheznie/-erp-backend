@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            S M E Pay Asset
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'sMEPayAssets.store']) !!}

                        @include('s_m_e_pay_assets.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
