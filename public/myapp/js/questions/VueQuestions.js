var app = new Vue({
    el: '#appQuestions',
    data: {
        oData: oServerData,
        oQuestion: new Question(),
        idPicked: "0"
    },
    methods: {
        /**
         * Despliega el modal para crear una nueva pregunta con sus respuestas
         */
        createQuestion() {
            this.oQuestion = new Question();
            this.oQuestion.subtopic_id = this.oData.idSubtopic;
            this.oQuestion.lAnswers = [];

            $('#questionModal').modal("show");
        },
        /**
         * Despliega el modal para editar una pregunta y sus respuestas
         */
        viewAnswers(questionId, nAnswers) {
            SGui.showWaiting(3000);

            axios
                .get(this.oData.sGetRoute, {
                    params: {
                        'question': questionId
                    }
                })
                .then(response => {
                    console.log(response);
                    let idAux = 465;
                    for (const answer of response.data.lAnswers) {
                        let d = new Date();
                        let t = d.getTime();

                        answer.id_aux = "" + idAux++;

                        if (response.data.answer_id == answer.id_answer) {
                            this.idPicked = answer.id_aux;
                        }
                    }

                    this.oQuestion = response.data;
                    $('#questionModal').modal('show');
                })
                .catch(err => {
                    console.log(err);
                    SGui.showError(err);
                });
        },
        discardAnswer(answer) {
            if (!answer.id_answer > 0) {
                var removeIndex = this.oQuestion.lAnswers.map(function(item) { return item.id_aux; }).indexOf(answer.id_aux);
                this.oQuestion.lAnswers.splice(removeIndex, 1);
                return;
            }

            /**
             * Petición al Controlador
             */
            axios.put(this.oData.delAnswerRoute, {
                    'answer': JSON.stringify(answer.id_answer)
                })
                .then(response => {
                    let res = response.data;
                    this.oQuestion.lAnswers.splice(this.oQuestion.lAnswers.indexOf(answer), 1);

                    SGui.showOk();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        /**
         * Determina si se realizará una inserción o un update en la BD
         */
        saveQuestion() {
            this.oQuestion.answer = this.idPicked;

            if (!this.validateQuestion()) {
                return;
            }

            SGui.showWaiting(10000);

            if (this.oQuestion.id_question > 0) {
                this.updateQuestion();
            } else {
                this.storeQuestion();
            }
        },
        deleteQuestion(question) {
            /**
             * Petición al Controlador
             */
            axios.put(this.oData.deleteQuestionRoute, {
                    'question': JSON.stringify(question.id_question)
                })
                .then(response => {
                    let res = response.data;
                    // this.oQuestion.lAnswers.splice(this.oQuestion.lAnswers.indexOf(answer), 1);

                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        /**
         * Valida el modal de captura de pregunta y respuestas
         */
        validateQuestion() {
            if (this.oQuestion.question.length == 0) {
                SGui.showError("La pregunta no debe estar vacía.");
                return false;
            }
            if (parseInt(this.oQuestion.number_answers) < 2) {
                SGui.showError("El número de respuestas debe ser al menos 2.");
                return false;
            }
            if (this.oQuestion.lAnswers.length < parseInt(this.oQuestion.number_answers)) {
                SGui.showError("El número de respuestas capturadas debe ser mayor o igual que el número de respuestas.");
                return false;
            }

            for (const ans of this.oQuestion.lAnswers) {
                if (ans.answer.length == 0) {
                    SGui.showError("Las respuestas no deben estar vacías.");
                    return false;
                }
            }

            if (!parseInt(this.idPicked) > 0) {
                SGui.showError("Debes elegir una respuesta correcta.");
                return false;
            }

            return true;
        },
        /**
         * Realiza la petición para la inserción a la BD
         */
        storeQuestion() {
            /**
             * Petición al Controlador
             */
            axios.post(this.oData.storeRoute, {
                    'question': JSON.stringify(this.oQuestion)
                })
                .then(response => {
                    let res = response.data;
                    this.oData.lQuestions.push(res);

                    $('#questionModal').modal('hide');

                    SGui.showOk();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        /**
         * Realiza la petición pára la actualización de la BD
         */
        updateQuestion() {
            /**
             * Petición al Controlador
             */
            axios.put(this.oData.updateRoute, {
                    'question': JSON.stringify(this.oQuestion)
                })
                .then(response => {
                    let res = response.data;
                    for (let question of this.oData.lQuestions) {
                        if (question.id_question == res.id_question) {
                            question = res;
                            break;
                        }
                    }

                    $('#questionModal').modal('hide');

                    SGui.showOk();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        /**
         * Crea una nueva respuesta, esto agrega un div en el modal para la captura de esta
         */
        newAnswer() {
            let oAnswer = new Answer();
            this.oQuestion.lAnswers.push(oAnswer);
        }
    },
})