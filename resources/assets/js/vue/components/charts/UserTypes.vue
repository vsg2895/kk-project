<template>
    <div class="user-types">
        <h3 class="mb-0" v-if="!title">Användare per typ</h3>
        <h3 class="mb-0" v-if="title"> {{ title }}</h3>
        <doughnut class="chart" :data="dataCollection" :options="doughnutOptions"></doughnut>
    </div>
</template>

<script>
    import Doughnut from './Doughnut.js'

    export default {
        props: ['data', 'title'],
        components: {
            Doughnut
        },
        data() {
            return {
                dataCollection: {},
                doughnutOptions: {
                    showProcent: true,
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'top',
                        fullWidth: false,
                        display: true,
                        onClick(e) { e.stopPropagation() },
                        labels: {
                            usePointStyle: true,
                            fontSize: 14,
                            fontFamily: 'proxima-nova',
                            boxWidth: 12,
                            generateLabels: function (chart) {
                                if (chart.data.labels.length && chart.data.datasets.length) {
                                    let labels = chart.data.labels;
                                    let data = chart.data.datasets[0].data;
                                    let colors = chart.data.datasets[0].backgroundColor;
                                    return labels.map((label, i) => {
                                        return {
                                            text: `${label} ${data[i]}st`,
                                            fillStyle: colors[i],
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
                        }
                    },
                    title: {
                        display: false,
                        text: 'Användare per typ'
                    },
                    animation: {
                        animateScale: false,
                        animateRotate: false
                    },

                }
            }
        },
        mounted() {
            this.fillData()
        },
        methods: {
            fillData() {
                this.dataCollection = {
                    labels: this.data.map(v => {
                        return v[0];
                    }),
                    datasets: [
                        {
                            label: 'Användare per typ',
                            backgroundColor: [
                                '#4F7FC1',
                                '#BDD9F2',
                                '#8AAAE9',
                            ],
                            data: this.data.map(v => { return v[1] }),
                        }
                    ]
                }
            }
            ,
        }
    }

</script>
