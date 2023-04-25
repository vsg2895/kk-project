<template>
    <span @click="select()" @mouseover="hover()" :class="[selected ? 'selected': '', !outline ? 'active' : '', !hoverOutline ? 'hover': '' , 'smiley', readOnly ? 'disabled' : '']">
        <template v-if="parseInt(value) > 4">
            <icon name="smiley5"></icon>
            <div class="check">
                <icon name="check-tiny"></icon>
            </div>
        </template>

        <template v-else-if="parseInt(value) > 3">
            <icon name="smiley4"></icon>
            <div class="check">
                <icon name="check-tiny"></icon>
            </div>
        </template>

        <template v-else-if="parseInt(value) > 2">
            <icon name="smiley3"></icon>
            <div class="check">
                <icon name="check-tiny"></icon>
            </div>
        </template>

        <template v-else-if="parseInt(value) > 1">
            <icon name="smiley2"></icon>
            <div class="check">
                <icon name="check-tiny"></icon>
            </div>
        </template>

        <template v-else-if="parseInt(value) > 0">
            <icon name="smiley1"></icon>
            <div class="check">
                <icon name="check-tiny"></icon>
            </div>
        </template>
    </span>
</template>

<script>
    import Icon from 'vue-components/Icon';

    export default {
        props: ['value', 'outline', 'selected', 'onClicked', 'onHover', 'hoverOutline', 'readOnly'],
        components: {
            Icon,
        },
        data () {
            return {}
        },
        methods: {
            select: function () {
                if (this.onClicked) {
                    this.onClicked(this.value);
                    return;
                }
            },
            hover: function () {
                if (this.onHover) {
                    this.onHover(this.value);
                    return;
                }
            }
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
