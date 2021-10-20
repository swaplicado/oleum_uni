@extends('layouts.appuni')

@section('content')
    @section('content_title', 'Intercambio de premios')
    @section('title_comp')
        Puntos disponibles: <span style="font-size: 110%" class="badge bg-primary">{{ $oPoints != null ? $oPoints->points : 0 }}</span>
    @endsection
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                @foreach ($lStock as $stock)
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    @for ($i = 0; $i < count($stock->lImages); $i++)
                                        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}" aria-current="{{ $i == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i+1 }}"></button>
                                    @endfor
                                </div>
                                <div class="carousel-inner">
                                    @for ($i = 0; $i < count($stock->lImages); $i++)
                                        <div class="carousel-item {{ $i == 0 ? 'active' : '' }}" data-bs-interval="5000">
                                            <img src="{{ asset($stock->lImages[$i]) }}" class="d-block w-100" alt="...">
                                            <div class="carousel-caption d-none d-md-block">
                                            {{-- <h5>First slide label</h5>
                                            <p>Some representative placeholder content for the first slide.</p> --}}
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                  <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                  <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                            <div class="card-body">
                            <h5 class="card-title">{{ $stock->gift }}</h5>
                            <p class="card-text">{{ $stock->description }}</p>
                            <h6>Puntos: <b style="color: blue">{{ $stock->points_value }}</b></h6>
                            <p>Disponibles: <b style="color: brown">{{ number_format($stock->d_stk, 0)  }}</b></p>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div align="center" class="col-12">
                                        <form action="{{ route('shop.exchange') }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <input type="hidden" name="id_gift" value="{{ $stock->gift_id }}">
                                            <button type="submit" class="btn btn-info">
                                                Canjear Premio
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    
@endsection