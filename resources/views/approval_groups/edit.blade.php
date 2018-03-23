@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Approval Groups
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($approvalGroups, ['route' => ['approvalGroups.update', $approvalGroups->id], 'method' => 'patch']) !!}

                        @include('approval_groups.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection