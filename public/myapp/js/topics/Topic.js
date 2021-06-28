class Topic {

    constructor() {
        this.id_topic = 0;
        this.topic = "";
        this.hash_id = "";
        this.is_deleted = false;
        this.course_id = 0;
        this.secuence_id = 1

        this.lSubtopics = [];
    }
}

class SubTopic {

    constructor() {
        this.id_subtopic = 0;
        this.subtopic = "";
        this.hash_id = "";
        this.number_questions = 0;
        this.is_deleted = false;
        this.topic_id = 0;
    }
}