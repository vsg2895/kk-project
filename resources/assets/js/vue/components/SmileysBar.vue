<template>
    <div class="school-rating school-rating-bar">
        <span class="rating-bar" @mouseleave="mouseLeave">
        <template v-for="n in 5">
            <smileys :read-only="readOnly" :on-clicked="smileyClicked" :on-hover="onSmileyHover" :value="n" :outline="outline(n)" :hover-outline="hoverOutline(n)" :selected="n == userRating"/>
        </template>
        </span>
        <span v-if="showRating" :class="hoveringOn === 0? 'rating-display' : 'rating-display rating-display-user'">
            <template v-if="hoveringOn === 0">{{ displayRating() }}</template>
            <template v-else="">{{ hoveringOn }}</template>
        </span>
    </div>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import Smileys from 'vue-components/Smileys';

    export default {
        props: {
            rating : 0,
            initUserRating : 0,
            readOnly: {
                default() { return false }
            },
            showRating: {
                default() { return true }
            },
            onSmileyClicked: {
                default: () => null
            }
        },
        components: {
            Smileys,
        },
        data () {
            return {
                userRating : this.initUserRating,
                noSelect : this.readOnly,
                hoveringOn : 0,
            }
        },
        watch : {
            initUserRating() {
                this.userRating = this.initUserRating;
            }
        },
        methods: {
            displayRating() {
                return this.rating ? Math.round(this.rating  * 10 ) / 10 : null;
            },
            smileyClicked(n) {
                if(!this.noSelect) {
                    this.userRating = n;
                    this.onSmileyClicked(n)
                }
            },
            onSmileyHover(n) {
                if (!this.noSelect) {
                    this.hoveringOn = n;
                }
            },
            mouseLeave() {
                this.hoveringOn = 0;
            },
            outline(n) {
                // return this.hoveringOn === 0 ? n > this.rating : n > this.hoveringOn;
                return n > this.rating;
            },
            hoverOutline(n) {
                return n > this.hoveringOn;
            },
            selected(n) {
                return this.userRating === n;
            }
        }
    }

</script>

<style lang="scss" type="text/scss">

</style>
