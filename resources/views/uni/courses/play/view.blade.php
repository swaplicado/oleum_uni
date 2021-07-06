@extends('layouts.appuni')

@section('scripts_section')
    @include('uni.courses.play.section_js')
@endsection

@section('content')
    @section('content_title', 'Contenido '.$oSubtopic->subtopic)
    @section('right_header')
        <button class="btn btn-info">Iniciar Evaluación <i class='bx bxs-spreadsheet'></i></button>
    @endsection
    <div class="row" id="playApp">
        <div class="col-12">          
            <div class="row">
                <div class="col-1">
                    <button v-if="nContents > 0 && indexContent > 0" class="btn btn-primary" v-on:click="previous()"><i class='bx bx-arrow-to-right bx-rotate-180 bx-lg'></i></button>
                </div>
                <div class="col-10">
                    <div style="text-align:center;">
                        {{-- Vista previa de imagen --}}
                        <img v-if="fileType == 'image'" :src="fileUrl" alt="" style="vertical-align:middle;" width="80%" height="80%">
        
                        {{-- Vista previa de video --}}
                        <video id="idVideo" v-else-if="fileType == 'video'" controls="" autoplay="" name="media" width="80%" height="80%">
                            <source id="idSource" :src="fileUrl" type="video/mp4">
                        </video>
        
                        {{-- Vista previa de PDF --}}
                        <embed v-else-if="fileType == 'pdf'" :src="fileUrl" width="100%" height="840px" />
        
                        {{-- Vista previa de TXT --}}
                        <div v-else-if="fileType == 'text'" class="row">
                            <div class="col-md-12">
                                <textarea v-model="sText" class="form-control" name="" id="" cols="30" rows="10" readonly></textarea>
                            </div>
                        </div>
        
                        {{-- Previo audio --}}
                        <audio id="idAudio" v-else-if="fileType == 'audio'" controls>
                            <source :src="fileUrl" type="audio/mpeg">
                        </audio>
        
                        {{-- Previo file --}}
                        <a v-else-if="fileType == 'file'" :href="fileUrl" download>@{{ sFileName }} <i class='bx bxs-file-archive'></i></a>
                    </div>
                </div>
                <div class="col-1">
                    <button v-if="nContents > 0 && indexContent < (nContents - 1)" v-on:click="next()" class="btn btn-primary"><i class='bx bx-arrow-to-right bx-lg'></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/uni/VuePlay.js') }}"></script>
@endsection