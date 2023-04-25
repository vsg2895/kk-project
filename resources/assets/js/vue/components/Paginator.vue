<template>
    <nav class="paginator">
        <ul class="pagination">
            <li class="page-item" :class="{'disabled': currentPage === 1 }">
                <a class="page-link" rel="prev" href="#" aria-label="Previous" @click="previous">
                    <span aria-hidden="true">
                        <icon name="angle-left"></icon>
                    </span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            <li class="page-item" v-for="nr in pageNrs" :class="{'active': nr === currentPage}" @click="toPage(nr, $event)">
                <a class="page-link" href="#">{{ nr }}</a>
            </li>
            <li class="page-item" :class="{'disabled': currentPage === totalPages}">
                <a class="page-link" rel="next" href="#" aria-label="Next" @click="next">
                    <span aria-hidden="true">
                        <icon name="angle-right"></icon>
                    </span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    import Icon from './Icon.vue';

    export default {
        components: {
            Icon
        },
        props: ['totalPages', 'currentPage'],
        methods: {
            previous(e) {
                e.preventDefault();
                this.$emit('prev');
            },
            toPage(n,e) {
                e.preventDefault();
                this.$emit('page', n);
            },
            next(e) {
                e.preventDefault();
                this.$emit('next');
            }
        },
        computed: {
            pageNrs() {
                let pages = []
                for (var i = 0; i < this.totalPages; i++) {
                    pages.push(i + 1);
                }
                return pages;
            }
        }
    }
</script>