@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oKareasTable = $('#example').DataTable({
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
                    { responsivePriority: 1, targets: [4, 5] }
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
    $(function () {
        var statusModal = document.getElementById('statusModal');
        statusModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var recipient = button.getAttribute('data-bs-whatever');
            var id = button.getAttribute('data-id');
            var modalTitle = statusModal.querySelector('.modal-title');
            var modalBodyInput = document.getElementById('row_id');

            modalTitle.textContent = recipient;
            modalBodyInput.value = id;
        });
        statusModal.addEventListener('hidden.bs.modal', function (event) {
            $(this).find('#mform')[0].reset();
        });
    })
</script>
<script>
    function areaDelete(area_id, name, ruta) {
            Swal.fire({
                title: 'Desea eliminar?',
                text: name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = ruta;
                    url = url.replace(':id',area_id);
                    var fm = document.getElementById('form_delete');
                    fm.setAttribute('action', url);
                    fm.submit();
                }
            });
        }
</script>
@endsection

@section('content')
<form id="form_delete" class="d-inline" method="POST" style="display: none;">
    @csrf @method("delete")
</form>
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="mform" action="{{route('kareas.status')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="estatus" class="form-label">Estatus:</label>
                        <select class="form-select" name="estatus" required>
                            <option value="" selected>Selecciona estatus</option>
                            <option value="1">Nuevo</option>
                            <option value="2">Editando</option>
                            <option value="3">Publicado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="row_id" id="row_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
  
    @section('content_title', $title)
    <a id="rightnew" href="{{ route($newRoute) }}" class="btn btn-success">
        Nuevo<i class='bx bx-plus'></i>
    </a>
    <div class="row" id="divPrerrequisites">
        <div class="col-md-12">
            <table id="example" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Área de competencia</th>
                        <th>Descripción</th>
                        <th>Secuencia</th>
                        <th>Estatus</th>
                        <th>Módulos</th>
                        <th>Pre</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lAreas as $area)
                        <tr>
                            <td>{{ $area->knowledge_area }}</td>
                            <td>{{ $area->description }}</td>
                            <td>{{ $area->seq_code }}</td>
                            <td>
                                {{ $area->status_code }}
                                <a href="#" data-bs-toggle="modal" data-bs-target="#statusModal" 
                                    data-bs-whatever="{{ $area->knowledge_area }}" data-id="{{$area->id_knowledge_area}}">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                            </td>
                            <td style="text-align: center">
                                <a title="Ver módulos" href="{{ route('modules.index', ['ka' => $area->id_knowledge_area]) }}">
                                    <i class='bx bxs-category'></i>
                                </a>
                                <a title="Editar área de competencia" href="{{ route('kareas.edit', $area->id_knowledge_area) }}">
                                    <i class='bx bx-edit'></i>
                                </a>
                            </td>
                            <td style="text-align: center">
                                <a href="#" v-on:click="showPreviousModal({{ config('csys.elem_type.AREA') }}, {{ $area->id_knowledge_area }}, '{{ $area->knowledge_area }}')">
                                    <i class='bx bxs-brightness'></i>
                                </a>
                            </td>
                            <td style="text-align: center">
                                <a href="#" onclick="areaDelete({{$area->id_knowledge_area}}, '{{$area->knowledge_area}}', '{{route('kareas.delete', ':id')}}')">
                                    <i class='bx bx-trash' style="color: red;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Área de competencia</th>
                        <th>Descripción</th>
                        <th>Secuencia</th>
                        <th>Estatus</th>
                        <th>Módulos</th>
                        <th>Pre</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @include('mgr.prerequisites_modal')
    </div>
@endsection