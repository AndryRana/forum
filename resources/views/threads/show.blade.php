@extends('layouts.app')

@section('header')

    <link href="/css/vendor/jquery.atwho.css" rel="stylesheet">

    <script>
        window.thread = <?= json_encode($thread); ?>
    </script>
    
@endsection

@section('content')
    <thread-view :thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row ">
                <div class="col-md-8" v-cloak>
                    
                    @include('threads._question')

                    <replies 
                        @added="repliesCount++"
                        @removed="repliesCount--"></replies>
                  
                 
                </div>

                <div class="col-md-4">
                    <div class="card mb-3">
                    
                        <div class="card-body">
                            <p>
                                This thread was published the {{ $thread->created_at->format('d/m/Y H:m' ) }} by
                                <a href="#">{{ $thread->creator->name }}</a>, and currently
                                has <span v-text="repliesCount"></span> {{ Str::plural('comment', $thread->replies_count) }} .
                            </p>

                            <p>
                                <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}" v-if="signedIn"></subscribe-button>

                                <button class="btn btn-outline-info" v-if="authorize('isAdmin')" @click="toggleLock" v-text=" locked ? 'Unlock' : 'Lock'"></button>

                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </thread-view>
@endsection