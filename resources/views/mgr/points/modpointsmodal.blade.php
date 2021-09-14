<div id="modPointsModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modificar puntos universitarios</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-check">
              <input class="form-check-input" type="radio" value="1" name="movClassRadio" id="movClassRadio1" v-on:change="onClassChange()" v-model="movClass" checked>
              <label class="form-check-label" for="flexRadioDefault1">
                Agregar puntos
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" value="2" name="movClassRadio" id="movClassRadio2" v-on:change="onClassChange()" v-model="movClass">
              <label class="form-check-label" for="flexRadioDefault2">
                Restar puntos
              </label>
            </div>
            <div class="mb-3">
              <label for="mov_type" class="form-label">Seleccione tipo de movimiento</label>
              <select id="mov_type" class="form-select" aria-label="Default select example" v-model="movType">
                <option v-for="mt in lMovTypes" :value="mt.id_mov_type">@{{ mt.code + "-" + mt.movement_type }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="pointsField" class="form-label">Puntos universitarios</label>
              <input type="number" class="form-control" id="pointsField" placeholder="99.0" v-model="points">
            </div>
            <div class="mb-3">
              <label for="comments" class="form-label">Puntos universitarios</label>
              <input type="text" class="form-control" id="comments" maxlength="199" placeholder="Comentarios" v-model="comments">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" v-on:click="storeMovement()" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
  </div>