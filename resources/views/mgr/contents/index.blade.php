@extends('layouts.appuni')

@section('scripts_section')
    <script type="text/javascript">
        $(document).ready(function() {
            var oKareasTable = $('#media_table').DataTable({
                "language": {
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix":    "",
                    "sSearch":         "Buscar:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                "colReorder": true,
                "responsive": true,
                "columnDefs": [
                    { responsivePriority: 1, targets: 2 }
                ],
                "dom": 'Bfrtip',
                "lengthMenu": [
                    [ 10, 25, 50, 100, -1 ],
                    [ 'Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100', 'Mostrar todo' ]
                ],
                "buttons": [
                        'pageLength',
                        {
                            extend: 'copy',
                            text: 'Copiar'
                        }, 
                        'csv', 
                        'excel', 
                        {
                            extend: 'print',
                            text: 'Imprimir'
                        }
                    ]
            });
        } );
    </script>
    <script type="text/javascript">
        function GlobalData () {
            this.sGetRoute = <?php echo json_encode( route($sGetRoute) ) ?>;
        }

        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    @section('content_title', $title)
<div id="contentsApp">
    <a id="rightnew" href="{{ route($newRoute) }}" class="btn btn-success">
        Nuevo<i class='bx bx-plus'></i>
    </a>
    <div class="row">
        <div class="col-md-12">
            <table id="media_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre de archivo</th>
                        <th>Tipo de archivo</th>
                        <th>Vista previa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lContents as $content)
                        <tr>
                            <td>{{ $content->file_name }}</td>
                            <td>{{ $content->file_type }}</td>
                            <td style="text-align: center">
                                @if($content->file_type != 'link')
                                    <button class="btn btn-info" v-on:click="preview('{{ $content->id_content  }}', '{{ $content->file_type  }}', '{{ $content->file_name  }}')">
                                        Ver <i class='bx bxs-show'></i>
                                    </button>
                                @else
                                    <a class="btn btn-info" target="_blank" href="{{ $content->file_path }}">
                                        Ver <i class='bx bxs-show'></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Nombre de archivo</th>
                        <th>Tipo de archivo</th>
                        <th>Vista previa</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @include('mgr.contents.preview')
</div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/contents/VueContents.js') }}"></script>
@endsection