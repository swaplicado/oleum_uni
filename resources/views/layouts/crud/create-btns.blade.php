<div class="row">
    @section('scripts_section_complement')
        <script>
            function onSave() {
                let isValid = document.querySelector('#createForm').reportValidity();

                if (! isValid) {
                    return;
                }

                document.getElementById("saveButton").disabled = true;
                SGui.showWaiting(3000);
                document.getElementById("createForm").submit();
            }
        </script>
    @endsection
    <div class="col-9"></div>
    <div class="col-3">
        <button id="saveButton" onclick="onSave()"  class="btn btn-success">Guardar</button>
        <button type="reset" onclick="window.history.back();" class="btn btn-danger">Cancelar</button>
    </div>
</div>