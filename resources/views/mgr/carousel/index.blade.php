@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oCarouselTable = $('#carousel_table').DataTable({
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
                    { responsivePriority: 1, targets: [5] }
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
    function carouselDelete(route) {
         /**
         * Petición al Controlador
         */
        axios.delete(route, {})
        .then(response => {
            let res = response.data;

            SGui.showOk();
            location.reload();
        })
        .catch(function(error) {
            console.log(error);
            SGui.showError(error);
        });
    }
</script>
@endsection

@section('content')
    @section('content_title', $title)
    <a id="rightnew" href="{{ route($newRoute) }}" class="btn btn-success">
        Nuevo<i class='bx bx-plus'></i>
    </a>
    <div class="row">
        <div class="col-md-12">
            <table id="carousel_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Video</th>
                        <th>Título</th>
                        <th>Texto</th>
                        <th>URL</th>
                        <th>Activo</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lElements as $element)
                        <tr>
                            <td>{{ $element->image }}</td>
                            <td>{{ $element->content_n_id != null ? $element->file_name : "" }}</td>
                            <td>{{ $element->title }}</td>
                            <td>{{ $element->text }}</td>
                            <td><a href="{{ $element->url }}">{{ $element->url }}</a></td>
                            <td>{{ $element->is_active }}</td>
                            <td style="text-align: center">
                                <a href="{{ route($editRoute, $element->id_slide) }}">
                                    <i class='bx bxs-edit-alt'></i>
                                </a>
                                <a href="#" onclick="carouselDelete('{{ route($deleteRoute, $element->id_slide) }}')">
                                    <i class='bx bx-x'></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Imagen</th>
                        <th>Video</th>
                        <th>Título</th>
                        <th>Texto</th>
                        <th>URL</th>
                        <th>Activo</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection