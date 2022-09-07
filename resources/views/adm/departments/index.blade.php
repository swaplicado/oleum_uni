@extends('layouts.appuni')

@section('scripts_section')
    <script type="text/javascript">
        $(document).ready(function() {
            var oProfileTable = $('#departments').DataTable({
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
                //     { responsivePriority: 1, targets: 4 }
                // ],
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
            this.lAreas = <?php echo json_encode( $lAreas ) ?>;
            this.routeUpDepto = <?php echo json_encode( route('areasAdm.departments.update') ) ?>;
        }
        
        var oServerData = new GlobalData();
    </script>
    <script>
        $(document).ready(function() {
            $('.select2class').select2({
                dropdownParent: $('#departmentArea')
            });
        });
    </script>
@endsection

@section('content')
    @section('content_title', 'Departments')
    <div class="row" id="departmentsApp">
        <div class="col-12">
            <table id="departments" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Área funcional</th>
                        <th style="text-align: center">-</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($lDepartments as $department)
                   <tr>
                        <td>{{ $department->department }}</td>
                        <td>{{ $department->area }}</td>
                        <td style="text-align: center">
                            <a href="#" v-on:click="editArea({{$department->id_department}}, '{{$department->department}}', {{$department->area_id}});" title="Cambiar area funcional">
                                <i class='bx bxs-spreadsheet'></i>
                            </a>
                        </td>
                    </tr>
                   @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Departamento</th>
                        <th>Área funcional</th>
                        <th style="text-align: center">-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @include('adm.departments.upd_departmentArea_modal')
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/adm/departmentsVue.js') }}"></script>
@endsection