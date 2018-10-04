@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            H R M S Jv Master
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('h_r_m_s_jv_masters.show_fields')
                    <a href="{!! route('hRMSJvMasters.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
