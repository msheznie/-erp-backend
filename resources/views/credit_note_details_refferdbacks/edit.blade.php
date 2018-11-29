@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Credit Note Details Refferdback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($creditNoteDetailsRefferdback, ['route' => ['creditNoteDetailsRefferdbacks.update', $creditNoteDetailsRefferdback->id], 'method' => 'patch']) !!}

                        @include('credit_note_details_refferdbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection