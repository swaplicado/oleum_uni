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
        SelElement: null,
        lElement: null,
        element: null,
        SelNivel: null,
        lNivel: null,
        nivel: null
    },
    methods: {
       element_type(){
            switch (this.SelElement) {
                case 'competencia':
                    this.lElement = this.lAreas;
                    break;
                case 'modulo':
                    this.lElement = this.lModules;
                    break;
                case 'curso':
                    this.lElement = this.lCourses;
                    break;
                case 'tema':
                    this.lElement = this.lTopics;
                    break;
                case 'subtema':
                    this.lElement = this.lSubtopics;
                    break;
                case 'todo':
                    this.lElement = [{id:0, name:"todo"}];
                    break;
                default:
                    break;
            }
            console.log(this.lElement);
       },
       level_type(){
            switch (this.SelNivel) {
                case 'organizacion':
                    this.lNivel = this.lOrganizations;
                    break;
                case 'empresa':
                    this.lNivel = this.lCompany;
                    break;
                case 'sucursal':
                    this.lNivel = this.lBranches;
                    break;
                case 'departamento':
                    this.lNivel = this.lDepartments;
                    break;
                case 'puesto':
                    this.lNivel = this.lJobs;
                    break;
                case 'estudiante':
                    this.lNivel = this.lStudent;
                    break;
                default:
                    break;
            }
            console.log(this.lElement);
       }
    }
})