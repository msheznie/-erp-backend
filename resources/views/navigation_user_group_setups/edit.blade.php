@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Navigation User Group Setup
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($navigationUserGroupSetup, ['route' => ['navigationUserGroupSetups.update', $navigationUserGroupSetup->id], 'method' => 'patch']) !!}

                        @include('navigation_user_group_setups.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection