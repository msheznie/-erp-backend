@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($assetType, ['route' => ['assetTypes.update', $assetType->id], 'method' => 'patch']) !!}

                        @include('asset_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection