<div id="elemContentModalId"  class="modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Contenido de subtema</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div>
            <div class="mb-3">
                <label for="content_id" class="form-label">Seleccione contenido</label>
                <select v-model="iContentId" style="width: 100%" class="form-control" id="content_id" name="content_id" placeholder="Contenido...">
                    <option v-for="content in oData.lContents" :value="content.id_content">@{{ content.file_name }}</option>
                </select>
            </div>
            <div class="mb-3">
                <div class="row">
                    <div class="col-6">
                        <label for="order" class="form-label">Orden</label>
                        <input v-model="iOrder" class="form-control" min="1" type="number" name="order" id="order">
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" v-on:click="storeEC()">Guardar</button>
        </div>
      </div>
    </div>
  </div>