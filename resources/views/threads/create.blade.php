@extends('layouts.app')

@section('header')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create a new Thread</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <form method="POST" action="/threads">
                        @csrf

                        <div class="form-group">
                            <label for="channel_id">Choose a channel:</label>
                            <select class="form-control" name="channel_id" id="channel_id" required>
                                <option value="">Choose One...</option>

                                @foreach ($channels as $channel)
                                <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected': '' }} >
                                     {{ $channel->name }}
                                </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder=""
                                value=" {{ old('title') }} " required>
                        </div>

                        <div class="form-group">
                            <label for="body">Body:</label>
                            <wysiwyg name="body"></wysiwyg>
                        </div>

                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="6LcwK6gZAAAAAB7ksIKK9IIPi7yZ8NnKnKOVDxUa"></div>
                        </div>

                        <div class=" form-group">
                            <button type="submit" class="btn btn-outline-primary">Publish</button>
                        </div>
                        @if (count($errors))
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }} </li>
                            @endforeach
                        </ul>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection