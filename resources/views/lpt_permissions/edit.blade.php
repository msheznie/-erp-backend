@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Lpt Permission
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($lptPermission, ['route' => ['lptPermissions.update', $lptPermission->id], 'method' => 'patch']) !!}

                        @include('lpt_permissions.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection