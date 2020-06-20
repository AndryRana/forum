<template>
        <div :id="'reply-'+id" class="card mb-3">
            <div class="card-header" :class="isBest ? 'bg-success': 'bg-light'">
                <div class="level">
                    <h5 class="flex">
                        <a :href="'/profiles/'+reply.owner.name"
                         v-text="reply.owner.name">
                        </a>
                        said <span v-text="ago"></span>
                    </h5>

                        <div v-if="signedIn">
                            <favorite :reply="reply"></favorite>
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

            <div class="card-footer level" v-if="authorize('owns', reply) || authorize('owns', thread)">
                <div v-if="authorize('owns', reply)">
                    <button class="btn btn-outline-secondary btn-sm mr-1" @click="editing = true">Edit</button>
                    <button class="btn btn-outline-danger  btn-sm mr-1" @click="destroy">Delete</button>
                </div>

                <button class="btn btn-outline-primary  btn-sm ml-auto" @click="markBestReply" v-if="authorize('owns', thread)">Best Reply?</button>
            </div>
        </div>
    <!-- </reply> -->
</template>
    

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        
        props: ['reply'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                // isBest: this.data.isBest,
                thread: window.thread
            };
        },

        computed: {
            isBest() {
                return this.thread.best_reply_id == this.id;
            },

            ago() {
                return moment(this.reply.created_at).fromNow() + '...';
            }

            

            // canUpdate() {
            //    return this.authorize(user => this.data.user_id == user.id);
            //     // return this.data.user_id == window.App.user.id;
            // },

            // created () {
            //     window.events.$on('best-reply-selected', id => {
            //     this.isBest = (id === this.id);
            // });
            // }
        },
        methods: {
            update() {
                axios.patch('/replies/' + this.reply.id, {
                    body: this.body
                })
                .catch(error => {
                    flash(error.response.data, 'danger');
                });

                this.editing = false;

                flash('Updated!');
            },

            destroy() {
                axios.delete('/replies/' + this.reply.id);
                
                this.$emit('deleted', this.reply.id);
             
            },

            markBestReply() {
                // axios.post('/replies/' + this.reply.id + '/best');

                // window.events.$emit('best-reply-selected', this.id);
                axios.post('/replies/' + this.id + '/best');
                this.thread.best_reply_id = this.id;
            }
        }
    }
</script>