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
            "responsive": true,
            "columnDefs": [
                    { responsivePriority: 1, targets: 7 }
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
<script>
    function GlobalData () {
            this.updateRoute = <?php echo json_encode( $updateCourse ) ?>;
            this.deleteRoute = <?php echo json_encode( "" ) ?>;
    }

    var oServerData = new GlobalData();
</script>
@endsection

@section('content')
    @section('content_title', 'Cursos')
    <div class="row" id="indexAssignmentsApp">
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <table id="assignments_table" class="display stripe hover row-border order-column" style="width:100%">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Curso</th>
                                <th>Fecha de apertura</th>
                                <th>Fecha límite</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lCourses as $course)
                                <tr>
                                    <td>{{ $course->student }}</td>
                                    <td>{{ $course->course }}</td>
                                    <td>{{ $course->dt_open }}</td>
                                    <td>{{ $course->dt_close }}</td>
                                    <td style="text-align: center">
                                        <button class="btn btn-info" v-on:click="editAssignment('{{ $course->dt_open }}', '{{ $course->dt_close }}', '{{ $course->student }}', '{{ $course->id_course_control }}')">
                                            Editar <i class='bx bxs-edit-alt'></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Estudiante</th>
                                <th>Curso</th>
                                <th>Fecha de apertura</th>
                                <th>Fecha límite</th>
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