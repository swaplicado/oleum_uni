@extends('layouts.appuni')

@section('scripts_section')
<script>
    $(document).ready(function() {
        $('.select2class').select2();
    });
</script>
@if(session('message'))
    <script>
        msg = "<?php echo session('message'); ?>";
        icon = "<?php echo session('icon'); ?>"

        SGui.showMessage('Realizado', msg, icon);
    </script>
@endif
@endsection
@section('content')
    @section('content_title', 'Crear área funcional')
    <div>
        <form action="{{route('areasAdm.store')}}" id="createForm" method="POST">
            @include('adm.areas.formArea')
        </form>
        <br>
    </div>
@endsection

@section('bottom_scripts')
@endsection