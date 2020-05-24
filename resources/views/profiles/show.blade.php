@extends('layouts.app')

@section('content')
<div class="container d-flex h-100">
    <div class="row align-self-center w-100 ">
        <div class="col-md-8 col-md-offset-2  mx-auto">

            <div class="pb-2 mt-4 mb-2 border-bottom">
                <h1>
                    {{ $profileUser->name }}
                </h1>
            </div>
        
            @forelse ($activities as $date => $activity)
                <div class="pb-2 mt-4 mb-2 border-bottom">
                    <h3>{{ $date }} </h3>
                </div>
            
                @foreach ($activity as $record)
                    @if (view()->exists("profiles.activities.{$record->type}"))
                        @include("profiles.activities.{$record->type}", ['activity' => $record])
                    @endif
                @endforeach
            @empty
                <p>There is no activity for this user yet</p>
            @endforelse
                {{-- {{ $threads->links() }} --}}
        </div>
    </div>

</div>
@endsection
