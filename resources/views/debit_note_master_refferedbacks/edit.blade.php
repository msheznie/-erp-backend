@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Debit Note Master Refferedback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($debitNoteMasterRefferedback, ['route' => ['debitNoteMasterRefferedbacks.update', $debitNoteMasterRefferedback->id], 'method' => 'patch']) !!}

                        @include('debit_note_master_refferedbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection