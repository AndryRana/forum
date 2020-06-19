<template>
        <div :id="'reply-'+id" class="card mb-3">
            <div class="card-header" :class="isBest ? 'bg-success': 'bg-light'">
                <div class="level">
                    <h5 class="flex">
                        <a :href="'/profiles/'+data.owner.name"
                         v-text="data.owner.name">
                        </a>
                        said <span v-text="ago"></span>
                    </h5>

                        <div v-if="signedIn">
                            <favorite :reply="data"></favorite>
                        </div>

                </div>
            </div>

            <div class="card-body">
                
                <div v-if="editing">
                    <form @submit="update">
                        <div class="form-group">
                            <textarea class=" form-control" v-model="body" required> </textarea>
                        </div>
                        
                        <button class="btn btn-sm btn-outline-primary" >Update</button>
                        <button class="btn btn-sm btn-outline-secondary" @click="editing = false" type="button">Cancel </button>
                    </form>

                </div>
                <div v-else v-html="body"></div>

            </div>

            <div class="card-footer level" v-if="canUpdate">
                <div v-if="canUpdate">
                    <button class="btn btn-outline-secondary btn-sm mr-1" @click="editing = true">Edit</button>
                    <button class="btn btn-outline-danger  btn-sm mr-1" @click="destroy">Delete</button>
                </div>

                <button class="btn btn-outline-primary  btn-sm ml-auto" @click="markBestReply" v-show="! isBest">Best Reply?</button>
            </div>
        </div>
    <!-- </reply> -->
</template>
    

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        
        props: ['data'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body,
                // isBest: this.data.isBest,
                thread: window.thread
            };
        },

        computed: {
            isBest() {
                return this.thread.best_reply_id == this.id;
            },

            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            },

            signedIn() {
                return window.App.signedIn;
            },

            canUpdate() {
               return this.authorize(user => this.data.user_id == user.id);
                // return this.data.user_id == window.App.user.id;
            },

            // created () {
            //     window.events.$on('best-reply-selected', id => {
            //     this.isBest = (id === this.id);
            // });
            // }
        },
        methods: {
            update() {
                axios.patch('/replies/' + this.data.id, {
                    body: this.body
                })
                .catch(error => {
                    flash(error.response.data, 'danger');
                });

                this.editing = false;

                flash('Updated!');
            },

            destroy() {
                axios.delete('/replies/' + this.data.id);
                
                this.$emit('deleted', this.data.id);
             
            },

            markBestReply() {
                // axios.post('/replies/' + this.data.id + '/best');

                // window.events.$emit('best-reply-selected', this.id);
                axios.post('/replies/' + this.id + '/best');
                this.thread.best_reply_id = this.id;
            }
        }
    }
</script>