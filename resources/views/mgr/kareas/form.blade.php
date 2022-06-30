@csrf
<div class="mb-3">
    <label for="name" class="form-label">Nombre del cuadrante</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ isset($oKa) ? $oKa->knowledge_area : '' }}">
</div>
<div class="mb-3">
    <label for="sequence" class="form-label">Secuencia</label>
    <div>
        <select class="select2class form-control" name="sequence" style="width: 75%">
            @foreach ($sequences as $seq)
                <option value="{{ $seq->id_sequence }}" {{ isset($oKa) && $oKa->sequence_id == $seq->id_sequence ? "selected" : "" }}>{{ $seq->seq }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label for="course_cover" class="form-label">Portada del cuadrante (se recomienda una imagen con relación de aspecto 16:9)</label>
    <div>
        <select id="sel_portada" value="{{ isset($oCover) ? $oCover->id_content : 0 }}" class="select2class form-control" name="cuadrante_cover" style="width: 75%" required>
                <option value="0">Sin portada</option>
            @foreach ($lContents as $content)
                <option {{ isset($oCover) ? (($oCover->id_content) == $content->id_content ? 'selected' : '') : '' }} value="{{ $content->id_content }}">{{ $content->file_name.' - '.$content->f_type }}</option>
            @endforeach
        </select>
        <a class="btn btn-danger" style="height: 28px;" onclick="$('#sel_portada').val('0').trigger('change');"><span class="bx bx-x"></span></a>
    </div>
</div>

<div class="mb-3">
    <label for="objectives" class="form-label">Objetivos</label>
    <textarea id="objectives" name="objectives" class="form-control" aria-label="With textarea">{{ isset($oKa) ? $oKa->objectives : '' }}</textarea>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea class="form-control" id="description" name="description" rows="15">{{ isset($oKa) ? $oKa->description : '' }}</textarea>
</div>
<div class="mb-3">
  <label class="form-check-label">
    <input type="checkbox" class="form-check-input" name="has_document" {{ isset($oKa) && $oKa->has_document ? 'checked' : '' }}>
    Genera certificado
  </label>
</div>