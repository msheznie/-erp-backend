@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Accruaval From O P Master
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('accruaval_from_o_p_masters.show_fields')
                    <a href="{!! route('accruavalFromOPMasters.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
