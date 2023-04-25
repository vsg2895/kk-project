<template>
    <div class="comment" v-if="commentExist">
        <div class="comment__meta">
            <div class="comment__author">{{ comment.user.name }}</div>
            <div class="comment__time">{{ this.getFormattedData(comment.created_at) }}</div>
        </div>

        <div class="comment__box">
            <div v-if="!commentEditing" class="comment__content">
                <span class="text">{{ comment.text }}</span>
            </div>
            <div v-else class="comment__content-edit">
            <textarea class="form-control" id="text" type="text" v-model="comment.text" placeholder=""
                      maxlength="255" required></textarea>
                <div v-if="errors && errors.text" class="text-danger">{{ errors.text[0] }}</div>
            </div>

            <div class="comment__actions" v-if="user.role_id === 3">
                <button v-if="!commentEditing" type="button" class="btn btn-icon btn-icon--edit" @click="editComment">
                    <icon name="edit"></icon>
                </button>
                <button v-else type="button" class="btn btn-icon btn-icon--edit" @click="updateComment">
                    <icon name="floppy-disk"></icon>
                </button>
                <button type="button" class="btn btn-icon btn-icon--delete" @click="deleteComment">
                    <icon name="dustbin"></icon>
                </button>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import routes from 'build/routes.js';
    import moment from 'moment';
    import Icon from '../../components/Icon';

    export default {
        props: ['comment', 'user'],
        components: {
            Icon
        },
        data() {
            return {
                routes,
                commentExist: true,
                commentEditing: false,
                errors: {}
            }
        },
        methods: {
            getFormattedData(date) {
                return moment(date).format('DD MMM, YYYY HH:mm:ss');
            },
            editComment: function () {
                this.commentEditing = !this.commentEditing;
            },
            updateComment: function () {
                let updatedComment = this.processUpdatedData(this.comment);

                this.$http.post(routes.route('blog::comments.update', {comment: this.comment.id}), updatedComment).then(function (response) {
                    this.commentEditing = false;
                }, function (errorResponse) {
                    if (errorResponse.status === 422) {
                        this.errors = errorResponse.body.errors || {};
                    }
                });
            },
            processUpdatedData: function (comment) {
                let commentFormData = new FormData();
                commentFormData.append('text', comment.text);
                commentFormData.append('post_id', comment.post_id);

                return commentFormData;
            },
            deleteComment: function () {
                this.$http.delete(routes.route('blog::comments.delete', {comment: this.comment.id})).then(function () {
                    this.commentExist = false;
                });
            }
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
