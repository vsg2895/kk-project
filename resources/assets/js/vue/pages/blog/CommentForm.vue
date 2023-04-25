<template>
    <div class="comment-form">
        <form @submit.prevent="onSubmit">
            <div class="form-group">
                <label for="text">Comment</label>
                <textarea class="form-control" id="text" type="text" v-model="comment.text" placeholder=""
                          maxlength="255" required></textarea>
                <div v-if="errors && errors.text" class="text-danger">{{ errors.text[0] }}</div>
            </div>

            <button class="btn btn-primary" type="submit">Add</button>
        </form>
    </div>
</template>

<script type="text/babel">
    import routes from 'build/routes.js';

    export default {
        props: ['postId'],
        data() {
            let comment = {
                text: '',
                post_id: this.postId
            };

            return {
                routes,
                comment: comment,
                errors: {},
            }
        },
        methods: {
            onSubmit: function () {
                let commentData = this.processData(this.comment);

                this.$http.post(routes.route('blog::comments.store'), commentData).then(function (response) {
                    this.comment.text = '';
                    this.$emit('commentsUpdate', response.body);
                }, function (errorResponse) {
                    if (errorResponse.status === 422) {
                        this.errors = errorResponse.body.errors || {};
                    }
                });
            },
            processData: function (comment) {
                let commentFormData = new FormData();
                commentFormData.append('text', comment.text);
                commentFormData.append('post_id', comment.post_id);

                return commentFormData;
            }
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
