@csrf
<div class="mb-3">
    <label for="course" class="form-label">Nombre del curso</label>
    <input type="text" value="{{ old('course', $oCourse->course ?? '') }}" class="form-control" id="course" name="course">
</div>
<div class="row">
    <div class="col-4">
        <div class="mb-3">
            <label for="course_key" class="form-label">Clave del curso</label>
            <input type="text" value="{{ old('course_key', $oCourse->course_key ?? '') }}" class="form-control" id="course_key" name="course_key">
        </div>
    </div>
    <div class="col-2">
        <div class="mb-3">
            <label for="completion_days" class="form-label">Duración (días)</label>
            <input type="number" value="{{ old('completion_days', $oCourse->completion_days ?? '') }}" class="form-control" id="completion_days" name="completion_days">
        </div>
    </div>
    <div class="col-2">
        <br>
        <div class="mb-3">
            <label class="form-check-label">
              <input type="checkbox" class="form-check-input" name="has_document"  {{ (isset($oCourse) && $oCourse->has_document) || !isset($oCourse) ? 'checked' : '' }}>
                Genera constancia
            </label>
        </div>
    </div>
    <div class="col-2">
        <br>
        <div class="mb-3">
            <label class="form-check-label">
              <input type="checkbox" class="form-check-input" name="has_points" {{ (isset($oCourse) && $oCourse->has_points) || !isset($oCourse) ? 'checked' : '' }}>
              Genera puntos
            </label>
        </div>
    </div>
    <div class="col-2">
        <div class="mb-3">
            <label for="university_points" class="form-label">Puntos</label>
            <input type="number" value="{{ old('university_points', $oCourse->university_points ?? '') }}"  class="form-control" id="university_points" name="university_points">
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="sequence" class="form-label">Secuencia</label>
    <div>
        <select value="{{ isset($oCourse) ? $oCourse->sequence_id : 1 }}" class="select2class form-control" name="sequence" style="width: 75%">
            @foreach ($sequences as $seq)
                <option {{ isset($oCourse) ? (($oCourse->sequence_id) == $seq->id_sequence ? 'selected' : '') : '' }} value="{{ $seq->id_sequence }}">{{ $seq->seq }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mb-3">
    <label for="course_cover" class="form-label">Portada del curso</label>
    <div>
        <select value="{{ isset($oCover) ? $oCover->id_content : 0 }}" class="select2class form-control" name="course_cover" style="width: 75%" required>
            @foreach ($lContents as $content)
                <option {{ isset($oCover) ? (($oCover->id_content) == $content->id_content ? 'selected' : '') : '' }} value="{{ $content->id_content }}">{{ $content->file_name.' - '.$content->f_type }}</option>
            @endforeach
        </select>
    </div>
</div>
@if (! isset($oCourse))
    <input type="hidden" name="module_id" value="{{ $moduleId }}">
@endif
<div class="mb-3">
    <label for="objectives" class="form-label">Objetivos</label>
    <textarea id="objectives" name="objectives" class="form-control" aria-label="With textarea">{{ isset($oCourse) ? $oCourse->objectives : '' }}</textarea>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea id="description" name="description" class="form-control" rows="5" aria-label="With textarea">{{ isset($oCourse) ? $oCourse->description : '' }}</textarea>
</div>