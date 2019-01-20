@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Template Cash Bank
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($reportTemplateCashBank, ['route' => ['reportTemplateCashBanks.update', $reportTemplateCashBank->id], 'method' => 'patch']) !!}

                        @include('report_template_cash_banks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection