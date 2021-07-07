@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oKareasTable = $('#assignments_table').DataTable({
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
<script>
    function GlobalData () {
            this.updateRoute = <?php echo json_encode( route($updateRoute) ) ?>;
    }

    var oServerData = new GlobalData();
</script>
@endsection

@section('content')
    @section('content_title', $title)
    <div class="row" id="indexAssignmentsApp">
        <div class="col-12">
            <a id="rightnew" href="{{ route($newRoute) }}" class="btn btn-success">
                Nuevo<i class='bx bx-plus'></i>
            </a>
            <div class="row">
                <div class="col-md-12">
                    <table id="assignments_table" class="display stripe hover row-border order-column" style="width:100%">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Área de competencia</th>
                                <th>Fecha de asignación</th>
                                <th>Fecha límite</th>
                                <th>Terminada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lAssignments as $oAssign)
                                <tr>
                                    <td>{{ $oAssign->student }}</td>
                                    <td>{{ $oAssign->ka }}</td>
                                    <td>{{ $oAssign->dt_assignment }}</td>
                                    <td>{{ $oAssign->dt_end }}</td>
                                    <td>{{ $oAssign->is_over ? 'SÍ' : 'NO' }}</td>
                                    <td style="text-align: center">
                                        <button class="btn btn-info" v-on:click="editAssignment('{{ $oAssign->dt_assignment }}', '{{ $oAssign->dt_end }}', '{{ $oAssign->student }}', '{{ $oAssign->id_assignment }}')">
                                            Editar <i class='bx bxs-edit-alt'></i>
                                        </button>
                                        <button class="btn btn-danger">
                                            Borrar <i class='bx bx-x'></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Estudiante</th>
                                <th>Área de competencia</th>
                                <th>Fecha de asignación</th>
                                <th>Fecha límite</th>
                                <th>Terminada</th>
                                <th>Acciones</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @include('mgr.assignments.editmodal')
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/assignments/Assignment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/assignments/VueAssignmentEdit.js') }}"></script>
@endsection