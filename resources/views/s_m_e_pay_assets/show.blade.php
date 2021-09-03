@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            S M E Pay Asset
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('s_m_e_pay_assets.show_fields')
                    <a href="{{ route('sMEPayAssets.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
