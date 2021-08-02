@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oGiftsTable = $('#gifts_table').DataTable({
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
    
<div class="row">
    <div class="col-12">
        @section('content_title', 'Premios')
        <a id="rightnew" href="{{ route($newRoute) }}" class="btn btn-success">
            Nuevo<i class='bx bx-plus'></i>
        </a>
        <table id="gifts_table" class="display stripe hover row-border order-column" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Premio</th>
                    <th>Descripción</th>
                    <th>Puntos</th>
                    <th>Activo</th>
                    <th>Disponibles</th>
                    <th>-</th>
                </tr>
            </thead>
            <tbody>
               @foreach ($lGifts as $oGift)
                    <tr>
                        <td>
                            {{ $oGift->code }}
                        </td>
                        <td>
                            {{ $oGift->gift }}
                        </td>
                        <td>
                            {{ $oGift->description }}
                        </td>
                        <td>
                            {{ $oGift->points_value }}
                        </td>
                        <td>
                            {{ $oGift->is_active }}
                        </td>
                        <td>
                            {{ $oGift->stk != null ? $oGift->stk->d_stk : 0 }}
                        </td>
                        <td>
                            <button class="btn btn-success"><i class='bx bxs-box'></i></button>
                            <button class="btn btn-danger"><i class='bx bxs-archive-out'></i></button>
                        </td>
                    </tr>
               @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>#</th>
                    <th>Premio</th>
                    <th>Descripción</th>
                    <th>Puntos</th>
                    <th>Activo</th>
                    <th>Disponibles</th>
                    <th>-</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection