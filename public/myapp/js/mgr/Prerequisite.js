class Prerequisite {
    constructor() {
        this.id_prerequisite = 0;
        this.is_deleted = 0;
        this.element_type_id = 0;
        this.knowledge_area_n_id = 0;
        this.module_n_id = 0;
        this.course_n_id = 0;
        this.topic_n_id = 0;
        this.subtopic_n_id = 0;
    }
}

class PrerequisiteRow {
    constructor() {
        this.id = 0;
        this.is_deleted = 0;
        this.prerequisite_id = 0;
        this.element_type_id = 0;
        this.knowledge_area_n_id = null;
        this.module_n_id = null;
        this.course_n_id = null;
        this.topic_n_id = null;
        this.subtopic_n_id = null;
    }
}