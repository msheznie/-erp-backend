@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Credit Note Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($creditNoteDetails, ['route' => ['creditNoteDetails.update', $creditNoteDetails->id], 'method' => 'patch']) !!}

                        @include('credit_note_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection