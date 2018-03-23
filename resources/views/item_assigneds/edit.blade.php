@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Assigned
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($itemAssigned, ['route' => ['itemAssigneds.update', $itemAssigned->id], 'method' => 'patch']) !!}

                        @include('item_assigneds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection