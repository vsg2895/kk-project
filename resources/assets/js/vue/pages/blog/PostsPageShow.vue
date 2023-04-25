<template>
    <div id="posts" class="posts posts-page">
        <div class="container posts-section">
            <div class="row equal-height">
                <div class="col-12 w-100" v-for="(post, index) in posts">
                    <div class="post-item">
                        <a :href="routes.route('blog::show', {post: post.slug})">
                            <div class="post-item__img" :class="{'empty-image': !post.previewImg}">
                                <img class="image" :src="post.previewImg" alt="">
                            </div>
                        </a>

                        <div class="post-item__content">
                            <a class="post-item__link" :href="routes.route('blog::posts.show', {post: post.id})">
                                <h2 class="post-item__title">{{ post.title }}</h2>
                            </a>

                            <div class="post-item__meta">
                                <span class="time">{{ getFormattedData(post.created_at) }}</span>
                            </div>
                            <div class="post-actions" v-if="roleId === 3">
                                <a :href="routes.route('blog::posts.edit', {post: post.id})" role="button"
                                   class="btn btn-icon btn-icon--edit">
                                    <icon name="edit"></icon>
                                </a>
                                <button type="button" class="btn btn-icon btn-icon--delete"
                                        @click="deletePost(post.id, index)">
                                    <icon name="dustbin"></icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <no-results v-if="!posts.length" title="There are no posts"></no-results>
            </div>

            <div class="row">
                <div class="col-md-4 offset-md-4">
                    <button class="intensive-button" v-on:click="loadMore" v-if="!disabledButton">
                        Get next posts <!--todo: should trans to native lang-->
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import routes from 'build/routes.js';
    import moment from 'moment';
    import NoResults from '../../components/NoResults';
    import Icon from '../../components/Icon';

    export default {
        props: ['roleId'],
        components: {
            NoResults,
            Icon
        },
        data() {
            return {
                page: 0,
                posts: [],
                routes,
                disabledButton: 'false'
            }
        },
        methods: {
            loadMore: function () {
                this.page++;
                this.getPosts(this.makeQuery());
            },
            addPosts: function (newPosts) {
                this.posts = this.posts.concat(newPosts)
            },
            getPosts: function (query) {
                Api.searchPosts(query).then((response) => {
                    this.disabledButton = response.data.last_page === this.page;
                    this.addPosts(response.data.posts.slice(0, 3));
                });
            },
            makeQuery: function () {
                return {page: this.page};
            },
            getFormattedData(date) {
                return moment(date).format('DD MMM, YYYY');
            },
            deletePost: function (postId, index) {
                if (confirm('Är du säker?')) {
                    this.$http.delete(routes.route('blog::posts.delete', {post: postId})).then(function () {
                        this.posts.splice(index, 1)
                    });
                }
            }
        },
        mounted: function () {
            this.loadMore();
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
