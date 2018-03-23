@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Yes No Selection
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($yesNoSelection, ['route' => ['yesNoSelections.update', $yesNoSelection->id], 'method' => 'patch']) !!}

                        @include('yes_no_selections.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection