class copyElementClass {

    showCopyElementModal(origen_id, type){
        appVue.origen_id = origen_id;
        appVue.type = type;
        appVue.area = null;
        appVue.module = null;
        appVue.course = null;
        appVue.topic = null;
        $('#sel_cuadrante').val('').trigger('change');
        $('#sel_modules').empty().prop("disabled", true);
        $('#sel_courses').empty().prop("disabled", true);
        $('#sel_topics').empty().prop("disabled", true);
        $('#modalCopyElement').modal('show');
    }

    setDefaultSelects(cuadrantes){
        $('#sel_cuadrante').select2({
            placeholder: 'selecciona cuadrante',
            data: [],
            dropdownParent: $('#modalCopyElement')
        });

        $('#sel_modules').select2({
            placeholder: 'selecciona módulo',
            data: [],
            dropdownParent: $('#modalCopyElement')
        }).prop("disabled", true);

        $('#sel_courses').select2({
            placeholder: 'selecciona curso',
            data: [],
            dropdownParent: $('#modalCopyElement')
        }).prop("disabled", true);

        $('#sel_topics').select2({
            placeholder: 'selecciona tema',
            data: [],
            dropdownParent: $('#modalCopyElement')
        }).prop("disabled", true);

        $('#sel_cuadrante').select2({
            placeholder: 'selecciona cuadrante',
            data: cuadrantes,
            dropdownParent: $('#modalCopyElement')
        });
    }

    select2OnChange(){
        $('#sel_cuadrante').on('select2:select', function (e){
            appVue.cuadrante = e.params.data.id;
            appVue.copyElementClass.getModules(e.params.data.id, appVue.oData.modulesRoute);
        });

        $('#sel_modules').on('select2:select', function (e){
            appVue.module = e.params.data.id;
            appVue.copyElementClass.getCourses(e.params.data.id, appVue.oData.coursesRoute);
        });

        $('#sel_courses').on('select2:select', function (e){
            appVue.course = e.params.data.id;
            appVue.copyElementClass.getTopics(e.params.data.id, appVue.oData.topicsRoute);
        });

        $('#sel_topics').on('select2:select', function (e){
            appVue.topic = e.params.data.id;
        });
    }

    setModules(){
        appVue.module = null;
        appVue.course = null;
        appVue.topic = null;

        $('#sel_courses').empty().prop("disabled", true);
        $('#sel_topics').empty().prop("disabled", true);
        $('#sel_modules').empty();
        $('#sel_modules').select2({
            dropdownParent: $('#modalCopyElement'),
            placeholder: 'selecciona módulo',
            data: appVue.lModules,
        }).prop("disabled", false);
    }

    setCourses(){
        appVue.course = null,
        appVue.topic = null,

        $('#sel_topics').empty().prop("disabled", true);
        $('#sel_courses').empty();
        $('#sel_courses').select2({
            dropdownParent: $('#modalCopyElement'),
            placeholder: 'selecciona curso',
            data: appVue.lCourses,
        }).prop("disabled", false);
    }

    setTopics(){
        $('#sel_topics').empty();
        $('#sel_topics').select2({
            dropdownParent: $('#modalCopyElement'),
            placeholder: 'selecciona tema',
            data: appVue.lTopics,
        }).prop("disabled", false);;
    }

    setSubtopics(){
        $('#sel_subtopics').empty();
        $('#sel_subtopics').select2({
            dropdownParent: $('#modalCopyElement'),
            placeholder: 'selecciona subtema',
            data: appVue.lSubtopics,
        }).prop("disabled", false);;
    }

    getModules(ka_id, modulesRoute){
        axios.post(modulesRoute, {
            'ka': ka_id,
        })
        .then(response => {
            appVue.lModules = response.data;
            this.setModules();
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    getCourses(mo_id, coursesRoute){
        axios.post(coursesRoute, {
            'mo': mo_id,
        })
        .then(response => {
            appVue.lCourses = response.data;
            this.setCourses();
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    getTopics(course_id, topicsRoute){
        axios.post(topicsRoute, {
            'course': course_id,
        })
        .then(response => {
            appVue.lTopics = response.data;
            this.setTopics();
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    getSubtopics(topic_id, subtopicsRoute){
        axios.post(subtopicsRoute, {
            'topic': topic_id,
        })
        .then(response => {
            appVue.lSubtopics = response.data;
            this.setSubtopics();
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    cleanCuadrante(){
        appVue.area = null,
        appVue.module = null,
        appVue.course = null,
        appVue.topic = null,

        $('#sel_cuadrante').val('').trigger('change');
        $('#sel_modules').empty().prop("disabled", true);
        $('#sel_courses').empty().prop("disabled", true);
        $('#sel_topics').empty().prop("disabled", true);
    }

    cleanModule(){
        appVue.module = null,
        appVue.course = null,
        appVue.topic = null,

        $('#sel_modules').val('').trigger('change');
        $('#sel_courses').empty().prop("disabled", true);
        $('#sel_topics').empty().prop("disabled", true);
    }

    cleanCourse(){
        appVue.course = null,
        appVue.topic = null,
        $('#sel_courses').val('').trigger('change');
        $('#sel_topics').empty().prop("disabled", true);
    }

    cleanTopic(){
        appVue.topic = null,
        $('#sel_topics').val('').trigger('change');
    }

    copyElement(){
        var requireElement = '';
        switch (appVue.type) {
            case 'subtopic':
                requireElement = 'Tema';
                appVue.destino_id = appVue.topic;
                break;

            case 'topic':
                requireElement = 'Curso';
                appVue.destino_id = appVue.course;
                break;

            case 'course':
                requireElement = 'Módulo';
                appVue.destino_id = appVue.module;
                break;

            case 'module':
                requireElement = 'Cuadrante';
                appVue.destino_id = appVue.cuadrante;
                break;

            case 'area':
                requireElement = '';
                appVue.destino_id = 0;
                break;
        
            default:
                appVue.destino_id = null;
                break;
        }
        if(appVue.destino_id != null){
            SGui.showWaiting(3000);
            axios.post(appVue.oData.copyRoute, {
                'origen_id': appVue.origen_id,
                'destino_id': appVue.destino_id,
                'type': appVue.type,
            })
            .then(response => {
                var data = response.data;
                if(data.Success){
                    SGui.showOk();
                    window.location.reload(true)
                }else{
                    SGui.showMessage("Error", data.message, 'error');
                }
                appVue.disabledCopy = false;
            })
            .catch(function(error) {
                console.log(error);
                SGui.showMessage("Error", '', 'error');
                appVue.disabledCopy = false;
            });
        }else{
            SGui.showMessage("Error", "Debe seleccionar un " + requireElement + " de destino", 'error');
            appVue.disabledCopy = false;
        }
    }
}