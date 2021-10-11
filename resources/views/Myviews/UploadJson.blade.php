@extends('layouts.appuni')

@section('content')
    @section('content_title', 'ReadJson')

    <div class="row">
        <div class="col-12">
            <div class="card-body" style="border: solid, red, 5px;">
                <form method="POST" action="/json" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row col-4">
                        <label style="margin-top: 5px;">Upload Json Archive</label>
                        <input type="file" name="JsonFile">
                    </div>
                    <div class="form-group row col-4">
                        <div class="col-4" style="margin-top: 5px;">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                        <div class="col-8" style="margin-top: 5px;"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
