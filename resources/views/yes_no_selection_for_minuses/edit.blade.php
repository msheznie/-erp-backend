@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Yes No Selection For Minus
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($yesNoSelectionForMinus, ['route' => ['yesNoSelectionForMinuses.update', $yesNoSelectionForMinus->id], 'method' => 'patch']) !!}

                        @include('yes_no_selection_for_minuses.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection