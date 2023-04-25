<template>
    <div class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div v-if="hasSlot('title')" class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <slot name="title">
                        <h5 class="modal-title">Modal title</h5>
                    </slot>
                </div>
                <div class="modal-body">
                    <slot name="body">
                        <p>Modal body text goes here.</p>
                    </slot>
                </div>
                <div v-if="hasSlot('footer')" class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Icon from 'vue-components/Icon';

    export default {
        props: {},
        components: {
            Icon
        },
        methods: {
            hasSlot($name) {
                return this.$slots[$name] ? true : false;
            }
        },
        data () {
            return {}
        },
        mounted() {}
    }
</script>