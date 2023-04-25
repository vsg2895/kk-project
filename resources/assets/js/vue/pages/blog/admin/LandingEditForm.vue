<template>
    <div class="post-edit-form" v-if="post">
        <div class="row">
            <div class="col-lg-10 col-xl-8">
                <form @submit.prevent="onSubmit">
                    <div class="form-group">
                        <label for="title">Titel</label>
                        <input class="form-control" id="title" name="title" v-model="post.title"
                               aria-describedby="titleHelp"
                               placeholder="" required>
                        <div v-if="errors && errors.title" class="text-danger">{{ errors.title[0] }}</div>
                    </div>
                    <div class="form-group">
                        <label for="title">Slug</label>
                        <input class="form-control" id="slug" name="slug" v-model="post.slug"
                               aria-describedby="slugHelp"
                               placeholder="" required>
                        <div v-if="errors && errors.slug" class="text-danger">{{ errors.slug[0] }}</div>
                    </div>

                    <div class="form-group">
                        <label>Content</label>
                        <wysiwyg v-model="post.content"/>
                        <div v-if="errors && errors.content" class="text-danger">{{ errors.content[0] }}</div>
                    </div>

                  <div class="form-group">
                        <label>Call To Action</label>
                        <wysiwyg v-model="post.footer_content"/>
                        <div v-if="errors && errors.footer_content" class="text-danger">{{ errors.footer_content[0] }}</div>
                    </div>

                    <div class="form-group post-edit-form__checkbox-box">
                        <label for="status">Show</label>
                        <input type="checkbox" class=" post-edit-form__checkbox" id="status" name="status"
                               v-model="post.status" true-value="1" false-value="0"
                               aria-describedby="statusHelp">
                        <div v-if="errors && errors.status" class="text-danger">{{ errors.status[0] }}</div>
                    </div>
                    <div class="form-group post-edit-form__checkbox-box">
                        <label for="status">Hide from google search</label>
                        <input type="checkbox" class=" post-edit-form__checkbox" id="hidden" name="hidden"
                               v-model="post.hidden" true-value="1" false-value="0"
                               aria-describedby="hiddenHelp">
                        <div v-if="errors && errors.hidden" class="text-danger">{{ errors.hidden[0] }}</div>
                    </div>

                    <button class="btn btn-success" type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import routes from 'build/routes.js';
    import Api from 'vue-helpers/Api';

    export default {
        props: ['postId'],
        data() {
            return {
                routes,
                post: {},
                previewImgViewState: false,
                multimediaViewState: false,
                multimediaInputState: true,
                errors: {}
            }
        },
        methods: {
            onSubmit: function () {
                let postData = this.processData(this.post);

                this.$http.post(routes.route('blog::posts.update', {post: this.post.id}), postData).then(function (response) {
                    window.location.href = routes.route('blog::posts.indexAdmin');
                }, function (errorResponse) {
                    if (errorResponse.status === 422) {
                        this.errors = errorResponse.body.errors || {};
                    }
                });
            },
            processData: function (post) {
                let postFormData = new FormData();
                postFormData.append('title', post.title);
                postFormData.append('slug', post.slug);
                postFormData.append('content', post.content);

                if (post.hasOwnProperty('footer_content')) {
                  postFormData.append('footer_content', post.footer_content);
                }
                if (post.hasOwnProperty('preview_img_filename')) {
                    postFormData.append('preview_img_filename', post.preview_img_filename);
                } else if (post.hasOwnProperty('preview_img_filename_delete') && post.preview_img_filename_delete) {
                    postFormData.append('preview_img_filename_delete', post.preview_img_filename_delete);
                }
                if (post.hasOwnProperty('alt_text')) {
                    postFormData.append('alt_text', post.alt_text);
                }
                if (post.hasOwnProperty('status')) {
                    postFormData.append('status', post.status);
                }
                if (post.hasOwnProperty('hidden')) {
                    postFormData.append('hidden', post.hidden);
                }
                if (post.hasOwnProperty('image') || post.hasOwnProperty('video')) {
                    if (this.multimediaInputState) {
                        postFormData.append('image', post.image);
                    } else {
                        postFormData.append('video', post.video);
                    }
                } else if (post.hasOwnProperty('multimedia_delete') && post.multimedia_delete) {
                    postFormData.append('multimedia_delete', post.multimedia_delete);
                }

                return postFormData;
            },
            async getPost() {
                this.post = await Api.findPost(this.postId);
            }
        },
        watch: {
            post: function () {
                this.previewImgViewState = !!this.post.previewImg;
                this.multimediaViewState = this.post.multimedia.length > 0;
                if (this.multimediaViewState) {
                    this.post.alt_text = this.post.multimedia[0].alt_text;
                }
            }
        },
        mounted: function () {
            this.getPost();
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
