@csrf
<div class="mb-3">
    <label for="course" class="form-label">Nombre del curso</label>
    <input type="text" class="form-control" id="course" name="course">
</div>
<div class="row">
    <div class="col-8">
        <div class="mb-3">
            <label for="course_key" class="form-label">Clave del curso</label>
            <input type="text" class="form-control" id="course_key" name="course_key">
        </div>
    </div>
    <div class="col-2">
        <div class="mb-3">
            <label for="completion_days" class="form-label">Duración</label>
            <input type="number" class="form-control" id="completion_days" name="completion_days">
        </div>
    </div>
    <div class="col-2">
        <div class="mb-3">
            <label for="university_points" class="form-label">Puntos</label>
            <input type="number" class="form-control" id="university_points" name="university_points">
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="sequence" class="form-label">Secuencia</label>
    <div>
        <select class="select2class form-control" name="sequence" style="width: 75%">
            @foreach ($sequences as $seq)
                <option value="{{ $seq->id_sequence }}">{{ $seq->seq }}</option>
            @endforeach
        </select>
    </div>
</div>
<input type="hidden" name="module_id" value="{{ $moduleId }}">
<div class="mb-3">
    <label for="objectives" class="form-label">Objetivos</label>
    <textarea id="objectives" name="objectives" class="form-control" aria-label="With textarea"></textarea>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea id="description" name="description" class="form-control" rows="5" aria-label="With textarea"></textarea>
</div>