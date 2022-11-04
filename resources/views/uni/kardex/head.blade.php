@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oHeadTable = $('#head_table').DataTable({
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
                    { responsivePriority: 1, targets: 9 }
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
    @section('content_title', 'Avance general')

    <div class="row">
        <div class="col-12">
            <table id="head_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Num</th>
                        <th>Estudiante</th>
                        @if ($withFunctionalArea)
                            <th>Área</th>
                        @else
                            <th>Dept</th>
                        @endif
                        <th>Promedio general</th>
                        <th>Comp</th>
                        <th>Aprob</th>
                        <th>Comp act</th>
                        <th>Avance actual</th>
                        <th>Avance actual</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lStudents as $student)
                        <tr>
                            <td>{{ str_pad($student->num_employee, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $withFunctionalArea ? $student->area : $student->department}}</td>
                            <td style="text-align: center">{{ number_format($student->generalAverage, 2, '.', '') }}</td>
                            <td style="text-align: center">{{ $student->nTotalAssignments }}</td>
                            <td style="text-align: center">{{ $student->nTotalApprovedAssignments }}</td>
                            <td style="text-align: center">{{ $student->nTotalCurrentAssignments }}</td>
                            <td style="text-align: center">{{ $student->nTotalCurrentAssignments > 0 ? number_format($student->currentAdvancePercent, 2, '.', '').'%' : '-' }}</td>
                            @if($student->nTotalCurrentAssignments > 0)
                            <td>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{ $student->currentAdvancePercent }}%" aria-valuenow="{{ $student->currentAdvancePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            @else
                                <td>
                                    -
                                </td>
                            @endif
                            <td style="text-align: center">
                                <a href="{{ route('kardex.index', $student->id) }}">
                                    <i class='bx bxs-graduation'></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Num</th>
                        <th>Estudiante</th>
                        @if ($withFunctionalArea)
                            <th>Área</th>
                        @else
                            <th>Dept</th>
                        @endif
                        <th>Promedio general</th>
                        <th>Comp</th>
                        <th>Aprob</th>
                        <th>Comp act</th>
                        <th>Avance actual</th>
                        <th>Avance actual</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection