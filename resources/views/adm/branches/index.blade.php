@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oBranchesTable = $('#branches_table').DataTable({
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
                    { responsivePriority: 1, targets: 0 }
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
    <div class="row">
        <div class="col-12">
            <a id="rightnew" href="{{ route($newRoute) }}" class="btn btn-success">
                Nuevo<i class='bx bx-plus'></i>
            </a>
            <div class="row">
                <div class="col-md-12">
                    <table id="branches_table" class="display stripe hover row-border order-column" style="width:100%">
                        <thead>
                            <tr>
                                <th>Acrónimo</th>
                                <th>Sucursal</th>
                                <th>Titular</th>
                                <th>Empresa</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lBranches as $oBranch)
                                <tr>
                                    <td>{{ $oBranch->acronym }}</td>
                                    <td>{{ $oBranch->branch }}</td>
                                    <td>{{ $oBranch->full_name }}</td>
                                    <td>{{ $oBranch->company }}</td>
                                    <td style="text-align: center">
                                        <button class="btn btn-info">
                                            Editar <i class='bx bxs-edit-alt'></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Acrónimo</th>
                                <th>Sucursal</th>
                                <th>Titular</th>
                                <th>Empresa</th>
                                <th>-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection