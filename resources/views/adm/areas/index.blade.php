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
            this.deleteRoute = <?php echo json_encode( route('areasAdm.delete', ':id')); ?>;
    }

    var oServerData = new GlobalData();
</script>
@endsection

@section('content')
    @section('content_title', 'Areas funcionales')
    <div class="row" id="areasApp">
        <div class="col-12">
            <div style="float: right;">
                <a href="{{route('areasAdm.create')}}" class="btn btn-success">
                    Nueva área<i class='bx bx-plus'></i>
                </a>
            </div>
            <br>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <table id="assignments_table" class="display stripe hover row-border order-column" style="width:100%">
                        <thead>
                            <tr>
                                <th>Área</th>
                                <th>Supervisor</th>
                                <th>Área superior</th>
                                <th>-</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areas as $area)
                                <tr>
                                    <td>{{$area->area}}</td>
                                    <td>{{$area->user}}</td>
                                    <td>{{$area->father}}</td>
                                    <td style="text-align: center">
                                        <a href="{{route('areasAdm.edit', ['area_id' => $area->id_area])}}">
                                            <i class='bx bxs-brightness'></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center">
                                        <a href="#" v-on:click="deleteArea({{$area->id_area}}, '{{$area->area}}')">
                                            <i class='bx bx-trash' style="color: red;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Área</th>
                                <th>Supervisor</th>
                                <th>Área superior</th>
                                <th>-</th>
                                <th>-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/adm/areasVue.js') }}"></script>
@endsection