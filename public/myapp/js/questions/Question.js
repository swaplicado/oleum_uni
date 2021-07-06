/**
 * Clase de Modelo para frontend
 */
class Question {

    constructor() {
        this.id_question = 0;
        this.question = "";
        this.number_answers = 2;
        this.answer_feedback = "";
        this.is_deleted = false;
        this.answer_id = 0;
        this.subtopic_id = 0;

        this.lAnswers = [];
    }

}

/**
 * Clase de Modelo para frontend
 */
class Answer {

    constructor() {
        this.id_answer = 0;
        this.answer = "";
        this.is_deleted = false;
        this.content_n_id = 0;
        this.question_id = 0;

        let d = new Date();
        let t = d.getTime();

        this.id_aux = "" + t;
    }

}