@extends('layouts.appuni')

@section('scripts_section')


@endsection

@section('content')
    @section('content_title', $title)
    <div class="row" id="pointsApp">
        <div class="col-md-12">
            <table id="points_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Ganados</th>
                        <th>Restados</th>
                        <th>Total</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lPoints as $point)
                        <tr>
                            <td>{{ $point->full_name }}</td>
                            <td style="text-align: right">{{ number_format($point->t_increments, 1) }}</td>
                            <td style="text-align: right">{{ number_format($point->t_decrements, 1) }}</td>
                            <td style="text-align: right">{{ number_format($point->t_increments - $point->t_decrements, 1) }}</td>
                            <td style="text-align: center">
                                <button class="btn btn-light" title="Detalle de movimientos" v-on:click="{{ "viewDetail(".$point->student_id.")" }}">
                                    <i class='bx bxs-detail'></i>
                                </button>
                                <button class="btn btn-light" title="Ajustar puntos" v-on:click="{{ "adjustPoints(".$point->student_id.")" }}">
                                    <i class='bx bxs-sort-alt'></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Estudiante</th>
                        <th>Ganados</th>
                        <th>Restados</th>
                        <th>Total</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @include('mgr.points.detailmodal')
        @include('mgr.points.modpointsmodal')
    </div>
@endsection

@section('bottom_scripts')
<script type="text/javascript">
    // $(document).ready(function() {
        var oPointsTable = $('#points_table').DataTable({
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
                    { responsivePriority: 1, targets: [0, 1] }
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

        var oDetailTable = $('#details_table').DataTable({
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
                    { responsivePriority: 1, targets: [0, 1] }
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
    // });
</script>
<script type="text/javascript">
        function GlobalData () {
            this.sGetRoute = <?php echo json_encode( route($sGetRoute) ) ?>;
            this.sStoreRoute = <?php echo json_encode( route($sStoreRoute) ) ?>;
            this.inMovTypes = <?php echo json_encode( $inMovTypes ) ?>;
            this.outMovTypes = <?php echo json_encode( $outMovTypes ) ?>;
        }

        var oGlobalData = new GlobalData();
</script>
<script type="text/javascript" src="{{ asset('myapp/js/mgr/points/VuePoints.js') }}"></script>
@endsection