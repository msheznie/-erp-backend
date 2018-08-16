@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Debit Note Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($debitNoteDetails, ['route' => ['debitNoteDetails.update', $debitNoteDetails->id], 'method' => 'patch']) !!}

                        @include('debit_note_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection