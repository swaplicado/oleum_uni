<div class="row">
    <div class="col-12">
        <div id="homeCarousel" data-ride="carousel" class="carousel slide" data-bs-ride="carousel" data-interval="500000">
            <div class="carousel-indicators">
                @for ($i = 0; $i < count($lCarousel); $i++) <button type="button" data-bs-target="#homeCarousel"
                    data-bs-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}"
                    aria-current="{{ $i == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i+1 }}"></button>
                    @endfor
            </div>
            <div class="carousel-inner">
                @for ($i = 0; $i < count($lCarousel); $i++) 
                    <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                        <a href="{{ $lCarousel[$i]->url }}" target="_blank">
                            @if ($lCarousel[$i]->content_n_id == null)
                                <img src="{{ asset($lCarousel[$i]->image) }}" class="d-block w-100" alt="">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 style="color: {{ $lCarousel[$i]->text_color }}"><b>{{ $lCarousel[$i]->title }}</b></h5>
                                    <p style="color: {{ $lCarousel[$i]->text_color }}">{{ $lCarousel[$i]->text }}</p>
                                </div>
                            @else
                                <video controls="" name="media" class="carousel-video">
                                    <source id="idSource" src="{{ $lCarousel[$i]->path }}" type="video/mp4">
                                </video>
                            @endif
                        </a>
                    </div>
                @endfor
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
</div>