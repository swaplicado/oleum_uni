var app = new Vue({
    el: '#examApp',
    data: {
        oData: oServerData,
        oQuestion: [],
        indexQuestion: 0,
        nQuestions: oServerData.lQuestions.length,
        picked: 0,
        bFeedback: false,
        bAnswered: false,
        sClassName: "",
        sMessage: "",
        lRecordAnswers: [],
        lSteps: []
    },
    mounted() {
        this.lSteps = [];

        let counter = 1;
        for (const question of this.oData.lQuestions) {
            let step = new Step();

            step.circle = counter++;
            step.idQuestion = question.id_question;
            step.text = "Pregunta " + step.circle;
            // step.class = 

            this.lSteps.push(step);
        }

        this.setQuestion();
        delete localStorage.questions;
    },
    methods: {
        next() {
            if (this.indexQuestion == (this.nQuestions - 1)) {
                return;
            }

            this.indexQuestion++;
            this.setQuestion();
        },
        setQuestion() {
            this.oQuestion = this.oData.lQuestions[this.indexQuestion];
            this.bAnswered = false;
            this.picked = 0;
            this.bFeedback = false;
            this.bAnswered = false;
            this.sClassName = "";
            this.sMessage = "";
        },
        checkAnswer(idAnswer) {
            if (this.bAnswered) {
                return;
            }

            let isCorrect = idAnswer == this.oQuestion.answer_id;
            this.bFeedback = !isCorrect;

            let oStep = null;
            for (const step of this.lSteps) {
                if (this.oQuestion.id_question == step.idQuestion) {
                    oStep = step;
                    break;
                }
            }

            if (isCorrect) {
                this.sClassName = "alert alert-success";
                this.sMessage = '¡La respuesta es CORRECTA!'
                oStep.class = 'step step-success';
            } else {
                this.sClassName = "alert alert-danger";
                this.sMessage = '¡La respuesta es INCORRECTA!'
                oStep.class = 'step step-error';
            }

            this.picked = idAnswer;
            this.bAnswered = true;

            this.recordAnswer(this.oQuestion.id_question, idAnswer, isCorrect);
        },
        async recordAnswer(idQuestion, idAnswer, isCorrect) {
            let questions = JSON.parse(localStorage.getItem('questions'));

            if (questions == undefined || questions.length == 0) {
                questions = [];
            }

            let question = { id_question: idQuestion, id_answer: idAnswer, is_correct: isCorrect };

            questions.push(question);

            localStorage.setItem('questions', JSON.stringify(questions));
        },
        async recordExam() {
            SGui.showWaiting(3000);

            await axios
                .post(this.oData.sRecordRoute, {
                    'questions': localStorage.getItem('questions')
                })
                .then(response => {
                    let res = response.data;
                    let route = "";
                    if (res.isApproved) {
                        SGui.showMessage("¡Felicidades!\n Aprobaste.", "Calificación: " + res.grade, 'success');
                        route = this.oData.sSuccessRoute;
                    } else {
                        SGui.showMessage("Lo siento, intenta de nuevo.", "Calificación: " + res.grade, 'error');
                        route = this.oData.sFailRoute;
                    }

                    location.href = route;
                })
                .catch(err => {
                    SGui.showError(err);
                });
        }
    }
})