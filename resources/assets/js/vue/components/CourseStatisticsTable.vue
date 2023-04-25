<template>
    <div class="course-statistics-table" :class="{ 'on-region' : data.region, 'requesting' : requesting }">
        <div class="region-banner" v-if="data.region">{{ data.region }}</div>
        <h5 class="grey bold grey">
            Totalt upplagda kurser
            <span v-if="data.region" class="bolder">i {{ data.region }}</span>
        </h5>
        <span v-if="data.region" class="back-link blue" @click="request()">
            <icon name="arrow-left"></icon>
            Alla orter
        </span>
        <div v-if="data.list.length" class="table">
            <div class="table-head table-row">
              <div class="region table-cell col-xs-6">
                Ort
              </div>
              <div class="count table-cell align-center col-xs-3">
                Antal
              </div>
              <div class="share table-cell align-center col-xs-3">
                Andel
              </div>
            </div>
            <div v-for="(row,i) in data.list" class="table-row">
              <div class="region table-cell col-xs-6">
                {{ page > 1 ? (page - 1) * perPage + i + 1 : i + 1 }} .
                <span class="blue" @click="getRegionData(row.id)">{{ row.name }}</span>
              </div>
              <div class="count table-cell align-center medium col-xs-3">
                {{ row.count }}
              </div>
              <div class="share table-cell align-center medium col-xs-3">
                {{ shareFormatter(row.share) }}
              </div>
            </div>
        </div>
      <nav aria-label="Page navigation example">
        <ul class="pagination">
          <li class="page-item" v-if="page > 1"><a class="page-link pagination-p-n" @click="prevPage(page - 1)">Previous</a></li>
          <li v-for="index in this.total" class="page-item" @click="movePage(index)" :class="{'active':page === index}">
            <a class="page-link cursor-pointer">{{ index }}</a>
          </li>
          <li class="page-item" v-if="page < total"><a class="page-link pagination-p-n" @click="nextPage(page + 1)">Next</a></li>
        </ul>
      </nav>
        <a v-if="data.list.length" class="table-link" href="#">Visa fullständig rapport</a>
        <no-results v-if="!data.list.length && !requesting" title="Inga kurser hittades"></no-results>
        <div v-if="requesting" class="loader-indicator"></div>
    </div>
</template>

<script>
    import NoResults from './NoResults';
    import Icon from 'vue-components/Icon';
    import Api from 'vue-helpers/Api';
    import Pagination from 'vue-helpers/Pagination';
    import $ from "jquery";

    export default {
        components: { NoResults, Icon },
        data() {
            return {
                allDataList:[],
                data: {
                    list: [],
                    region: '',
                },
                page:1,
                perPage:10,
                total:0,
                requesting : false
            }
        },
        mounted() {
          this.request();
        },
        methods: {
            request(id = null) {
                this.requesting = true;
                Api.topRegions(id)
                    .then(response => {
                        this.requesting = false;
                        if(response.ok) {
                          this.data = response.body;
                          this.allDataList = this.data.list;
                          this.total = Math.ceil(this.allDataList.length / this.perPage);
                          this.data.list = Pagination.dataForPage(this.page, this.allDataList, this.perPage);
                        }
                    }).catch(error => {
                        this.requesting = false;
                        console.error(error);
                    })
            },
            movePage(page) {
              this.page = page;
              this.data.list = Pagination.dataForPage(page, this.allDataList, this.perPage);
            },
            prevPage(page) {
              page = page > 0 ? page : 1;
              this.page = page;
              this.data.list = Pagination.dataForPage(page, this.allDataList, this.perPage);
            },
            nextPage(page) {
              page = page > this.total ? this.total : page;
              this.page = page;
              this.data.list = Pagination.dataForPage(page, this.allDataList, this.perPage);
            },
            getRegionData(id = null) {
             if (!this.data.region) {
                  this.request(id)
             }
            },
            shareFormatter(share){
                return (share * 100).toString().substring(0,4) + '%';
            }
        }
    }

</script>