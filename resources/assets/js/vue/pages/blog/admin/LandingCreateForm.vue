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
                let name = event.path[0].name;
                this.post[name] = event.target.files[0]
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
                postFormData.append('post_type', 'landing');
                postFormData.append('title', post.title);
                postFormData.append('slug', post.slug);
                postFormData.append('content', post.content);

                if (post.hasOwnProperty('footer_content')) {
                    postFormData.append('footer_content', post.footer_content);
                }
                if (post.hasOwnProperty('status')) {
                    postFormData.append('status', post.status);
                }
                if (post.hasOwnProperty('hidden')) {
                    postFormData.append('hidden', post.hidden);
                }

                return postFormData;
            }
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
