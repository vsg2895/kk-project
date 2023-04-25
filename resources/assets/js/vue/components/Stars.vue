<template>
    <div class="start-container">
        <span v-for="(star, index) in stars" :key="`a${index}`" class="stars-bar">
            <span class="stars-bar__star"></span>
        </span>
        <span v-for="index in (5 - stars.length)" :key="`b${index}`" class="stars-bar">
            <span class="stars-bar__null" />
        </span>
        <span v-if="halfStar" :style="halfStartStyle" class="stars-bar__star half-star"></span>
    </div>
</template>

<script>
    export default {
        props: ['rating'],
        data() {
            return {
                halfStar: false
            };
        },
        computed: {
            stars() {
                if (!this.rating) {
                    return [];
                }
                const averageRate = this.rating;
                const int = ~~averageRate;
                let arr = new Array(int).fill('');
                const decimal = averageRate % 1;
                if (decimal !== 0) {
                    this.halfStar = true;
                }
                return arr;
            },
            halfStartStyle() {
                if (!this.halfStar) return;

                const intPart = Math.trunc(this.rating);
                const floatPart = Number((this.rating - intPart).toFixed(2));
                return {
                    left: `${intPart * 24}px`,
                    width: `${floatPart * 20}px`
                };
            },
        }
    }
</script>

<style lang="scss" type="text/scss">
.start-container {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;

    .stars-bar {
        display: flex;
        align-items: center;
        margin-right: 4px;
        font-size: 0;

        &__star {
            background: url(/build/img/review1.png)
            
        }
        &__null {
            background: url(/build/img/review0.png)
        }

        &__null,
        &__star {
            display: inline-block;
            height: 20px;
            width: 20px;
            background-size: 20px 20px;
            background-repeat: no-repeat;
            &.half-star {
                position: absolute;
                width: 10px;
            }
        }
    }
}
</style>
