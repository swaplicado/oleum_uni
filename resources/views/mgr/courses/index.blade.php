@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oKareasTable = $('#courses_table').DataTable({
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
                    { responsivePriority: 1, targets: [8, 9] }
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
@endsection

@section('content')
    @section('content_title', $title)
    <a id="rightnew" href="{{ route($newRoute, $moduleId) }}" class="btn btn-success">
        Nuevo<i class='bx bx-plus'></i>
    </a>
    <div class="row" id="divPrerrequisites">
        <div class="col-md-12">
            <table id="courses_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Clave</th>
                        <th>Curso</th>
                        <th>Duración</th>
                        <th>Puntos</th>
                        <th>Descripción</th>
                        <th>Secuencia</th>
                        <th>Estatus</th>
                        <th>Módulo</th>
                        <th>Temas</th>
                        <th>Pre</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lCourses as $course)
                        <tr>
                            <td>{{ $course->course_key }}</td>
                            <td>{{ $course->course }}</td>
                            <td>{{ $course->completion_days }}</td>
                            <td>{{ $course->university_points }}</td>
                            <td>{{ $course->description }}</td>
                            <td>{{ $course->seq_code }}</td>
                            <td>{{ $course->status_code }}</td>
                            <td>{{ $course->module }}</td>
                            <td style="text-align: center">
                                <a href="{{ route('topics.index', ['course' => $course->id_course]) }}">
                                    <i class='bx bxs-category'></i>
                                </a>
                            </td>
                            <td style="text-align: center">
                                <a href="#" v-on:click="showPreviousModal({{ config('csys.elem_type.COURSE') }}, {{ $course->id_course }}, '{{ $course->course }}')">
                                    <i class='bx bxs-brightness'></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Clave</th>
                        <th>Curso</th>
                        <th>Duración</th>
                        <th>Puntos</th>
                        <th>Descripción</th>
                        <th>Secuencia</th>
                        <th>Estatus</th>
                        <th>Módulo</th>
                        <th>Temas</th>
                        <th>Pre</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @include('mgr.prerequisites_modal')
    </div>
@endsection