/**
 * Vue Aplicación para Temas
 */
var appTopics = new Vue({
    el: '#topicsApp',
    data: {
        message: 'Hello Vue!',
        oData: oServerData,
        oTopic: new Topic(),
        oSubTopic: new SubTopic(),
        oEdit: {id: '', name: ''},
        editType: null,
        copyElementClass: new copyElementClass(),
        lAreas: oServerData.lAreas,
        cuadrante: null,
        module: null,
        course: null,
        topic: null,
        origen_id: null,
        destino_id:  null,
        type: null,
        disabledCopy: false,
    },
    mounted(){
        var self = this;
        var cuadrantes = [];
        cuadrantes.push({id: '', text: ''});
        for(var i = 0; i<self.lAreas.length; i++){
            cuadrantes.push({id: self.lAreas[i].id_knowledge_area, text: self.lAreas[i].knowledge_area});
        }

        this.copyElementClass.setDefaultSelects(cuadrantes);
        this.copyElementClass.select2OnChange();
    },
    methods: {
        /**
         * Despliega el modal para la captura de un nuevo tema
         */
        createTopic() {
            this.oTopic = new Topic();
            this.oTopic.course_id = this.oData.courseId;

            $("#topicsModal").modal("show");
        },
        /**
         * Realiza la petición al servidor para guardar el tema en la BD
         */
        storeTopic() {
            if (!this.validateTopic()) {
                return;
            }

            SGui.showWaiting(3000);
            console.log(this.oTopic);

            /**
             * Petición al Controlador
             */
            axios.post(this.oData.storeRoute, {
                    'topic': JSON.stringify(this.oTopic)
                })
                .then(response => {
                    console.log(response);
                    let topic = response.data;
                    this.addTopic(topic);
                    $('#topicsModal').modal('hide');
                    SGui.showOk();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        /**
         * Despliega el modal para crear un nuevo subtema
         * 
         * @param {int} idTopic 
         */
        createSubtopic(idTopic) {
            this.oSubTopic = new SubTopic();
            this.oSubTopic.topic_id = idTopic;

            $("#subTopicsModal").modal("show");
        },
        /**
         * Realiza la petición al servidor para guardar el subtema en la BD
         */
        storeSubTopic() {
            if (!this.validateSubtopic()) {
                return;
            }

            SGui.showWaiting(3000);
            console.log(this.oSubTopic);

            /**
             * Petición al Controlador
             */
            axios.post(this.oData.storeSubRoute, {
                    'subtopic': JSON.stringify(this.oSubTopic)
                })
                .then(response => {
                    console.log(response);
                    let subtopic = response.data;
                    this.addSubTopic(subtopic);
                    $('#subTopicsModal').modal('hide');
                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        /**
         * Agrega el tema a la lista, esto actualiza la pantalla con el nuevo elemento
         * 
         * @param {Topic} topic 
         */
        addTopic(topic) {
            this.oData.lTopics.push(topic);
        },
        /**
         * Agrega el subtema a la lista, esto actualiza el <div>
         * 
         * @param {SubTopic} subtopic 
         */
        addSubTopic(subtopic) {
            // for (const top of this.oData.lTopics) {
            //     if (top.id_topic == subtopic.topic_id) {
            //         if (top.lSubtopics == undefined || top.lSubtopics.length == 0) {
            //             top.lSubtopics = [];
            //         }
            //         top.lSubtopics.push(subtopic);
            //         break;
            //     }
            // }
        },
        /**
         * Validación de Tema
         */
        validateTopic() {
            if (this.oTopic.topic == "") {
                SGui.showError("El nombre del tema no puede estar vacío");
                return false;
            }
            if (this.oTopic.sequence_id == 0) {
                SGui.showError("Debes elegir una secuencia");
                return false;
            }

            return true;
        },
        /**
         * Validación de subtema
         */
        validateSubtopic() {
            if (this.oSubTopic.subtopic == "") {
                SGui.showError("El nombre del subtema no puede estar vacío");
                return false;
            }
            if (this.oSubTopic.number_questions < 1) {
                SGui.showError("El mínimo de preguntas es 1");
                return false;
            }

            return true;
        },

        topicDelete(topic_id, name, ruta) {
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
                    url = url.replace(':id',topic_id);
                    var fm = document.getElementById('form_delete');
                    fm.setAttribute('action', url);
                    fm.submit();
                }
            });
        },

        subtopicDelete(subtopic_id, name, ruta) {
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
                    url = url.replace(':id',subtopic_id);
                    var fm = document.getElementById('form_delete');
                    fm.setAttribute('action', url);
                    fm.submit();
                }
            });
        },

        editTopic(topic_id, name, ruta){
            this.oEdit.id = topic_id;
            this.oEdit.name = name;
            this.editType = 'Tema';
            var url = ruta;
            url = url.replace(':id',topic_id);
            var fm = document.getElementById('mform');
            fm.setAttribute('action', url);
        },

        editSubtopic(subtopic_id, name, ruta){
            this.oEdit.id = subtopic_id;
            this.oEdit.name = name;
            this.editType = 'Subtema';
            var url = ruta;
            url = url.replace(':id',subtopic_id);
            var fm = document.getElementById('mform');
            fm.setAttribute('action', url);
        },

        showCopyElementModal(origen_id, type){
            this.copyElementClass.showCopyElementModal(origen_id, type);
        },

        copyElement(){
            this.disabledCopy = true;
            this.copyElementClass.copyElement();
        },
    },
})