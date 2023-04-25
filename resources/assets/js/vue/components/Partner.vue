<template>
    <div class="partner__bg" @click="goTo(url)">
        <img :src="`${img}?${id}`" alt="">
        <div class="partner__overlay">
            <h2 v-text="partner"></h2>
            <p v-text="description"></p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Partner",
        props: {
            img: {
                type: String,
                required: true
            },
            partner: {
                type: String,
                required: true
            },
            description: {
                type: String,
                required: true
            },
            url: {
                type: String,
                required: true
            },
            id: {
                type: Number,
                required: true
            }
        },
        methods: {
            goTo(url) {
                console.log(url);
                window.open(url, '_blank');
            }
        }
    }
</script>

<style scoped lang="scss">
    $partner__bg: rgba(0, 0, 0, 0.2);

    *,
    *:before,
    *:after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .partner__bg,
    .partner__overlay {
        text-align: center;
    }

    img,
    .partner__overlay {
        transition: .3s all;
        border-radius: 3px;
    }

    .partner__bg {
        float: left;
        position: relative;
        margin: .5%;

        img {
            margin-bottom: -4px;

            width: 256px !important;
            height: 178px !important;
        }

        .partner__overlay {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100%;
            background: $partner__bg;
            color: #fff;

            h2 {
                padding-top: 20%;
            }
        }

        &:hover {

            cursor: pointer !important;

            .partner__overlay {
                opacity: 1;
            }

            img {
                -webkit-filter: blur(2px);
                filter: blur(2px);
            }
        }
    }

    @media screen and (max-width: 1148px) {
        .partner__bg {
            max-width: 48%;
            margin: 1%;
        }
    }

    @media screen and (max-width: 768px) {
        .partner__bg {
            float: none;
            max-width: 80%;
            margin: 1% auto;
        }
    }
</style>
