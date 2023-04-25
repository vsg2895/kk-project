<template>
    <div class="search ui semantic-search-field">
        <slot>
            <div class="ui input">
              <div class="form-group mb-0">
                <span class="fa fa-map-marker-alt form-control-icon"></span>
                <input :placeholder="placeholder" :disabled="searching" v-model="message" type="text"
                 class="form-control form-control-lg prompt">
              </div>
            </div>
            <div  class="results">
                <p v-if="!results.length">Din sökning gav ingen träff</p>
                <template v-for="item in results">
                    <slot name="dropdown-item" :item="item">
                        <div :data-value="item.id" class="item item-title" :key="item.id">{{ item.title }}</div>
                    </slot>
                </template>
            </div>
        </slot>
    </div>
</template>

<script>
    import $ from 'jquery';
    $.fn.search = require('semantic-ui-search');
    $.fn.transition = require('semantic-ui-transition');

    export default {
        props: ['data', 'onItemSelected', 'placeholder', 'selected', 'searching'],
        components: {
        },
        data() {
            return {
                results: [],
                message: '',
            };
        },
        watch: {
            data() {
                this.initSearch()
            },
            selected() {
                let $element = $(this.$el);
                $element.search('set value', this.selected.name);
                $element.search('hide results');
                this.message = this.selected.name;
            },
            results: {
                handler() {
                    let vm = this;
                    setTimeout(function() {
                        vm.g.data().searchModule.inject.id(vm.results);
                    }, 0);
                    let $results = $(this.$el).find('.results');
                    if (this.results.length) {
                        $results.addClass('transition visible');
                        return true;
                    }
                },
                deep: true
            },
            message: {
                handler(message) {
                    if (!message.length) {
                        this.$emit('reload');
                    }
                }
            }
        },
        methods: {
            initSearch() {
                let vm = this;
                let $element = $(this.$el);
                vm.g = $element.search({
                    maxResults: 10,
                    source: vm.data,
                    searchFields: [
                        'name',
                        'description'
                    ],
                    minCharacters: 2,
                    onSelect(result, response) {
                        if (result && vm.onItemSelected) {
                            $element.search('set value', result.name);
                            $element.search('hide results');
                            vm.onItemSelected(result);
                        }
                        return false;
                    },
                    onResults(response) {
                        vm.$set(vm, 'results', response.results);
                    },
                    onResultsAdd(response) {
                        return false;
                    }
                });
            }
        },
        mounted() {
        },
        destroyed() {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
