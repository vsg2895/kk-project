<template>
    <semantic-search :onItemSelected="itemSelected" :data="results" placeholder="Sök på stad/trafikskola...">
        <template slot="dropdown-item" slot-scope="props">
            <a class="result" :class="props.item.category" @click="itemSelected(props.item)">
                <icon class="item-icon" v-if="props.item.category === 'CITY'" name="location"></icon>
                <icon class="item-icon" v-if="props.item.category === 'SCHOOL'" name="school"></icon>
                <div v-if="props.item.name" class="item-title">{{ props.item.name }}</div>
                <div v-if="props.item.description" class="item-description">{{ props.item.description }}</div>
            </a>
        </template>
    </semantic-search>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import _ from 'lodash';
    import SemanticSearch from 'vue-components/SemanticSearch';
    import routes from 'build/routes.js';
    import Icon from 'vue-components/Icon'

    export default {
        components: {
            'semantic-search': SemanticSearch,
            Icon
        },
        data() {
            return {
                results: [],
            };
        },
        watch: {
        },
        methods: {
            itemSelected(item) {
                var route = null;
                if (item) {
                    if (item.category === 'SCHOOL') {
                        route = routes.route('shared::schools.show', { citySlug: item.city_slug, schoolSlug: item.slug });
                    } else {
                        route = routes.route('shared::search.schools', { citySlug: item.slug });
                    }
                    window.location = route;
                }
            },

            async getData() {
                var vm = this;

                let [schools, cities] = await Promise.all([Api.getSchools(), Api.getCities()]);

                schools = _.map(schools, (school) => {
                    return {
                        id: 'school-' + school.id,
                        realId: school.id,
                        name: school.name,
                        category: 'SCHOOL',
                        slug: school.slug,
                        city_slug: school.city_slug,
                        description: 'i ' + school.city_name
                    }
                });
                cities = _.filter(cities, (city) => {
                    // return city.school_count > 0;
                    return true;
                });
                cities = _.map(cities, (city) => {
                    return {
                        id: 'city-' + city.id,
                        realId: city.id,
                        name: city.name,
                        category: 'CITY',
                        slug: city.slug
                    }
                });

                vm.results = _.sortBy(_.concat(schools, cities), (item) => {
                    return item.name.length;
                });
            }
        },
        mounted() {
            this.getData();
        },
        destroyed () {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
