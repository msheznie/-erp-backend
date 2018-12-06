@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Capitalization Referred
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($assetCapitalizationReferred, ['route' => ['assetCapitalizationReferreds.update', $assetCapitalizationReferred->id], 'method' => 'patch']) !!}

                        @include('asset_capitalization_referreds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection