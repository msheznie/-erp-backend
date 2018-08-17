@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Debit Note
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($debitNote, ['route' => ['debitNotes.update', $debitNote->id], 'method' => 'patch']) !!}

                        @include('debit_notes.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection