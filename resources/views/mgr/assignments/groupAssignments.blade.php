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
            // "columnDefs": [
            //         { responsivePriority: 1, targets: 4 }
            //     ],
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
    $(function() {
        $('input[name="daterange"]').daterangepicker({
        opens: 'left'
        }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
</script>
<script>
    function GlobalData () {
        this.getModulesRoute = <?php echo json_encode(route('assignmentsGroup.getModules')); ?>;
        this.updateRoute = <?php echo json_encode(route('assignmentsGroup.update')); ?>;
        this.deleteRoute = <?php echo json_encode(route('assignmentsGroup.delete')); ?>;
    }

    var oServerData = new GlobalData();
</script>
@endsection

@section('content')
    @section('content_title', 'Asignaciones')
    <div class="row" id="groupAssignmentsApp">
        <div class="col-12">
            <form action="{{ route('assignmentsGroup.index') }}">
                <div class="row justify-content-end">
                    <div class="col-3" style="text-align: right;">
                        <label class="form-label" for="daterang">Filtrar por fecha asignación:</label>
                    </div>
                    <div class="col-8 col-md-3">
                        <input size="24" class="form-control" type="text" name="daterange" value="{{ $daterange }}" />
                    </div>
                    <div class="col-1">
                        <button class="btn btn-primary btn-sm" type="submit"><i class='bx bx-search-alt bx-sm'></i></button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-12">
                    <table id="assignments_table" class="display stripe hover row-border order-column" style="width:100%">
                        <thead>
                            <tr>
                                <th>Fecha asignación</th>
                                <th>Fecha fin</th>
                                <th>Cuadrante</th>
                                <th>Medio asignación</th>
                                <th>Nombre</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lAssignments as $assign)
                                <tr>
                                    <td>{{$assign->dt_assignment}}</td>
                                    <td>{{$assign->dt_end}}</td>
                                    <td>{{$assign->karea}}</td>
                                    <td>{{$assign->type}}</td>
                                    <td>{{$assign->name}}</td>
                                    <td>
                                        <a href="#" v-on:click="editDate('{{$assign->dt_assignment}}', '{{$assign->dt_end}}', {{$assign->id_control}}, {{$assign->knowledge_area_id}}, '{{$assign->karea}}', '{{$assign->type}}', '{{$assign->name}}')" style="display: inline;">
                                            <i class='bx bxs-calendar'></i>
                                        </a>
                                        <a href="#" v-on:click="deleteAssign({{$assign->id_control}}, '{{$assign->karea}}', '{{$assign->type}}', '{{$assign->name}}', '{{$assign->dt_assignment}}', '{{$assign->dt_end}}')">
                                            <i class='bx bxs-trash' style="color: red;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Fecha asignación</th>
                                <th>Fecha fin</th>
                                <th>Cuadrante</th>
                                <th>Medio asignacion</th>
                                <th>Nombre</th>
                                <th>-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @include('mgr.assignments.editDatesModal')
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/assignments/VueGroupAssignment.js') }}"></script>
@endsection