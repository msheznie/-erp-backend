@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            H R M S Jv Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($hRMSJvMaster, ['route' => ['hRMSJvMasters.update', $hRMSJvMaster->id], 'method' => 'patch']) !!}

                        @include('h_r_m_s_jv_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection