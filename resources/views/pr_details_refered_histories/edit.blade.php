@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Pr Details Refered History
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($prDetailsReferedHistory, ['route' => ['prDetailsReferedHistories.update', $prDetailsReferedHistory->id], 'method' => 'patch']) !!}

                        @include('pr_details_refered_histories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection