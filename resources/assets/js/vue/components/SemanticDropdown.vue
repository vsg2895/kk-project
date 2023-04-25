<template>
    <div :id="id" class="ui dropdown" :class="dropdownClasses">
        <input type="hidden" :name="formName" :id="formName">
        <i v-if="search" class="far fa fa-map-marker-alt ui-icon"></i>
        <div class="relative text text-black">{{ placeholder }}</div>
        <icon v-if="!search" name="dropdown" class="dropdown-carret" />
        <div class="menu">
            <slot name="custom-dropdown-items"></slot>
            <template v-for="item in data">
                <slot name="dropdown-item" :item="item">
                    <div :key="item.id" :data-value="item.id" class="item item-text1">{{ item.name }}</div>
                </slot>
            </template>
        </div>
    </div>
</template>

<script type="text/babel">
    import $ from 'jquery';
    import _ from 'lodash';
    import Icon from 'vue-components/Icon';
    $.fn.search = require('semantic-ui-search');
    $.fn.dropdown = require('semantic-ui-dropdown');
    $.fn.transition = require('semantic-ui-transition');

    export default {
        components: {
            Icon,
        },
        props: {
            id: {},
            data: {},
            shouldSkipInitial: {},
            alignDropdown: {},
            onItemSelected: {},
            placeholder: {},
            header: {
              default: false
            },
            formName: {},
            size: {},
            multiple: {},
            search: {},
            readonly: {},
            floating: {},
            inline: {},
            initialItem: {},
            customAction: {},
            valueField: {
                default: 'id'
            }
        },
        data() {
            return {
                initialApplied: false
            };
        },
        methods: {
            applyInitial: function() {
                let $element = $(this.$el);
                let idField = this.initialItem && this.initialItem[this.valueField] ? this.initialItem[this.valueField] : this.initialItem;
                if (this.initialItem) {
                    if (this.multiple) {
                        if(this.initialItem.length === 0) $element.dropdown('restore defaults');
                        let ids =  _.split(idField, ',');
                        setTimeout(function() {
                            $element.dropdown('set selected', ids);
                        }, 0)
                    } else {
                        setTimeout(function() {
                            $element.dropdown('set selected', idField);
                        }, 0)
                    }
                } else {
                    $element.dropdown('restore defaults')
                }
            }
        },
        computed: {
            dropdownClasses: function() {
                let dropdownClasses = {
                    'multiple': this.multiple,
                    'search': this.search,
                    'floating': this.floating,
                    'fluid selection': !this.inline,
                    'inline': this.inline,
                    'disabled': this.readonly
                };
                if (this.alignDropdown) {
                    dropdownClasses['dropdown-'+this.alignDropdown] = this.alignDropdown;
                }
                if (this.size) dropdownClasses['size-' + this.size] = this.size;
                return dropdownClasses;
            }
        },
        watch: {
            initialItem: function() {
                this.applyInitial();
            }
        },
        mounted () {
            let $element = $(this.$el);
            let vm = this;
            let options = {
                forceSelection: false,
                fullTextSearch: true,
                onChange(value, text) {
                    if (vm.onItemSelected) {
                        if (!vm.initialApplied && vm.shouldSkipInitial) {
                            vm.initialApplied = true
                        } else {
                            let item = _.find(vm.data, (item) => {
                                if (_.isObject(item)) {
                                    return item[vm.valueField] == value;
                                } else {
                                    return item == value;
                                }
                            });

                            if (!item) {
                                item = value;
                            }

                            vm.onItemSelected(item);
                        }
                    }
                }
            };

            if (this.customAction) {
                options.action = function (text, value, element) {
                    $(this).find('.menu').removeAttr('style').removeClass('visible');
                    if (vm.onItemSelected) {
                        if (!vm.initialApplied && vm.shouldSkipInitial) {
                            vm.initialApplied = true
                        } else {
                            let item = _.find(vm.data, (item) => {
                                if (_.isObject(item)) {
                                    return item[vm.valueField] == value;
                                } else {
                                    return item == value;
                                }
                            });

                            if (!item) {
                                item = value;
                            }

                            vm.onItemSelected(item);
                        }
                    }
                }
            }

            $element.dropdown(options);
            this.applyInitial();
        },
        destroyed () {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
