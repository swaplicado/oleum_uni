<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\ElementContent;
use App\Uni\EduContent;
use App\Uni\SubTopic;

class ElementsContentsController extends Controller
{
     /**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function subtopics(Request $request, $idTopic = 0, $idSubtopic = 0)
    {
        $lElementContents = \DB::table('uni_contents_vs_elements AS ce')
                    ->join('uni_edu_contents AS c', 'ce.content_id', '=', 'c.id_content')
                    ->join('uni_subtopics AS s', 'ce.element_id', '=', 's.id_subtopic')
                    ->where('ce.element_type_id', 5)
                    ->where('ce.element_id', $idSubtopic)
                    ->get();

        $lContents = EduContent::where('is_deleted', false)
                            ->select('file_name', 'id_content')
                            ->get();

        $oSubtopic = SubTopic::find($idSubtopic);

        return view("mgr.topics.subtopics.contents")->with('title', "Contenidos de subtema")
                                                    ->with('newRoute', "subtopics.contents.index")
                                                    ->with('sGetRoute', 'contents.preview')
                                                    ->with('storeRoute', 'subtopics.contents.store')
                                                    ->with('oSubtopic', $oSubtopic)
                                                    ->with('idTopic', $idTopic)
                                                    ->with('idSubtopic', $idSubtopic)
                                                    ->with('lContents', $lContents)
                                                    ->with('lElementContents', $lElementContents);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $elem = new ElementContent();

        $elem->order = $request->order;
        $elem->content_id = $request->content;
        $elem->element_type_id = 5;
        $elem->element_id = $request->subtopic;
        $elem->created_by_id = \Auth::id();
        $elem->updated_by_id = \Auth::id();

        $elem->save();

        $lElementContents = \DB::table('uni_contents_vs_elements AS ce')
                    ->join('uni_edu_contents AS c', 'ce.content_id', '=', 'c.id_content')
                    ->join('uni_subtopics AS s', 'ce.element_id', '=', 's.id_subtopic')
                    ->where('ce.element_type_id', 5)
                    ->where('ce.id', $elem->id)
                    ->take(1)
                    ->get();

        return json_encode($lElementContents[0]);
    }
}
