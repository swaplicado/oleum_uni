var app = new Vue({
    el: '#appReport',
    data: {
        lAreas: oServerData.lAreas,
        lModules: oServerData.lModules,
        lCourses: oServerData.lCourses,
        lTopics: oServerData.lTopics,
        lSubtopics: oServerData.lSubtopics,
        lOrganizations: oServerData.lOrganizations,
        lCompany: oServerData.lCompany,
        lBranches: oServerData.lBranches,
        lDepartments: oServerData.lDepartments,
        lJobs: oServerData.lJobs,
        lStudent: oServerData.lStudent,
        lElements: oServerData.lElements,
        SelElement: null,
        lElement: null,
        element: null,
        element_value: null,
        SelNivel: null,
        lNivel: oServerData.lNivel,
        nivel: null,
        level_name: null,
        level_value: null,
    },
    mounted() {
        let self = this;
        $('#tipo_elemento').select2({
            placeholder: '',
            data: self.lElements,
        });
        $('#type_level').select2({
            placeholder: '',
            data: self.lNivel,
        });
        $('#tipo_elemento').on('select2:select', function (e){
            self.SelElement = e.params.data.id;
            switch (self.SelElement) {
                case 'cuadrante':
                    self.element = "Cuadrante";
                    self.lElement = self.lAreas;
                    break;
                case 'modulo':
                    self.element = "Módulo";
                    self.lElement = self.lModules;
                    break;
                case 'curso':
                    self.element = "Curso";
                    self.lElement = self.lCourses;
                    break;
                case 'tema':
                    self.element = "Tema";
                    self.lElement = self.lTopics;
                    break;
                case 'subtema':
                    self.element = "Subtema";
                    self.lElement = self.lSubtopics;
                    break;
                case 'todo':
                    self.element = "Todo";
                    self.lElement = [{id:0, name:"todo"}];
                    break;
                default:
                    break;
                
            }
            $('#elemento').empty();
            $('#elemento')
                .select2({ 
                    placeholder: 'selecciona',
                    data: self.lElement,
                });
        });

        $('#type_level').on('select2:select', function (e){
            self.SelNivel = e.params.data.id;
            switch (self.SelNivel) {
                case 'estudiante':
                    self.level_name = "Estudiante";
                    self.nivel = self.lStudent;
                    break;
                case 'puesto':
                    self.level_name = "Puesto";
                    self.nivel = self.lJobs;
                    break;
                case 'departamento':
                    self.level_name = "Departamento";
                    self.nivel = self.lDepartments;
                    break;
                case 'sucursal':
                    self.level_name = "Sucursal";
                    self.nivel = self.lBranches;
                    break;
                case 'empresa':
                    self.level_name = "Empresa";
                    self.nivel = self.lCompany;
                    break;
                case 'organizacion':
                    self.level_name = "Organización";
                    self.nivel = self.lOrganizations;
                    break;
                default:
                    break;
                
            }
            $('#level').empty();
            $('#level')
                .select2({ 
                    placeholder: 'selecciona',
                    data: self.nivel,
                });
        });

        $('#elemento').on('select2:select', function (e){
            self.element_value = e.params.data.id;
        });

        $('#level').on('select2:select', function (e){
            self.level_value = e.params.data.id;
        })
    },
    methods: {
       
    }
})