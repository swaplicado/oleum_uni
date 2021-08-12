<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\Carousel;

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
        $lCarousel = Carousel::where('is_deleted', false)->get();

        return view('mgr.carousel.index')->with('lElements', $lCarousel)
                                            ->with('title', 'Carrusel')
                                            ->with('newRoute', $this->newRoute)
                                            ->with('deleteRoute', $this->deleteRoute)
                                            ->with('editRoute', $this->editRoute);
    }

    public function create()
    {
        return view('mgr.carousel.create')->with('title', 'Carrusel')
                                            ->with('storeRoute', $this->storeRoute);
    }

    public function store(Request $request)
    {
        $oCarousel = new Carousel();

        $imageName = "";
        if($request->hasfile('img'))
        {
            $img = $request->file('img');
            $name = $img->getClientOriginalName();

            // you can also use the original name
            $imageName = time().'-'.$img->getClientOriginalName();
            $imageField = 'images/carousel/'.time().'-'.$img->getClientOriginalName();
            // Upload file to public path in images directory

            $img->move(public_path('images/carousel'), $imageName);
        }

        $oCarousel->title = $request->title;
        $oCarousel->text = $request->text;
        $oCarousel->text_color = $request->text_color;
        $oCarousel->url = $request->link;
        $oCarousel->image = $imageField;
        $oCarousel->is_active = isset($request->is_active);
        $oCarousel->is_deleted = false;
        $oCarousel->created_by_id = \Auth::id();
        $oCarousel->updated_by_id = \Auth::id();

        $oCarousel->save();

        return redirect()->route('carousel.index')->with("success", "Se ha agregado con éxito");
    }

    public function edit($idSlide)
    {
        $oCarousel = Carousel::find($idSlide);

        $image = $oCarousel->image;

        return view('mgr.carousel.edit')->with('title', 'Carrusel')
                                            ->with('oCarousel', $oCarousel)
                                            ->with('image', $image)
                                            ->with('updateRoute', $this->updateRoute);
    }

    public function update(Request $request)
    {
        Carousel::where('id_slide', $request->id_slide)->update([
            'title' => $request->title,
            'text' => $request->text,
            'text_color' => $request->text_color,
            'url' => $request->link,
            'is_active' => isset($request->is_active),
        ]);

        return redirect()->route('carousel.index')->with("success", "Se ha actualizado con éxito");
    }

    public function delete($idSlide)
    {
        $res = Carousel::where('id_slide', $idSlide)->update([
                                                            'is_deleted' => true
                                                        ]);

        return json_encode($res);
    }
}
