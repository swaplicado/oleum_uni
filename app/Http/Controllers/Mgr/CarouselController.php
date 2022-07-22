<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\Carousel;
use App\Uni\EduContent;

class CarouselController extends Controller
{
    public function __construct() {
        $this->newRoute = 'carousel.create';
        $this->storeRoute = 'carousel.store';
        $this->editRoute = 'carousel.edit';
        $this->deleteRoute = 'carousel.delete';
        $this->updateRoute = 'carousel.update';
    }

    public function index()
    {
        $lCarousel = \DB::table('uni_carousel AS c')
                            ->leftJoin('uni_edu_contents AS ec', 'c.content_n_id', '=', 'ec.id_content')
                            ->where('c.is_deleted', false)
                            ->get();

        return view('mgr.carousel.index')->with('lElements', $lCarousel)
                                            ->with('title', 'Carrusel')
                                            ->with('newRoute', $this->newRoute)
                                            ->with('deleteRoute', $this->deleteRoute)
                                            ->with('editRoute', $this->editRoute);
    }

    public function create()
    {
        $videos = EduContent::where('file_type', 'video')->where('is_deleted', false)->get();

        foreach ($videos as $video) {
            $url = asset($video->file_path);
            $path = str_replace("public", "", $url);
            $path = str_replace("storage", "storage/app", $path);

            $video->path = $path;
        }

        return view('mgr.carousel.create')->with('title', 'Carrusel')
                                            ->with('videos', $videos)
                                            ->with('storeRoute', $this->storeRoute);
    }

    public function store(Request $request)
    {
        $oCarousel = new Carousel();

        if ($request->element_type == 'i') {
            $imageName = "";
            if($request->hasfile('img'))
            {
                $img = $request->file('img');
                $name = str_replace(" ", "-", $img->getClientOriginalName());
    
                // you can also use the original name
                $imageName = time().'-'.$name;
                $imageField = 'images/carousel/'.$imageName;
                // Upload file to public path in images directory
    
                $img->move(public_path('images/carousel'), $imageName);
            }
    
            $oCarousel->title = is_null($request->title) ? "" : $request->title;
            $oCarousel->text = is_null($request->text) ? "" : $request->text;
            $oCarousel->text_color = $request->text_color;
            $oCarousel->image = $imageField;
            $oCarousel->is_active = isset($request->is_active);
            $oCarousel->is_deleted = false;
            $oCarousel->content_n_id = null;
        }
        else {
            $oCarousel->title = "";
            $oCarousel->text = "";
            $oCarousel->text_color = "";
            $oCarousel->image = "";
            $oCarousel->is_active = isset($request->is_active);
            $oCarousel->is_deleted = false;
            $oCarousel->content_n_id = $request->id_content;
        }

        $oCarousel->url = $request->link != null ? $request->link : "#";
        $oCarousel->created_by_id = \Auth::id();
        $oCarousel->updated_by_id = \Auth::id();

        $oCarousel->save();

        return redirect()->route('carousel.index')->with("success", "El elemento se agregó con éxito.");
    }

    public function edit($idSlide)
    {
        $oCarousel = Carousel::find($idSlide);

        $image = $oCarousel->image;

        $videos = EduContent::where('file_type', 'video')->where('is_deleted', false)->get();

        foreach ($videos as $video) {
            $url = asset($video->file_path);
            $path = str_replace("public", "", $url);
            $path = str_replace("storage", "storage/app", $path);

            $video->path = $path;
        }

        return view('mgr.carousel.edit')->with('title', 'Carrusel')
                                            ->with('oCarousel', $oCarousel)
                                            ->with('image', $image)
                                            ->with('videos', $videos)
                                            ->with('updateRoute', $this->updateRoute);
    }

    public function update(Request $request)
    {
        if ($request->element_type == 'i') {
            $imageName = "";
            if($request->hasfile('img'))
            {
                $img = $request->file('img');
                $name = str_replace(" ", "-", $img->getClientOriginalName());
    
                // you can also use the original name
                $imageName = time().'-'.$name;
                $imageField = 'images/carousel/'.$imageName;
                // Upload file to public path in images directory
    
                $img->move(public_path('images/carousel'), $imageName);
            }

            if (isset($imageField)) {
                $image = $imageField;
            }

            $title = $request->title;
            $text = $request->text;
            $text_color = $request->text_color;
            $content_n_id = null;
        }
        else {
            $title = "";
            $text = "";
            $text_color = "";
            $image = "";
            $content_n_id = $request->id_content;
        }

        $fields = [
            'title' => $request->title,
            'text' => $request->text,
            'text_color' => $request->text_color,
            'url' => $request->link != null ? $request->link : "#",
            'is_active' => isset($request->is_active),
            'content_n_id' => $content_n_id,
            'updated_by_id' => \Auth::id()
        ];

        if (isset($image)) {
            $fields['image'] = $image;
        }

        Carousel::where('id_slide', $request->id_slide)->update($fields);

        return redirect()->route('carousel.index')->with("success", "Se actualizó con éxito.");
    }

    public function delete($idSlide)
    {
        $res = Carousel::where('id_slide', $idSlide)->update([
                                                            'is_deleted' => true
                                                        ]);

        return json_encode($res);
    }
}
