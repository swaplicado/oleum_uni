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

    <form id="createForm" method="POST" action="{{ route($storeRoute) }}" enctype="multipart/form-data">
        @method('POST')
        @include('mgr.carousel.form')
        @include('layouts.crud.create-btns')
    </form>
@endsection