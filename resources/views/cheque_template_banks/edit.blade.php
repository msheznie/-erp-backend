@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Cheque Template Bank
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($chequeTemplateBank, ['route' => ['chequeTemplateBanks.update', $chequeTemplateBank->id], 'method' => 'patch']) !!}

                        @include('cheque_template_banks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection