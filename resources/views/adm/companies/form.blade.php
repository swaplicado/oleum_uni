@csrf
<div class="mb-3">
    <label for="acronym" class="form-label">Acrónimo de la empresa</label>
    <input type="text" class="form-control" id="acronym" name="acronym" required>
</div>
<div class="mb-3">
    <label for="company" class="form-label">Nombre de la empresa</label>
    <input type="text" class="form-control" id="company" name="company" required>
</div>
<div class="mb-3">
    <label for="head_user_id" class="form-label">Titular</label>
    <div>
        <select class="select2class form-control" name="head_user_id" style="width: 75%" required>
            @foreach ($users as $usr)
                <option value="{{ $usr->id }}">{{ $usr->full_name }}</option>
            @endforeach
        </select>
    </div>
</div>