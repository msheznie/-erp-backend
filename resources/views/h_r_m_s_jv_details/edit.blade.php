@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            H R M S Jv Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($hRMSJvDetails, ['route' => ['hRMSJvDetails.update', $hRMSJvDetails->id], 'method' => 'patch']) !!}

                        @include('h_r_m_s_jv_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection