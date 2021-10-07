@csrf
<div class="form-check">
    <input type="radio" v-model="typeContent" value="0"><b>Archivo</b>
    <br>
    <input type="radio" v-model="typeContent" value="1"><b>Link</b>
    <br>
    <input type="radio" v-model="typeContent" value="2"><b>Youtube Video</b>
</div>
<br>
<input type="hidden" name="tcontent" v-model="typeContent">
<div v-if="typeContent == 0" class="mb-3">
    <label for="theFile" class="form-label">Seleccione archivo a cargar</label>
    <input class="form-control" type="file" id="theFile" name="theFile" required>
</div>
<div v-else-if="typeContent == 1">
    <div class="mb-3">
        <label for="theName" class="form-label">Ingrese nombre</label>
        <input class="form-control" type="text" id="theName" name="theName" required>
    </div>
    <div class="mb-3">
        <label for="theFile" class="form-label">Ingrese link</label>
        <input class="form-control" type="text" id="theFile" name="theFile" required>
    </div>
</div>
<div v-else>
    <div class="mb-3">
        <label for="theName" class="form-label">Ingrese nombre video</label>
        <input class="form-control" type="text" id="theName" name="theName" required>
    </div>
    <div class="mb-3">
        <label for="video_id" class="form-label">Ingrese ID <span><i class='bx bxl-youtube bx-sm'></i></span></label>
        <input class="form-control" type="text" id="video_id" name="video_id" required>
    </div>
</div>