@extends('layouts.appuni')

@section('scripts_section')
    <script>
        $(document).ready(function() {
            $('.select2class').select2();
        });
    </script>
@endsection

@section('content')
    @section('content_title', ($movClass == 'mov_in' ? 'Entrada' : 'Salida').' premio')

    <form id="createForm" method="POST" action="{{ route($storeRoute) }}">
        @method('POST')
        @include('mgr.gifts.formstk')
        @include('layouts.crud.create-btns')
    </form>
@endsection