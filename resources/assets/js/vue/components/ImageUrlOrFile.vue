<template>
    <div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <p>URL</p>
                    <img :src="cdnUrl || placeholderImage" alt="" class="partner__image">
                </div>
                <div class="col-md-6">
                    <p>Fil</p>
                    <img :src="bildUrl" alt="" class="partner__image" @error="errorFileImage">
                </div>
            </div>
        </div>

        <div class="form-group">
            <input type="radio" v-model="type" id="type__url" value="url">
            <label for="type__url">URL</label>
            &nbsp;
            <input type="radio" v-model="type" id="type__file" value="file">
            <label for="type__file" v-text="fileLabel"></label>
        </div>
        <div class="form-group">
            <template v-if="type === 'url'">
                <input class="form-control" type="url" name="image" placeholder="Partners bildadress"/>
            </template>
            <template v-if="type === 'file'">
                <input class="form-control" type="file" name="image" accept="image/jpeg,image/png">
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ImageUrlOrFile",
        props: {
            oldValue: {
                type: String
            },
            fileLabel: {
                type: String,
                default: 'File'
            },
            bildUrl: {
                default: null
            },
            cdnUrl: {
                default: null
            },
        },
        data() {
            return {
                type: 'url',
                placeholderImage: 'https://placehold.it/256x178?text=No+Image'
            }
        },
        methods: {
            errorFileImage(event) {
                event.target.src = this.placeholderImage;
            }
        }
    }
</script>

<style scoped lang="scss">
    img {
        &.partner__image {
            width: 256px !important;
            height: 178px !important;
        }
    }
</style>
