import { Bar } from 'vue-chartjs'
import $ from 'jquery'

export default Bar.extend({
    name: 'BarLineChart',
    props: [
        'data',
        'options',],
    created() {
    },
    mounted() {
        this.renderChart(this.data, this.options);
    },
    watch: {
        data() {
            this._chart.data = this.data;
            this._chart.update();
        }
    },
})
