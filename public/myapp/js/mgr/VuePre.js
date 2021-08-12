var app = new Vue({
    el: '#divPrerrequisites',
    data: {
        oData: oGlobalData,
        texTypeElement: '',
        textName: '',
        lPrerrequisites: [],
        idPre: 0,
        iElementType: 0,
        iElementReference: 0,
        iRowType: 1,
        lAreas: [],
        lModules: [],
        lCourses: [],
        iReferenceId: 0,
    },
    methods: {
        showPreviousModal(idType, idReference, name) {
            SGui.showWaiting(4000);

            this.iElementType = idType;
            this.iElementReference = idReference;
            this.textName = name;
            this.texTypeElement = "";
            this.iRowType = 1;
            this.lPrerrequisites = [];
            this.idPre = 0;
            this.lAreas = 0;
            this.lModules = 0;
            this.lCourses = 0;
            this.iReferenceId = 0;

            switch (idType) {
                case 1:
                    this.texTypeElement = "Área de competencia"
                    break;
                case 2:
                    this.texTypeElement = "Módulo"
                    break;
                case 3:
                    this.texTypeElement = "Curso"
                    break;
                case 4:
                    this.texTypeElement = "Tema"
                    break;
                case 5:
                    this.texTypeElement = "Subtema"
                    break;

                default:
                    break;
            }

            this.loadPreData(idType, idReference);
        },
        loadPreData(idType, idReference) {
            axios
                .get(this.oData.sGetRoute, {
                    params: {
                        'id_type': idType,
                        'id_reference': idReference
                    }
                })
                .then(response => {
                    console.log(response);
                    let data = response.data;

                    this.lAreas = data.lAreas;
                    this.lModules = data.lModules;
                    this.lCourses = data.lCourses;

                    this.lPrerrequisites = data.lPres == null ? [] : data.lPres;
                    this.idPre = data.oPre == null ? 0 : data.oPre.id_prerequisite;

                    $('#previousRequisites').modal('show');
                })
                .catch(err => {
                    console.log(err);
                    SGui.showError(err);
                });
        },
        createPrerequisite() {
            let oRow = new PrerequisiteRow();

            oRow.is_deleted = false;
            oRow.prerequisite_id = this.idPre;
            oRow.element_type_id = this.iRowType;

            switch (parseInt(this.iRowType, 10)) {
                case 1:
                    oRow.knowledge_area_n_id = this.iReferenceId;
                    break;
                case 2:
                    oRow.module_n_id = this.iReferenceId;
                    break;
                case 3:
                    oRow.course_n_id = this.iReferenceId;
                    break;

                default:
                    break;
            }

            Swal.fire({
                title: '¿Dar de alta el requisito previo?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, agrégalo'
            }).then((result) => {
                if (result.isConfirmed) {
                    SGui.showWaiting(3000);

                    /**
                     * Petición al Controlador
                     */
                    axios.post(this.oData.storeRoute, {
                            'id_prerequisite': this.idPre,
                            'elem_type_id': this.iElementType,
                            'elem_reference_id': this.iElementReference,
                            'reference_id': this.iReferenceId,
                            'row': JSON.stringify(oRow),
                        })
                        .then(response => {
                            let element = response.data;
                            this.lPrerrequisites = element;
                            SGui.showOk();
                            // $('#previousRequisites').modal('hide');
                        })
                        .catch(function(error) {
                            console.log(error);
                            SGui.showError(error);
                        });
                }
            })
        },
        deletePrerequisiteRow(oRow) {
            SGui.showWaiting(3000);

            /**
             * Petición al Controlador
             */
            axios.put(this.oData.deleteRoute, {
                    'id_prerequisite_row': oRow.id,
                    'prerequisite_id': oRow.prerequisite_id
                })
                .then(response => {
                    let element = response.data;
                    this.lPrerrequisites = element;
                    SGui.showOk();
                    // $('#previousRequisites').modal('hide');
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        getRowText(oRow) {
            let text = oRow.element_type + "-";
            switch (parseInt(oRow.id_element_type, 10)) {
                case 1:
                    text += oRow.knowledge_area;
                    break;
                case 2:
                    text += oRow.module;
                    break;
                case 3:
                    text += oRow.course;
                    break;

                default:
                    break;
            }

            return text;
        }
    },
})