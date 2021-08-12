@csrf
<div class="mb-3">
    <label for="title" class="form-label">Título de la imagen</label>
    <input type="text" value="{{ old('title', $oCarousel->title ?? '') }}" class="form-control" id="title" name="title" placeholder="Título de la imagen">
</div>
<div class="mb-3">
    <label for="text" class="form-label">Texto en imagen</label>
    <input type="text" value="{{ old('text', $oCarousel->text ?? '') }}" class="form-control" id="text" name="text" placeholder="Texto de la imagen">
</div>
<div class="mb-3">
    <label for="text_color" class="form-label">Color del texto</label>
    <div class="col-2">
        <input type="color" value="{{ old('text_color', $oCarousel->text_color ?? '#000000') }}" class="form-control" id="text_color" name="text_color">
    </div>
</div>
<div class="mb-3">
    <label for="link" class="form-label">Url</label>
    <input type="text" width="20%" value="{{ old('link', $oCarousel->url ?? '') }}" class="form-control" id="link" name="link" placeholder="Url de la imagen">
</div>
@if (isset($oCarousel))
    <input type="hidden" name="id_slide" value="{{ $oCarousel->id_slide }}">
@endif
<hr>
<div class="mb-3">
    @if (isset($image))
        <label for="img_cur" class="form-label">Imagen actual:</label>
        <br>
        <img src="{{ asset($image) }}" alt="">
    @endif
    <br>
    <label for="img" class="form-label">Seleccione imagen</label>
    <input class="form-control" type="file" name="img" {{ isset($image) ? '' : 'required' }}>
</div>
<div class="form-check">
    <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ (isset($oCarousel) && $oCarousel->is_active) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">
        Activo
    </label>
</div>