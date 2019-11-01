<script>
import { Line, mixins } from 'vue-chartjs'
import dayjs from 'dayjs'
import 'dayjs/locale/de'
import 'dayjs/locale/fr'

export default {
    extends: Line,
    mixins: [mixins.reactiveData],
    data() {
        return {
            rendered: false,
            chartData: {
                labels: [],
                tooltipLabels: [],
                datasets: [{
                    data: []
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#b2b2b2',
                            padding: 6
                        },
                        gridLines: {
                            display: true,
                            tickMarkLength: 5,
                            zeroLineColor: '#b2b2b2',
                            color: '#dfdfdf'
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: true,
                            tickMarkLength: 6,
                        },
                        ticks: {
                            fontColor: '#b2b2b2',
                            padding: 6
                        },
                    }]
                },
                elements: {
                    line: {
                        tension: 0,
                        backgroundColor: 'rgba(211, 222, 180, 0.5)',
                        borderColor: '#a7bd69',
                    },
                    point: {
                        radius: 5,
                        backgroundColor: '#ffffff',
                        borderColor: '#a7bd69',
                        borderWidth: 3,
                        hoverRadius: 6,
                    },
                },
                legend: {
                    display: false
                },
                tooltips: {
                    mode: 'label',
                    backgroundColor: '#282c34',
                    xPadding: 12,
                    yPadding: 8,
                    caretPadding: 10,
                    cornerRadius: 4,
                    caretSize: 6,
                    displayColors: false,
                    titleFontStyle: 'normal',
                    titleFontColor: '#999999',
                    callbacks: {
                        title(tooltipItem, data) {
                            return data['tooltipLabels'][tooltipItem[0]['index']]
                        },
                        label(tooltipItem, data) {
                            return data['datasets'][0]['data'][tooltipItem['index']] + ' visits'
                        },
                    }
                },
                hover: {
                    animationDuration: 0,
                },
                responsive: true,
                maintainAspectRatio: false
            },
        }
    },
    props: {
        currentPeriod: String,
        lang: String,
    },
    computed: {
        config() {
            let config = {}

            if(this.currentPeriod == 'year') {
                config = {
                    method: 'VisitsSummary.getUniqueVisitors',
                    period: 'month',
                    date: 'last12',
                }
            }
            if(this.currentPeriod == 'month') {
                config = {
                    method: 'VisitsSummary.getUniqueVisitors',
                    period: 'day',
                    date: 'last31',
                }
            }
            if(this.currentPeriod == 'week') {
                config = {
                    method: 'VisitsSummary.getUniqueVisitors',
                    period: 'day',
                    date: 'last7',
                }
            }
            if(this.currentPeriod == 'day') {
                config = {
                    method: 'VisitTime.getVisitInformationPerServerTime',
                    period: 'day',
                    date: 'today',
                }
            }

            return config
        }
    },
    watch: {
        currentPeriod: {
            immediate: true,
            handler(newVal, oldVal) {
                this.syncContent()
            }
        },
        lang: {
            immediate: true,
            handler(newVal, oldVal) {
                dayjs.locale(newVal)
            }
        },
    },
    methods: {
        syncContent() {
            this.$emit('chartIsLoading')
            this.$api
                .get('matomo-panel/get-chart-data', this.config)
                .then(response => {
                    let self = this
                    let formatedLabels
                    let tooltipLabels
                    let dataset
                    let d = new Date()

                    // keep only the data of the current year
                    if(this.currentPeriod == 'year') {
                        let currentYear = d.getFullYear()

                        let labels = Object.keys(response).filter(key => {
                            return key.split('-')[0] == currentYear;
                        })
                        formatedLabels = labels.map(key => {
                            return dayjs(key).format('MMMM')
                        })
                        tooltipLabels = labels.map(key => {
                            return dayjs(key).format('MMMM YYYY')
                        })
                        dataset = labels.map(key => {
                            return response[key];
                        })
                    }
                    // keep only the data of the current month
                    if(this.currentPeriod == 'month') {
                        let currentMonth = ('0' + (d.getMonth() + 1)).slice(-2)

                        let labels = Object.keys(response).filter(key => {
                            return parseInt(key.split('-')[1]) == parseInt(currentMonth);
                        })
                        formatedLabels = labels.map(key => {
                            let date = key.split('-')
                            return date[2] +'/'+ date[1]
                        })
                        tooltipLabels = labels.map(key => {
                            return dayjs(key).format('dddd, DD MMMM YYYY')
                        })
                        dataset = labels.map(key => {
                            return response[key];
                        })
                    }
                    // keep only the data of the current week
                    if(this.currentPeriod == 'week') {
                        let currentMonth = ('0' + (d.getMonth() + 1)).slice(-2)

                        let labels = Object.keys(response)
                        formatedLabels = labels.map(key => {
                            let date = key.split('-')
                            return date[2] +'/'+ date[1]
                        })
                        tooltipLabels = labels.map(key => {
                            return dayjs(key).format('dddd, DD MMMM YYYY')
                        })
                        dataset = labels.map(key => {
                            return response[key];
                        })
                    }
                    // keep only the data of the current day
                    if(this.currentPeriod == 'day') {
                        let labels = Object.keys(response)
                        formatedLabels = labels.map(key => {
                            let label = parseInt(key) + 1 + 'h'
                            return isFinite(label) ? label : ''
                        })
                        tooltipLabels = formatedLabels
                        dataset = labels.map(key => {
                            return response[key].nb_uniq_visitors;
                        })
                    }

                    this.chartData.tooltipLabels = tooltipLabels
                    this.chartData.labels = formatedLabels
                    this.chartData.datasets[0].data = dataset

                    if(!this.rendered) {
                        this.renderChart(this.chartData, this.options)
                        this.rendered = true
                    }
                    else {
                        this.$data._chart.update()
                    }

                    this.$emit('chartIsLoaded')
                })
                .catch(error => {
                    this.$emit('chartIsEmpty')
                })
        },
    }
}
</script>
