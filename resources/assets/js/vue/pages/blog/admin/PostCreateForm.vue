<template>
    <div class="post-create-form">
        <div class="row">
            <div class="col-lg-10 col-xl-8">
                <form @submit.prevent="onSubmit">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input class="form-control" id="title" name="title" v-model="post.title"
                               aria-describedby="titleHelp"
                               placeholder="" required>
                        <div v-if="errors && errors.title" class="text-danger">{{ errors.title[0] }}</div>
                    </div>

                    <div class="form-group">
                        <label for="title">URL Name</label>
                        <input class="form-control" id="slug" name="slug" v-model="post.slug"
                               aria-describedby="slugHelp"
                               placeholder="" required>
                        <div v-if="errors && errors.slug" class="text-danger">{{ errors.slug[0] }}</div>
                    </div>

                  <div class="form-group">
                    <label for="previewImg">Picture</label>
                    <input type="file" class="form-control" id="previewImg" name="preview_img_filename"
                           @change="onFileChanged" accept="image/jpeg,image/png"
                           aria-describedby="previewImgHelp" placeholder="">
                    <div v-if="errors && errors.preview_img_filename" class="text-danger">
                      {{ errors.preview_img_filename[0] }}
                    </div>
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

                    <div class="form-group">
                      <label>Button Text</label>
                      <input class="form-control" id="button_text" name="button_text" v-model="post.button_text"
                             aria-describedby="buttonTextHelp">
                      <div v-if="errors && errors.button_text" class="text-danger">{{ errors.button_text[0] }}</div>
                    </div>

                    <div class="form-group">
                      <label>Button Link</label>
                      <input class="form-control" id="link" name="link" v-model="post.link"
                             aria-describedby="linkHelp">
                      <div v-if="errors && errors.link" class="text-danger">{{ errors.link[0] }}</div>
                    </div>

                    <div class="form-group post-create-form__checkbox-box">
                        <label for="status">Show</label>
                        <input class="post-create-form__checkbox" type="checkbox" id="status" name="status"
                               v-model="post.status" true-value="1" false-value="0"
                               aria-describedby="statusHelp">
                        <div v-if="errors && errors.status" class="text-danger">{{ errors.status[0] }}</div>
                    </div>

                    <div class="form-group post-create-form__checkbox-box">
                        <label for="status">Hide from google search</label>
                        <input class="post-create-form__checkbox" type="checkbox" id="hidden" name="hidden"
                               v-model="post.hidden" true-value="1" false-value="0"
                               aria-describedby="hiddenHelp">
                        <div v-if="errors && errors.hidden" class="text-danger">{{ errors.hidden[0] }}</div>
                    </div>

                    <button class="btn btn-success" type="submit">Skapa</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import routes from 'build/routes.js';

    export default {
        data() {
            return {
                routes,
                post: {},
                errors: {},
                multimediaState: true
            }
        },
        methods: {
            onFileChanged: function (event) {
                this.post['preview_img_filename'] = event.target.files[0]
            },
            onSubmit: function () {
                let postData = this.processData(this.post);

                this.$http.post(routes.route('blog::posts.store'), postData).then(function (response) {
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
                if (post.hasOwnProperty('button_text')) {
                  postFormData.append('button_text', post.button_text);
                }
                if (post.hasOwnProperty('link')) {
                  postFormData.append('link', post.link);
                }
                if (post.hasOwnProperty('preview_img_filename')) {
                    postFormData.append('preview_img_filename', post.preview_img_filename);
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
                    if (this.multimediaState) {
                        postFormData.append('image', post.image);
                    } else {
                        postFormData.append('video', post.video);
                    }
                }

                return postFormData;
            }
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
