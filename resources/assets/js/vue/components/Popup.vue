<template>
    <span data-toggle="popover" :data-content="content"><icon :name="icon"></icon></span>
</template>

<script type="text/babel">
    import Icon from 'vue-components/Icon';
    import $ from 'jquery';

    export default {
        props: {
            content: {
                type: String
            },
            icon: {
                type: String,
                default: 'info'
            },
            onShow: {
                type: Object,
                default: function () {}
            },
            onShown: {
                type: Object,
                default: function () {}
            },
            onHide: {
                type: Object,
                default: function () {}
            },
            onHidden: {
                type: Object,
                default: function () {}
            }
        },
        components: {
            Icon
        },
        computed: {
        },
        methods: {
        },
        mounted () {
            var $element = $(this.$el);
            $element.popover({
                trigger: 'hover focus',
                addTouchEvents: true,
                offset: '0 -14px'
            });

            $element.on('show.bs.popover', this.onShow);
            $element.on('shown.bs.popover', this.onShown);
            $element.on('hide.bs.popover', this.onHide);
            $element.on('hidden.bs.popover', this.onHidden);
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>