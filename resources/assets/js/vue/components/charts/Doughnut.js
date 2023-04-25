import { Doughnut } from 'vue-chartjs'

window.Chart.plugins.register({
    afterDatasetsDraw: function (chart, easing) {
        if (chart.options.showProcent) {
            let ctx = chart.ctx;
            let sum = 0;

            var fontSize = 14;
            var fontStyle = 'normal';
            var fontFamily = 'Raleway';


            chart.data.datasets.forEach(function (dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach(function (element, index) {
                        if (!element.hidden) {
                            sum = sum + dataset.data[index];
                        }
                    });
                }
            });

            chart.data.datasets.forEach(function (dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach(function (element, index) {
                        if (!element.hidden) {
                            ctx.fillStyle = 'rgb(255, 255, 255)';
                            ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                            let pro = ((0.0 + dataset.data[index]) / sum) * 100;
                            var dataString = pro.toFixed(2).toString() + '%';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            var padding = 5;
                            var position = element.tooltipPosition();
                            ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                        }
                    });
                }
            });
        }
    }
});

export default Doughnut.extend({
    name: 'DoughnutChart',
    props: ['data', 'options'],
    mounted() {
        this.renderChart(this.data, this.options);
        window.addEventListener('resize', () => {
            this._chart.destroy()
            this.renderChart(this.data, this.options)
        })
    },
    watch: {
        data: function () {
            this._chart.destroy()
            this.renderChart(this.data, this.options)
        }
    }
})

