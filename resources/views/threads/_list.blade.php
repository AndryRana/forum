@forelse ($threads as $thread)
<div class="card mb-3">
    <div class="card-header">

        <div class="level">

            <div class="flex">
                <h4>
                     <a href=" {{ $thread->path() }} ">

                     @if (auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                        <strong class=" bold">
                            {{ $thread->title }}
                        </strong>
                     @else
                           <i>{{ $thread->title }}</i>
                     @endif
                        
                    </a>
                </h4>

                <h5>
                    Publiée par: <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> le <small>{{ $thread->created_at->format('d/m/Y H:m' ) }}</small>
                </h5>
            </div>


            <a href="{{ $thread->path() }}">
                {{ $thread->replies_count }} {{ Str::plural('reply', $thread->replies_count) }}
            </a>
        </div>

    </div>

    <div class="card-body">
        {{-- @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif --}}
            <div class="body"> {!! $thread->body !!} </div>
    </div>
    <div class=" card-footer">
        {{ $thread->visits() }} Visits
    </div>
</div>
@empty
<p>There are no relevant results at this time</p>  
@endforelse