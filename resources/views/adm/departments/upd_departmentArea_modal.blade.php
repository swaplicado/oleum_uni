<!-- Modal -->
<div class="modal fade" id="departmentArea" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modificar área funcional: @{{depto}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="username" class="col-form-label">Área funcional</label>
                    <div class="col-12">
                        <select class="select2class form-control" name="area" id="selArea" style="width: 80%">
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" v-on:click="updateDeptoArea();">Actualizar</button>
            </div>
        </div>
    </div>
</div>