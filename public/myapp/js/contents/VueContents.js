var app = new Vue({
    el: '#contentsApp',
    data: {
        oData: oServerData,
        fileType: "",
        fileUrl: "",
        sText: "",
        sFileName: "",
        iContentId: 0,
        iOrder: 1
    },
    methods: {
        preview(idFile, type, fileName) {
            SGui.showWaiting(3000);
            this.fileType = type;
            this.sFileName = fileName;
            // let fn = fileName.replaceAll("\"", "");
            // console.log(fn);

            axios
                .get(this.oData.sGetRoute, {
                    params: {
                        'id_file': idFile
                    }
                })
                .then(response => {
                    console.log(response);
                    this.fileUrl = response.data;

                    switch (this.fileType) {
                        case 'video':
                            let video = document.getElementById('idVideo');
                            video.load();
                            break;
                        case 'text':
                            this.sText = this.fileUrl;
                            break;
                        case 'audio':
                            let audio = document.getElementById('idAudio');
                            audio.load();
                            break;
                        case 'file':
                            break;

                        default:
                            break;
                    }

                    $('#previewModal').modal('show');
                })
                .catch(err => {
                    console.log(err);
                    SGui.showError(err);
                });
        },
        readTextFile(filePath) {
            fetch(filePath).then(function(response) {
                return response
            }).then(function(data) {
                return data.text()
            }).then(function(normal) {
                this.sText = normal;
            }).catch(function(err) {
                console.log('Fetch problem show: ' + err.message);
            });
        },
        createElementContent() {
            this.iContentId = 0;
            this.iOrder = 1;
            $('#content_id').val('').trigger('change');
            $('#elemContentModalId').modal('show');
        },
        storeEC() {
            SGui.showWaiting(3000);

            /**
             * Petición al Controlador
             */
            axios.post(this.oData.storeRoute, {
                    'content': this.iContentId,
                    'order': this.iOrder,
                    'subtopic': this.oData.idSubtopic,
                })
                .then(response => {
                    let element = response.data;
                    this.oData.lElementContents.push(element);
                    $('#elemContentModalId').modal('hide');
                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        deleteFileSubtopic(id, name, ruta){
            Swal.fire({
                title: 'Desea eliminar?',
                text: name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = ruta;
                    url = url.replace(':id',id);
                    var fm = document.getElementById('form_delete');
                    fm.setAttribute('action', url);
                    fm.submit();
                }
            });
        }
    },
})