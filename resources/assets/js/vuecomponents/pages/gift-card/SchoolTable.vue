<template>
    <div class="table-container">
        <table class="schools-table" :class="{ 'searching' : searching }">
            <tr>
                <th>Trafikskolor</th>
                <th class="text-xs-right"></th>
            </tr>
            <template v-for="row in pagnatedSchoolRows">
                <tr class="school-row" :key="row.id">
                    <td class="name text-xs-left">
                        {{ row.name }}
                    </td>
                    <td class="location text-xs-right">
                    </td>
                </tr>
            </template>
            <tr v-if="noSchools" class="no-schools">
                <td v-if="cityName" colspan="2">Inga skolor i {{ cityName }} hittades</td>
                <td v-else colspan="2">Inga skolor hittades</td>
            </tr>
            <div v-if="searching" class="loader-indicator brand-success text-xs-left"></div>
        </table>
        <paginator class="brand-success" v-if="!noSchools" :total-pages="totalPages" :current-page="currentPage" @next="onNext" @page="onPage" @prev="onPrev"></paginator>
    </div>
</template>
<script>
    import _ from 'lodash';
    import Api from '../../../vue/helpers/Api'
    import Icon from '../../../vue/components/Icon.vue';
    import Smileys from '../../../vue/components/Smileys.vue';
    import Paginator from '../../Paginator.vue';
    import routes from 'build/routes.js';

    export default {
        components: {
            Icon,
            Smileys,
            Paginator
        },
        props: ['schools', 'cityName', 'searching'],
        data() {
            return {
                currentPage: 1,
                resultsPerPage: 5,
            }
        },
        computed: {
            noSchools() {
                return this.schools.length === 0;
            },
            allSchoolRows() {
                return this.noSchools ? [] :
                    this.schools.map(s => {
                        let url = routes.route('shared::schools.show', { citySlug: s.city.slug, schoolSlug: s.slug });
                        return {
                            id: s.id,
                            name: s.name,
                            rating: s.average_rating,
                            location: s.postal_city,
                            url,
                        }
                    });
            },
            pagnatedSchoolRows() {
                if (this.noSchools) {
                    return [];
                } else {
                    let start = (this.currentPage - 1) * this.resultsPerPage;
                    let end = start + this.resultsPerPage;
                    return this.schools ? this.allSchoolRows.slice(start, end) : [];
                }
            },
            totalPages() {
                return this.noSchools? 0 : Math.ceil(this.schools.length / this.resultsPerPage);
            },
            pagnation() {
                return this.schools.length > this.resultsPerPage;
            }
        },
        methods: {
            onNext() {
                this.currentPage = Math.min(this.currentPage + 1, this.totalPages);
            },
            onPrev() {
                this.currentPage = Math.max(this.currentPage - 1, 1);
            },
            onPage(n) {
                if (0 < n && n <= this.totalPages)
                    this.currentPage = n;
            },
            selectSchool(id){
                this.$emit('schoolSelected', id);
            }
        },
    }
</script>