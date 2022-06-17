@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Third Party Integration Keys
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($thirdPartyIntegrationKeys, ['route' => ['thirdPartyIntegrationKeys.update', $thirdPartyIntegrationKeys->id], 'method' => 'patch']) !!}

                        @include('third_party_integration_keys.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection