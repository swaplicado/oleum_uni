@csrf
<div class="row">
    <div class="col">
        <div class="mb-3">
            <label for="code" class="form-label">Código del premio</label>
            <input type="text" class="form-control" id="code" name="code" required>
        </div>
    </div>

    <div class="col">
        <div class="mb-3">
            <label for="points" class="form-label">Puntos Universitarios</label>
            <input type="number" class="form-control" id="points" name="points" required>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="gift" class="form-label">Nombre del premio</label>
    <input type="text" class="form-control" id="gift" name="gift" required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <input type="text" class="form-control" id="description" name="description">
</div>

<div class="mb-3">
    <label for="images" class="form-label">Imágenes</label>
    <input type="file" class="form-control" name="images[]" multiple/>
</div>