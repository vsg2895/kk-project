<template>
    <div id="comments" class="comments">
        <div class="container comment-form">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <comment-form v-if="this.isLogged" v-on:commentsUpdate="handleCommentsUpdate"
                                  :post-id="postId"></comment-form>
                </div>
            </div>
        </div>

        <div class="container comments-section">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <comment-card v-for="(comment, index) in comments" :comment="comment" :key="index"
                                  :user="user"></comment-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import CommentCard from './CommentCard';
    import CommentForm from './CommentForm';

    export default {
        props: ['postId'],
        components: {
            CommentCard,
            CommentForm
        },
        data() {
            return {
                page: 0,
                comments: [],
                disabledButton: 'false',
                isLogged: false,
                user: {}
            }
        },
        methods: {
            loadMore: function () {
                this.page++;
                this.getComments(this.makeQuery());
            },
            addComments: function (newComments) {
                this.comments = this.comments.concat(newComments)
            },
            getComments: function (query) {
                Api.searchComments(query).then((response) => {
                    this.disabledButton = response.data.last_page === this.page;
                    this.addComments(response.data.comments);
                });
            },
            makeQuery: function () {
                return {page: this.page, post_id: this.postId};
            },
            clearComments: function () {
                this.comments = [];
            },
            getLoggedUser: function () {
                let self = this;
                Api.getLoggedInUser().then(function (response) {
                    if (response.hasOwnProperty('role_id')) {
                        self.isLogged = true;
                        self.user = response;
                    } else {
                        self.isLogged = false;
                        self.user = {};
                    }
                });
            },
            handleCommentsUpdate: function (commentsResponse) {
                this.clearComments();

                this.comments = commentsResponse.comments;
                this.page = commentsResponse.page;
                this.disabledButton = commentsResponse.last_page === this.page;
            }
        },
        mounted: function () {
            this.loadMore();

            this.getLoggedUser();
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
