{{-- Editing question --}}
<div class="card mb-3 " v-if="editing">
    <div class="card-header">

        <div class="level">

            <input type="text" class="form-control" v-model="form.title">
 

        </div>
    </div>

    <div class="card-body">
        <div class="form-group">
           <wysiwyg v-model="form.body" value="form.body"></wysiwyg>
        </div>
    </div>

    <div class="card-footer">
        <div class="level">
            <button type="submit" class="btn btn-outline-secondary btn-sm level-item" @click="editing = true" v-show="! editing">Edit</button>
            <button type="submit" class="btn btn-outline-secondary btn-sm level-item" @click="update">Update</button>
            <button type="submit" class="btn btn-outline-info btn-sm level-item" @click="cancel">Cancel</button>
    
            @can('update', $thread)
                <form action="{{ $thread->path() }}" method="POST" class="ml-auto">
                    @csrf
                    @method('DELETE')
        
                <button type="submit" class="btn btn-link">Delete Thread</button> 
                </form>
            @endcan   
        </div>
    </div>
</div>

{{-- Viewing the question --}}
<div class="card mb-3" v-else>
    <div class="card-header">

        <div class="level">
                
            <img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="25" height="25" class="mr-1">

            <span class="flex">
                <a href="{{ route('profile', $thread->creator) }}"> {{ $thread->creator->name }} </a> posted:
                <span v-text="title"></span>
            </span>
            
        </div>
    </div>

    <div class="card-body" v-html="body"></div>

    <div class="card-footer" v-if="authorize('owns', thread)">
        <button type="submit" class="btn btn-outline-secondary btn-sm" @click="editing = true">Edit</button>
    </div>
</div>