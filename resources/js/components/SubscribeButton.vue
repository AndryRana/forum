<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>

    export default {
        props: ['active'],

        computed: {
            classes() {
                return ['btn' , this.active ? 'btn-outline-primary' : 'btn-outline-dark'];
            }
        },

        methods: {
            subscribe() {
                axios[
                    (this.active ? 'delete' : 'post')
                    ](location.pathname + '/subscriptions');

                this.active = ! this.active;

            }
        }

    }

</script>