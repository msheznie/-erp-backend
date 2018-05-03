@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Users Log History
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($usersLogHistory, ['route' => ['usersLogHistories.update', $usersLogHistory->id], 'method' => 'patch']) !!}

                        @include('users_log_histories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection