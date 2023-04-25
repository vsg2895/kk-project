<template>
    <div class="summary" :class="{ 'requesting' : requesting }">
        <div class="grey-background">
            <div class="medium">Bokningar och omsättning: <span class="blue bold">{{ data.display }}</span></div>
            <div class="chart-container">
                <bar-line :data="chartData" :options="chartOptions"></bar-line>
            </div>
            <div class="input-row">
                <div class="interval-btns" role="group" aria-label="interval buttons">
                    <button @click="changeInterval('daily')" :class="{'active' : data.interval === 'daily'}"
                        type="button" class="btn btn-sm">Dag</button>
                    <button @click="changeInterval('weekly')" :class="{'active' : data.interval === 'weekly'}"
                        type="button" class="btn btn-sm">Vecka</button>
                    <button @click="changeInterval('monthly')" :class="{'active' : data.interval === 'monthly'}"
                        type="button" class="btn btn-sm">Månad</button>
                    <button @click="changeInterval('yearly')" :class="{'active' : data.interval === 'yearly'}"
                        type="button" class="btn btn-sm">År</button>
                </div>
                <div class="range-picker">
                    <summary-date-range :start-date="data.startDate" :end-date="data.endDate"
                        @onRangeChange="changeRange"></summary-date-range>
                </div>
            </div>
        </div>
        <div class="type-btns" role="group" aria-label="interval buttons">
            <span>Baserat på</span>
            <button @click="changeType('city')" :class="{'active' : data.type === 'city'}"
                type="button" class="btn btn-sm">Ort</button>
            <button v-if="!organization" @click="changeType('organization')"
                :class="{'active' : data.type === 'organization'}"
                type="button" class="btn btn-sm">Organisation</button>
            <button @click="changeType('school')" :class="{'active' : data.type === 'school'}"
                type="button" class="btn btn-sm">Trafikskola</button>
        </div>
        <div v-if="data.topFive.length" class="top-five table">
            <div class="table-head table-row">
                <div class="table-cell col-xs-4 col-sm-3">
                    Ort
                </div>
                <div class="table-cell align-center col-xs-4 col-sm-3">
                    Bokningar
                </div>
                <div class="table-cell align-center col-xs-4 col-sm-3">
                    Avbokningar
                </div>
                <div class="table-cell align-center hidden-xs-down col-sm-3">
                    Omsättning
                </div>
            </div>
            <div v-for="(row,i) in data.topFive" :key="i" class="table-row">
                <div class="table-cell col-xs-4 col-sm-3 name-block">
                    <a class="blue" @click="tableLink(row)">
                      {{ page > 1 ? (page - 1) * perPage + i + 1 :  i + 1  }} . {{ row.name }}
                    </a>
                  <div v-if="moreInfo" class="col-md-2 col-lg-2">
                    <div class="dropdown">
                      <a>
                        <span v-if="row.schoolId !== undefined" class="top-bar-app-links make-it-regular top-bar-margin-top"
                              @click="showMoreHandle(row.schoolId)">Mer info</span>
                        <span v-else-if="row.cityId !== undefined" class="top-bar-app-links make-it-regular top-bar-margin-top"
                              @click="showMoreHandle(row.cityId)">Mer info</span>
                        <span v-else class="top-bar-app-links make-it-regular top-bar-margin-top"
                              @click="showMoreHandle(row.orgId)">Mer info</span>
                        <span class="caret"></span>
                        <ul v-if="(showMore == row.schoolId || showMore == row.cityId || showMore == row.orgId)" class="dropdown-menu show-more-organization-statistics" aria-labelledby="dLabel">
                          <li v-if="item.segment_name !== 'vehicle_segments.'" class="show-more-organization-statistics_first_li" v-for="(item,j) in data.moreInfo[row.name]">
                            <div>{{ item.segment_name }}</div>
                          </li>
                          <li class="show-more-organization-statistics_first_li">
                            <div>Paket</div>
                          </li>
                        </ul>
                      </a>
                    </div>
                  </div>

                </div>
              <div class="table-cell align-center col-xs-4 col-sm-3" :class="{'more-info-block':moreInfo}">
                <div class="booked">
                  {{ row.booked }}
                </div>
                <ul v-if="(showMore == row.schoolId || showMore == row.cityId || showMore == row.orgId) && moreInfo" class="mt-3 dropdown-menu show-more-organization-statistics"
                    aria-labelledby="dLabel">
                  <li v-if="item.segment_name !== 'vehicle_segments.'" v-for="(item,j) in data.moreInfo[row.name]">
                    <div>{{ item.order_count }}</div>
                  </li>
                  <li>
                    <div>{{ data.moreInfo[row.name][0].paket_count }}</div>
                  </li>
                </ul>
              </div>

              <div class="table-cell align-center col-xs-4 col-sm-3" :class="{'more-info-block':moreInfo}">
                <div class="cancelled">
                  {{ row.cancelled }}
                </div>
                <ul v-if="(showMore == row.schoolId || showMore == row.cityId || showMore == row.orgId) && moreInfo" class="mt-3 dropdown-menu show-more-organization-statistics"
                    aria-labelledby="dLabel">
                  <li v-if="item.segment_name !== 'vehicle_segments.'" v-for="(item,j) in data.moreInfo[row.name]">

                    <div>{{ item.cancelled_order_count }}</div>

                  </li>
                  <li>
                    <div>{{ data.moreInfo[row.name][0].cancelled_paket_count }}</div>
                  </li>
                </ul>

              </div>
              <div class="table-cell align-center hidden-xs-down col-sm-3" :class="{'more-info-block':moreInfo}">
                <div class="turnover">
                  {{ row.turnover }}
                </div>
                <ul v-if="(showMore == row.schoolId || showMore == row.cityId || showMore == row.orgId) && moreInfo" class="mt-3 dropdown-menu show-more-organization-statistics"
                    aria-labelledby="dLabel">
                  <li v-if="item.segment_name !== 'vehicle_segments.'" v-for="(item,j) in data.moreInfo[row.name]">

                    <div>{{ item.turnover }}</div>

                  </li>
                  <li>
                    <div>{{ data.moreInfo[row.name][0].paket_turnover }}</div>
                  </li>
                </ul>
              </div>
            </div>
        </div>
      <nav v-if="total > 1" aria-label="Page navigation example">
        <ul class="pagination">
          <li class="page-item" v-if="page > 1"><a class="page-link pagination-p-n" @click="prevPage(page - 1)">Previous</a></li>
          <li v-for="index in this.total" class="page-item" @click="movePage(index)" :class="{'active':page === index}">
            <a class="page-link cursor-pointer">{{ index }}</a>
          </li>
          <li class="page-item" v-if="page < total"><a class="page-link pagination-p-n" @click="nextPage(page + 1)">Next</a></li>
        </ul>
      </nav>
        <a v-if="data.topFive.length" class="table-link" href="#">Visa fullständig rapport</a>
        <no-results v-else title="Inga ordrar hittades"></no-results>
        <div v-if="requesting" class="loader-indicator"></div>
    </div>
</template>

<script>
    import moment from 'moment';
    import Api from 'vue-helpers/Api';
    import BarLine from './BarLine.js';
    import SummaryDateRange from './SummaryDateRange';
    import NoResults from 'vue-components/NoResults';
    import Pagination from 'vue-helpers/Pagination';

    export default {
        components: { BarLine, SummaryDateRange, NoResults },
        props: ['organization','moreInfo'],
        data() {
            return {
                showMore:"",
                allDataList:[],
                page:1,
                perPage:10,
                total:0,
                requesting: false,
                data: {
                    display: '',
                    interval: 'monthly',
                    startDate: moment().subtract(6, 'months').format('YYYY-MM-DD'),
                    endDate: moment().format('YYYY-MM-DD'),
                    type: 'city',
                    cityId: -1,
                    orgId: -1,
                    schoolId: -1,
                    dataset: {
                        labels: [],
                        turnover: [],
                        booked: [],
                        cancelled: []
                    },
                    datasetLastYear: {
                        labels: [],
                        turnover: [],
                        booked: [],
                        cancelled: []
                    },
                    topFive: [],
                    moreInfo: [],
                },
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: 'label'
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: .8,
                            drawBorder: false,
                        }],
                        yAxes: [{
                            type: 'linear',
                            display: true,
                            position: 'left',
                            id: 'y-axis-1',
                            scaleLabel: {
                                display: true,
                                labelString: 'Bokningar/Avbokningar',
                                fontFamily: 'proxima-nova',
                                fontSize: 12,
                            },
                            gridLines: {
                                drawBorder: false,
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }, {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            id: 'y-axis-2',
                            scaleLabel: {
                                display: true,
                                labelString: 'Omsättning',
                                fontFamily: 'proxima-nova',
                                fontSize: 12,
                            },
                            gridLines: {
                                drawBorder: false,
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                    },
                    legend: {
                        position: 'top',
                        fullWidth: false,
                        display: true,
                        onClick(e) { e.stopPropagation() },
                        labels: {
                            usePointStyle: true,
                            fontSize: 14,
                            fontStyle: 500,
                            fontFamily: 'proxima-nova',
                            generateLabels: function (chart) {
                                if (chart.data.labels.length && chart.data.datasets.length) {
                                    return chart.data.datasets.map((set, i) => {
                                        return {
                                            text: set.label,
                                            fillStyle: set.backgroundColor,
                                            strokeStyle: 'transparent',
                                            lineWidth: 0,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                } else {
                                    return [];
                                }
                            }
                        },
                    },
                },

            }
        },
        mounted() {
            this.request();
        },
        computed: {
            ids() {
                return [
                    this.data.cityId,
                    this.data.orgId,
                    this.data.schoolId
                ]
            },
            chartData() {

              if (this.data.interval !== "yearly") {
                return this.data.dataset ? {
                    labels: this.data.dataset.labels || [], // REPLACE with array for interval labels
                    datasets: [{
                        type: 'line',
                        label: 'Förra året Omsättning',
                        data: this.data.datasetLastYear.turnover || [], // REPLACE with array for turnover
                        backgroundColor: '#4fc1a1',
                        borderColor: '#4fc175',
                        lineTension: 0,
                        pointRadius: 4,
                        fill: 'true',
                        yAxisID: 'y-axis-2'
                      },
                      {
                        type: 'line',
                        label: 'Omsättning',
                        data: this.data.dataset.turnover || [], // REPLACE with array for turnover
                        backgroundColor: '#4F7FC1',
                        borderColor: '#4F7FC1',
                        lineTension: 0,
                        pointRadius: 4,
                        fill: 'true',
                        yAxisID: 'y-axis-2'
                      },
                      {
                        type: 'bar',
                        label: 'Förra året Bokningar',
                        backgroundColor: '#6f052a',
                        data: this.data.datasetLastYear.booked || [], // REPLACE with array for booked courses
                        yAxisID: 'y-axis-1'
                      },
                      {
                        type: 'bar',
                        label: 'Bokningar',
                        backgroundColor: '#8AAAE9',
                        data: this.data.dataset.booked || [], // REPLACE with array for booked courses
                        yAxisID: 'y-axis-1'
                      },
                      {
                        type: 'bar',
                        label: 'Förra året Avbokningar',
                        backgroundColor: '#f2ddbd',
                        data: this.data.datasetLastYear.cancelled || [], // REPLACE with array for cancelled courses
                        yAxisID: 'y-axis-1'
                      },
                      {
                        type: 'bar',
                        label: 'Avbokningar',
                        backgroundColor: '#BDD9F2',
                        data: this.data.dataset.cancelled || [], // REPLACE with array for cancelled courses
                        yAxisID: 'y-axis-1'
                      }]
                } : {};
              } else {
                return this.data.dataset ? {
                  labels: this.data.dataset.labels || [], // REPLACE with array for interval labels
                  datasets: [{
                    type: 'line',
                    label: 'Omsättning',
                    data: this.data.dataset.turnover || [], // REPLACE with array for turnover
                    backgroundColor: '#4F7FC1',
                    borderColor: '#4F7FC1',
                    lineTension: 0,
                    pointRadius: 4,
                    fill: 'true',
                    yAxisID: 'y-axis-2'
                  },
                  {
                    type: 'bar',
                    label: 'Bokningar',
                    backgroundColor: '#8AAAE9',
                    data: this.data.dataset.booked || [], // REPLACE with array for booked courses
                    yAxisID: 'y-axis-1'
                  },
                  {
                    type: 'bar',
                    label: 'Avbokningar',
                    backgroundColor: '#BDD9F2',
                    data: this.data.dataset.cancelled || [], // REPLACE with array for cancelled courses
                    yAxisID: 'y-axis-1'
                  }]
                } : {};
              }
            },
            routeData() {
                return {
                    startDate: this.data.startDate,
                    endDate: this.data.endDate,
                    granularity: this.data.interval,
                    type: this.data.type,
                    cityId: this.data.cityId,
                    orgId: this.data.orgId,
                    schoolId: this.data.schoolId,
                    organization: this.organization,
                }
            }
        },
        methods: {
            changeInterval(interval) {
                if (interval !== this.data.interval) {
                    this.data.interval = interval;
                    this.request()
                }
            },
            changeRange(range) {
                if (this.data.startDate !== range.startDate || this.data.endDate !== range.endDate) {
                    this.data.startDate = range.startDate;
                    this.data.endDate = range.endDate;
                    this.request()
                }
            },
            changeType(type) {
                if (type !== this.data.type) {
                    this.data.type = type;
                    this.data.cityId = -1;
                    this.data.schoolId = -1;
                    this.data.orgId = -1;
                    this.page = 1;
                    this.request();
                }
            },
            tableLink(row) {
                switch (this.data.type) {
                    case 'city':
                        this.data.cityId = row.cityId;
                        break;
                    case 'organization':
                        this.data.orgId = row.orgId;
                        break;
                    case 'school':
                        this.data.schoolId = row.schoolId;
                        break;
                }
                if (this.data.type === 'city') {
                    this.data.type = 'organization'
                } else {
                    this.data.type = 'school'
                }
                this.request()
            },
            request() {
                this.requesting = true;
                Api.orderStatistics(this.routeData)
                    .then(response => {
                        this.requesting = false;
                        if (response.ok) {
                            this.data = response.body;
                            this.allDataList = this.data.topFive;
                            this.total = Math.ceil(this.allDataList.length / this.perPage);
                            this.data.topFive = Pagination.dataForPage(this.page, this.allDataList, this.perPage);
                        }
                    }).catch(error => {
                        this.requesting = false;
                    });
            },
          movePage(page) {
            this.page = page;
            this.data.topFive = Pagination.dataForPage(page, this.allDataList, this.perPage);
          },
          prevPage(page) {
            page = page > 0 ? page : 1;
            this.page = page;
            this.data.topFive = Pagination.dataForPage(page, this.allDataList, this.perPage);
          },
          nextPage(page) {
            page = page > this.total ? this.total : page;
            this.page = page;
            this.data.topFive = Pagination.dataForPage(page, this.allDataList, this.perPage);
          },
          showMoreHandle(value) {
            this.showMore = value == this.showMore ? "" : value;
          }
        }
    }

</script>

<style scoped>

.top-bar-margin-top{
  margin-top: 1rem!important;
  display: block;
  /*margin-left: 3.5rem;*/
}
</style>