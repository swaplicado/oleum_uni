var app = new Vue({
    el: '#editGeneral',
    data: {
        lAreas: oGlobalData.lAreas,
        lModules: [],
        lCourses: [],
        lTopics: [],
        lSubtopics: [],
        modulesRoute: oGlobalData.modulesRoute,
        coursesRoute: oGlobalData.coursesRoute,
        topicsRoute: oGlobalData.topicsRoute,
        subtopicsRoute: oGlobalData.subtopicsRoute,
        routeTopic: oGlobalData.routeTopic,
        routeCourse: oGlobalData.routeCourse,
        routeModule: oGlobalData.routeModule,
        routeArea: oGlobalData.routeArea,
        area: null,
        module: null,
        course: null,
        topic: null,
        subtopic: null,
    },
    mounted(){
        let self = this;
        var cuadrantes = [];
        cuadrantes.push({id: '', text: ''});
        for(var i = 0; i<self.lAreas.length; i++){
            cuadrantes.push({id: self.lAreas[i].id_knowledge_area, text: self.lAreas[i].knowledge_area});
        }

        $('#sel_modules_edit').select2({
            placeholder: 'selecciona modulo',
            data: [],
        }).prop("disabled", true);;

        $('#sel_courses_edit').select2({
            placeholder: 'selecciona curso',
            data: [],
        }).prop("disabled", true);;

        $('#sel_topics_edit').select2({
            placeholder: 'selecciona tema',
            data: [],
        }).prop("disabled", true);;

        // $('#sel_subtopics').select2({
        //     placeholder: 'selecciona subtema',
        //     data: [],
        // });

        $('#sel_cuadrante_edit')
            .select2({
                placeholder: 'selecciona cuadrante',
                data: cuadrantes,
            })
            .on('select2:select', function (e){
                self.area = e.params.data.id;
                self.getModules(e.params.data.id, self.modulesRoute);
            });

        $('#sel_modules_edit').on('select2:select', function (e){
            self.module = e.params.data.id;
            self.getCourses(e.params.data.id, self.coursesRoute);
        });

        $('#sel_courses_edit').on('select2:select', function (e){
            self.course = e.params.data.id;
            self.getTopics(e.params.data.id, self.topicsRoute);
        });

        $('#sel_topics_edit').on('select2:select', function (e){
            self.topic = e.params.data.id;
            // self.getSubtopics(e.params.data.id, self.subtopicsRoute);
        });

        // $('#sel_subtopics').on('select2:select', function (e){
        //     self.subtopic = e.params.data.id;
        // });
    },
    methods: {
        showEditGeneral(){
            this.area = null,
            this.module = null,
            this.course = null,
            this.topic = null,
            // this.subtopic = null,
            $('#sel_cuadrante_edit').val('').trigger('change');
            $('#sel_modules_edit').empty().prop("disabled", true);
            $('#sel_courses_edit').empty().prop("disabled", true);
            $('#sel_topics_edit').empty().prop("disabled", true);
            // $('#sel_subtopics').empty().prop("disabled", true);
            $('#modalEditGeneral').modal('show');
        },

        setModules(){
            this.module = null,
            this.course = null,
            this.topic = null,

            $('#sel_courses_edit').empty().prop("disabled", true);
            $('#sel_topics_edit').empty().prop("disabled", true);
            $('#sel_modules_edit').empty();
            $('#sel_modules_edit').select2({
                dropdownParent: $('#modalEditGeneral'),
                placeholder: 'selecciona modulo',
                data: self.lModules,
            }).prop("disabled", false);
        },

        setCourses(){
            this.course = null,
            this.topic = null,

            $('#sel_topics_edit').empty().prop("disabled", true);
            $('#sel_courses_edit').empty();
            $('#sel_courses_edit').select2({
                dropdownParent: $('#modalEditGeneral'),
                placeholder: 'selecciona curso',
                data: self.lCourses,
            }).prop("disabled", false);
        },

        setTopics(){
            $('#sel_topics_edit').empty();
            $('#sel_topics_edit').select2({
                dropdownParent: $('#modalEditGeneral'),
                placeholder: 'selecciona tema',
                data: self.lTopics,
            }).prop("disabled", false);;
        },

        setSubtopics(){
            $('#sel_subtopics').empty();
            $('#sel_subtopics').select2({
                dropdownParent: $('#modalEditGeneral'),
                placeholder: 'selecciona subtema',
                data: self.lSubtopics,
            }).prop("disabled", false);;
        },

        getModules(ka_id, modulesRoute){
            axios.post(modulesRoute, {
                'ka': ka_id,
            })
            .then(response => {
                self.lModules = response.data;
                this.setModules();
            })
            .catch(function(error) {
                console.log(error);
            });
        },

        getCourses(mo_id, coursesRoute){
            axios.post(coursesRoute, {
                'mo': mo_id,
            })
            .then(response => {
                self.lCourses = response.data;
                this.setCourses();
            })
            .catch(function(error) {
                console.log(error);
            });
        },

        getTopics(course_id, topicsRoute){
            axios.post(topicsRoute, {
                'course': course_id,
            })
            .then(response => {
                self.lTopics = response.data;
                this.setTopics();
            })
            .catch(function(error) {
                console.log(error);
            });
        },

        getSubtopics(topic_id, subtopicsRoute){
            axios.post(subtopicsRoute, {
                'topic': topic_id,
            })
            .then(response => {
                self.lSubtopics = response.data;
                this.setSubtopics();
            })
            .catch(function(error) {
                console.log(error);
            });
        },

        cleanCuadrante(){
            this.area = null,
            this.module = null,
            this.course = null,
            this.topic = null,

            $('#sel_cuadrante_edit').val('').trigger('change');
            $('#sel_modules_edit').empty().prop("disabled", true);
            $('#sel_courses_edit').empty().prop("disabled", true);
            $('#sel_topics_edit').empty().prop("disabled", true);
        },

        cleanModule(){
            this.module = null,
            this.course = null,
            this.topic = null,

            $('#sel_modules_edit').val('').trigger('change');
            $('#sel_courses_edit').empty().prop("disabled", true);
            $('#sel_topics_edit').empty().prop("disabled", true);
        },

        cleanCourse(){
            this.course = null,
            this.topic = null,
            $('#sel_courses_edit').val('').trigger('change');
            $('#sel_topics_edit').empty().prop("disabled", true);
        },

        cleanTopic(){
            this.topic = null,
            $('#sel_topics_edit').val('').trigger('change');
        },

        getEdit(){
            if(this.topic != null && this.course != null){
                var url = this.routeTopic;
                url = url.replace(':course', this.course);
                window.open(url, '_blank');
                return;
            }

            if(this.course != null){
                var url = this.routeCourse;
                url = url.replace(':id', this.course);
                window.open(url, '_blank');
                return;
            }

            if(this.module != null && this.course == null){
                var url = this.routeModule;
                url = url.replace(':id', this.module);
                window.open(url, '_blank');
                return;
            }

            if(this.area != null && this.module == null){
                var url = this.routeArea;
                url = url.replace(':karea', this.area);
                window.open(url, '_blank');
                return;
            }
        }
    }
})