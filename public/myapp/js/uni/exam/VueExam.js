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
                this.sMessage = '¡Bien hecho! La respuesta es CORRECTA.'
                oStep.class = 'step step-success';
            } else {
                this.sClassName = "alert alert-danger";
                this.sMessage = 'Intenta de nuevo, la respuesta es INCORRECTA.'
                oStep.class = 'step step-error';
            }

            this.picked = idAnswer;
            this.bAnswered = true;

            this.recordAnswer(this.oQuestion.id_question, idAnswer, isCorrect);
        },
        recordAnswer(idQuestion, idAnswer, isCorrect) {
            let question = { id_question: idQuestion, id_answer: idAnswer, is_correct: isCorrect, take_evaluation: this.oData.takeEvaluation };

            axios
                .post(this.oData.sRecordRoute, {
                    'question': JSON.stringify(question)
                })
                .then(response => {
                    let res = response.data;
                })
                .catch(err => {
                    SGui.showError(err);
                });
        },
        recordExam() {
            SGui.showWaiting(3000);

            axios
                .post(this.oData.sRecordExam, {
                    'id_course': this.oData.oTopic.course_id,
                    'number_questions': this.nQuestions,
                    'take_evaluation': this.oData.takeEvaluation,
                    'take_subtopic': this.oData.idSubtopicTaken
                })
                .then(response => {
                    let res = response.data;
                    let route = "";
                    if (res.isApproved) {
                        if (res.oCompleted.course) {
                            SGui.showSuccess("¡Felicidades!\n Aprobaste el curso.",
                                "Calificación final: "  + res.oCompleted.grade + "\n" +
                                (res.oCompleted.has_points ? ("Ganaste: " + res.oCompleted.points + " puntos.") : ""),
                                'success', this.oData.sImageRoute);
                        } else {
                            SGui.showSuccess("¡Felicidades!\n Aprobaste.", "Calificación: " + res.grade, 'success', this.oData.sImageRoute);
                        }

                        route = this.oData.sSuccessRoute;
                    } else {
                        SGui.showMessage("Lo siento, intenta de nuevo.", "Calificación: " + res.grade, 'error');
                        route = this.oData.sFailRoute;
                    }

                    this.endExam(route);
                })
                .catch(err => {
                    SGui.showError(err);
                });
        },
        async endExam(route) {
            await SGui.sleep(10000);
            location.href = route;
        }
    }
})