@csrf
<div class="row">
    <div class="col-6">
        <label for="dt_assignment" class="form-label">Fecha inicio*:</label>
        <input type="date" class="form-control" id="dt_assignment" name="dt_assignment" v-model="dtStart" required>
    </div>
    <div class="col-6">
        <label for="dt_end" class="form-label">Fecha límite*:</label>
        <input type="date" class="form-control" id="dt_end" name="dt_end" v-model="dtEnd" required>
    </div>
</div>
<br>
<div class="mb-3">
    <label for="ka_id" class="form-label">Cuadrante*:</label>
    <div>
        <select class="form-control" id="selec_ka" v-model="kaId" name="ka_id" style="width: 85%" required v-select2>
        </select>
    </div>
</div>
<div class="mb-3">
    <label for="assignment_by" class="form-label">Asignar por*:</label>
    <div>
        <select class="form-control" id="selec_iAssignmentBy" v-model="iAssignmentBy" name="assignment_by" style="width: 85%" required v-select2>
        </select>
    </div>
</div>

<div class="mb-3">
    <label for="type_sel" class="form-label">@{{type_sel}}*:</label>
    <div>
        <select class="form-control" id="type_selec" name="type_sel" style="width: 85%"></select>
    </div>
</div>