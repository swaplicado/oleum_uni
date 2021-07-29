@extends('layouts.appuni')

@section('content')
    @section('content_title', 'Cambiar avatar')
    <div class="row">
        @foreach ($images as $image)
        <div class="card col-6 col-md-2">
            <img src="{{ asset($image->route) }}" width="100%" height="100%" class="rounded mx-auto d-block" alt="">
            <div class="card-body">
                <form action="{{ route('update.avatar') }}" method="post">
                    @method('POST')
                    @csrf
                    <input type="hidden" name="image_path" value="{{ $image->route }}">
                    <button type="submit" class="btn btn-primary">Seleccionar</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
@endsection