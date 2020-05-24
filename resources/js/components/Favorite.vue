<template>
  <button type="submit" :class="classes" @click="toggle">
    <span class=" fa fa-heart"></span>
    <span v-text="favoritesCount"></span> 
  </button>
</template>


<script>
export default {
    props:['reply'],

    data() {
        return {
            favoritesCount: this.reply.favoritesCount,
            isFavorited: this.reply.isFavorited
        }
    },
    
    computed: {
        classes() {
            return ['btn', this.isFavorited ? 'btn-outline-primary' : 'btn-outline-secondary'];
        },

        endpoint() {
            return '/replies/' + this.reply.id + '/favorites';

        }
    },

    methods: {
        toggle() {

            return this.isFavorited ? this.destroy() : this.create();
        },

        create() {
                axios.post(this.endpoint);

                this.isFavorited = true;
                this.favoritesCount++;
            
            },

        destroy() {
            
            axios.delete(this.endpoint);

            this.isFavorited = false;
            this.favoritesCount--;
        }
    }

}
</script>