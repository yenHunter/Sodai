@extends("shared.base", ["title" => "Video Player"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Plugins", "title" => "Video Player"])

                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic MP4 Video Player</h4>
                            </div>
                            <div class="card-body">
                                <video class="plyr w-100" controls="" id="player1" playsinline="" poster="https://media.w3.org/2010/05/sintel/poster.png" style="--plyr-color-main: #1ac266">
                                    <source src="https://media.w3.org/2010/05/sintel/trailer_hd.mp4" type="video/mp4" />
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Autoplay (muted), Loop Video Player</h4>
                            </div>
                            <div class="card-body">
                                <video autoplay="" class="plyr w-100" controls="" id="player3" loop="" muted="" playsinline="" poster="https://media.w3.org/2010/05/sintel/poster.png" style="--plyr-color-main: #1c84c6">
                                    <source src="https://media.w3.org/2010/05/sintel/trailer_hd.mp4" type="video/mp4" />
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">YouTube Video Player</h4>
                            </div>
                            <div class="card-body">
                                <div class="plyr w-100" data-plyr-embed-id="bTqVqk7FSmY" data-plyr-provider="youtube" id="yt1" style="--plyr-color-main: #f8ac59"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Vimeo Video Player</h4>
                            </div>
                            <div class="card-body">
                                <div class="plyr w-100" data-plyr-embed-id="76979871" data-plyr-provider="vimeo" id="vimeo1" style="--plyr-color-main: #ed5565"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Audio Player</h4>
                            </div>
                            <div class="card-body">
                                <audio class="w-100" controls="" id="player-audio" style="--plyr-color-main: #7b70ef">
                                    <source src="https://cdn.plyr.io/static/demo/Kishi_Bashi_-_It_All_Began_With_a_Burst.mp3" type="audio/mp3" />
                                    <source src="https://cdn.plyr.io/static/demo/Kishi_Bashi_-_It_All_Began_With_a_Burst.ogg" type="audio/ogg" />
                                </audio>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include("shared.partials.footer")
        </div>
    </div>

    @include("shared.partials.customizer") @include("shared.partials.footer-scripts")
@endsection

@section("scripts")
    @vite(["resources/js/pages/plugins-plyr.js"])
@endsection
