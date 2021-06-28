@csrf
<div class="form-check">
    <input class="form-check-input" type="checkbox" v-model="isFile" name="isFile" id="isFilet" checked>
    <label class="form-check-label" for="isFilet">
        Es Archivo
    </label>
</div>
<br>
<div v-if="isFile" class="mb-3">
    <label for="theFile" class="form-label">Seleccione archivo a cargar</label>
    <input class="form-control" type="file" id="theFile" name="theFile" required>
</div>
<div v-else>
    <div class="mb-3">
        <label for="theName" class="form-label">Ingrese nombre</label>
        <input class="form-control" type="text" id="theName" name="theName" required>
    </div>
    <div class="mb-3">
        <label for="theFile" class="form-label">Ingrese link</label>
        <input class="form-control" type="text" id="theFile" name="theFile" required>
    </div>
</div>