@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            User Group Assign
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($userGroupAssign, ['route' => ['userGroupAssigns.update', $userGroupAssign->id], 'method' => 'patch']) !!}

                        @include('user_group_assigns.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection