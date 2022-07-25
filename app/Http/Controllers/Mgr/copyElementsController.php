<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\Topic;
use App\Uni\SubTopic;
use App\Uni\Question;
use App\Uni\Answer;
use App\Uni\ElementContent;

class copyElementsController extends Controller
{
    public function copyArea($orArea_id){
        $oldKa = KnowledgeArea::findOrFail($orArea_id);

        $newKa = new KnowledgeArea();
        $newKa->knowledge_area_title = $oldKa->knowledge_area_title;
        $newKa->knowledge_area = $oldKa->knowledge_area;
        $newKa->hash_id = hash('ripemd160', $oldKa->knowledge_area);
        $newKa->description = $oldKa->description;
        $newKa->objectives = $oldKa->objectives;
        $newKa->has_document = $oldKa->has_document;
        $newKa->is_deleted = 0;
        $newKa->elem_status_id = config('csys.elem_status.NEW');
        $newKa->sequence_id = $oldKa->sequence_id;
        $newKa->created_by_id = \Auth::id();
        $newKa->updated_by_id = \Auth::id();
        $newKa->save();

        $modules = Module::where([['knowledge_area_id', $oldKa->id_knowledge_area],['is_deleted', 0]])->get();

        foreach($modules as $m){
            $this->copyModule($m->id_module, $newKa->id_knowledge_area, true);
        }

        $this->copyContentVsElement('knowledge_area_n_id', $oldKa->id_knowledge_area, $newKa->id_knowledge_area);
    }

    public function copyModule($orModule_id, $destArea_id, $is_child = false){
        if(!$is_child){
            $oldModule = Module::findOrFail($orModule_id);
        }else{
            $oldModule = Module::where([['id_module', $orModule_id], ['is_deleted', 0]])->first();
        }

        if(!is_null($oldModule)){
            $newModule = new Module();
            $newModule->module = $oldModule->module;
            $newModule->hash_id = hash('ripemd160', $newModule->module);
            $newModule->description = $oldModule->description;
            $newModule->objectives = $oldModule->objectives;
            $newModule->has_document = $oldModule->has_document;
            $newModule->is_deleted = 0;
            $newModule->knowledge_area_id = $destArea_id;
            $newModule->elem_status_id = config('csys.elem_status.NEW');
            $newModule->sequence_id = $oldModule->sequence_id;
            $newModule->created_by_id = \Auth::id();
            $newModule->updated_by_id = \Auth::id();
            $newModule->save();

            $courses = Course::where([['module_id', $oldModule->id_module], ['is_deleted', 0]])->get();

            foreach($courses as $c){
                $this->copyCourse($c->id_course, $newModule->id_module, true);
            }

            $this->copyContentVsElement('module_n_id', $oldModule->id_module, $newModule->id_module);
        }
    }

    public function copyCourse($orCourse_id, $destModule_id, $is_child = false){
        if(!$is_child){
            $oldCourse = Course::findOrFail($orCourse_id);
        }else{
            $oldCourse = Course::where([['id_course', $orCourse_id],['is_deleted', 0]])->first();
        }

        if(!is_null($oldCourse)){
            $newCourse =  new Course();
            $newCourse->course = $oldCourse->course;
            $newCourse->course_key = $oldCourse->course_key;
            $newCourse->hash_id = hash('ripemd160', $oldCourse->course);
            $newCourse->completion_days = $oldCourse->completion_days;  
            $newCourse->has_points = $oldCourse->has_points;
            $newCourse->university_points = $oldCourse->university_points;
            $newCourse->description = $oldCourse->description;
            $newCourse->objectives = $oldCourse->objectives;
            $newCourse->has_document = $oldCourse->has_document;
            $newCourse->is_deleted = 0;
            $newCourse->module_id = $destModule_id;
            $newCourse->elem_status_id = config('csys.elem_status.NEW');
            $newCourse->sequence_id = $oldCourse->sequence_id;
            $newCourse->created_by_id = \Auth::id();
            $newCourse->updated_by_id = \Auth::id();
            $newCourse->save();

            $topics = Topic::where([['course_id', $oldCourse->id_course], ['is_deleted', 0]])->get();

            foreach($topics as $t){
                $this->copyTopic($t->id_topic, $newCourse->id_course, true);
            }

            $this->copyContentVsElement('course_n_id', $oldCourse->id_course, $newCourse->id_course);

        }

    }

    public function copyTopic($orTopic_id, $destCourse_id, $is_child = false){
        if(!$is_child){
            $oldTopic = Topic::findOrFail($orTopic_id);
        }else{
            $oldTopic = Topic::where([['id_topic', $orTopic_id],['is_deleted', 0]])->first();
        }

        if(!is_null($oldTopic)){
            $newTopic = new Topic();
            $newTopic->topic = $oldTopic->topic;
            $newTopic->hash_id = hash('ripemd160', $oldTopic->topic);
            $newTopic->is_deleted = false;
            $newTopic->course_id = $destCourse_id;
            $newTopic->sequence_id = $oldTopic->sequence_id;
            $newTopic->created_by_id = \Auth::id();
            $newTopic->updated_by_id = \Auth::id();
            $newTopic->save();
    
            $subTopics = SubTopic::where([['topic_id', $oldTopic->id_topic], ['is_deleted', 0]])->get();
    
            foreach($subTopics as $st){
                $this->copySubtopic($st->id_subtopic, $newTopic->id_topic, true);
            }

            $this->copyContentVsElement('topic_n_id', $oldTopic->id_topic, $newTopic->id_topic);

        }

        return true;
    }

    public function copySubtopic($orSubtopic_id, $destTopic_id, $is_child = false){
        if(!$is_child){
            $oldSubtopic = SubTopic::findOrFail($orSubtopic_id);
        }else{
            $oldSubtopic = SubTopic::where([['id_subtopic', $orSubtopic_id], ['is_deleted', 0]])->first();
        }

        if(!is_null($oldSubtopic)){
            $newSubtopic = new SubTopic();
            $newSubtopic->subtopic = $oldSubtopic->subtopic;
            $newSubtopic->hash_id = hash('ripemd160', $oldSubtopic->subtopic);
            $newSubtopic->number_questions = $oldSubtopic->number_questions;
            $newSubtopic->is_deleted = false;
            $newSubtopic->topic_id = $destTopic_id;
            $newSubtopic->created_by_id = \Auth::id();
            $newSubtopic->updated_by_id = \Auth::id();
            $newSubtopic->save();
    
            $oldQuestions = \DB::table('uni_questions as q')
                            ->where([['subtopic_id', $orSubtopic_id], ['is_deleted', 0]])
                            ->get();
    
            $arr_oldQuestions = [];
            
            foreach($oldQuestions as $q){
                array_push($arr_oldQuestions, $q->id_question);
            }
    
            $oldAnswers = \DB::table('uni_answers as ans')
                            ->whereIn('question_id', $arr_oldQuestions)
                            ->get();
    
            foreach($oldQuestions as $q){
                $oldAnswers_oldQuestion = $oldAnswers->where('question_id', $q->id_question);
    
                $newQuestion = new Question();
                $newQuestion->question = $q->question;
                $newQuestion->number_answers = $q->number_answers;
                $newQuestion->answer_feedback = $q->answer_feedback;
                $newQuestion->is_deleted = false;
                $newQuestion->answer_id = $q->answer_id;
                $newQuestion->subtopic_id = $newSubtopic->id_subtopic;
                $newQuestion->created_by_id = \Auth::id();
                $newQuestion->updated_by_id = \Auth::id();
                $newQuestion->save();
    
                foreach($oldAnswers_oldQuestion as $aq){
                    $newAnswer = new Answer();
                    $newAnswer->answer = $aq->answer;
                    $newAnswer->is_deleted = $aq->is_deleted ;
                    $newAnswer->content_n_id = $aq->content_n_id;
                    $newAnswer->question_id = $newQuestion->id_question;
                    $newAnswer->save();
    
                    if($aq->id_answer == $q->answer_id){
                        $newQuestion->answer_id = $newAnswer->id_answer;
                        $newQuestion->update();
                    }
                }
            }

            $this->copyContentVsElement('subtopic_n_id', $oldSubtopic->id_subtopic, $newSubtopic->id_subtopic);
        }

        return true;
    }

    public function copyContentVsElement($type, $oldId, $newId){
        $oldElements = ElementContent::where($type, $oldId)->get();

        foreach($oldElements as $el){
            $elem = new ElementContent();
            $elem->order = $el->order;
            $elem->content_id = $el->content_id;
            $elem->element_type_id = $el->element_type_id;
            $elem->$type = $newId;
            $elem->created_by_id = \Auth::id();
            $elem->updated_by_id = \Auth::id();
            $elem->save();
        }

    }

    public function copyElement(Request $request){
        $success = null;
        $message = '';
        try {
            \DB::transaction(function () use ($request) {
                switch ($request->type) {
                    case 'subtopic':
                        $this->copySubtopic($request->origen_id, $request->destino_id);
                        break;

                    case 'topic':
                        $this->copyTopic($request->origen_id, $request->destino_id);
                        break;

                    case 'course':
                        $this->copyCourse($request->origen_id, $request->destino_id);
                        break;

                    case 'module':
                        $this->copyModule($request->origen_id, $request->destino_id);
                        break;

                    case 'area':
                        $this->copyArea($request->origen_id);
                        break;
                        
                    default:
                        break;
                }
            });
            $success = true;
            $message = 'elemento copiado con exito';
        } catch (\Throwable $th) {
            $success = false;
            $message = $th->getMessage();
        } catch (QueryException $qe) {
            $success = false;
            $message = $qe->errorInfo[2];
        } catch (\Exception $e) {
            $success = false;
            $message = $e->errorInfo[2];
        }
        return json_encode(['Success' => $success, 'message' => $message]);
    }

    public function getCuadrantes(){
        $lAreas = \DB::table('uni_knowledge_areas AS ka')
                    ->join('sys_element_status AS es', 'ka.elem_status_id', '=', 'es.id_element_status')
                    ->join('sys_sequences AS seq', 'ka.sequence_id', '=', 'seq.id_sequence')
                    ->select(['ka.id_knowledge_area',
                            'ka.knowledge_area_title',
                            'ka.knowledge_area',
                            'ka.description',
                            'ka.is_deleted',
                            'es.code AS status_code',
                            'seq.code AS seq_code',
                            ])
                    ->where('ka.is_deleted', 0)
                    ->get();

        return json_encode($lAreas);
    }
}
