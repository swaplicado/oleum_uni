@extends('layouts.appuni')

@section('scripts_section')
    <script>
        $(document).ready(function() {
            $('.select2class').select2();
        });
    </script>
@endsection

@section('content')
    @section('content_title', $title)

    <form id="createForm" method="POST" action="{{ route($updateRoute, $oModule->id_module) }}">
        @method('PUT')
        @include('mgr.modules.form')
        @include('layouts.crud.create-btns')
    </form>
@endsection