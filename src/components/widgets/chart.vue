<template>
    <div class="matomo-chart-canvas"><canvas ref="canvas"></canvas></div>
</template>

<script>
import Chart from 'chart.js'
import { markRaw } from 'vue'
import dayjs from 'dayjs'
import 'dayjs/locale/de'
import 'dayjs/locale/fr'
import 'dayjs/locale/es'

export default {
    emits: ['chartIsLoading', 'chartIsLoaded', 'chartIsEmpty'],
    data() {
        return {
            chartInstance: null,
            requestId: 0,
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
        },
        options() {
            return {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: this.b2b2b2,
                            padding: 6
                        },
                        gridLines: {
                            display: true,
                            tickMarkLength: 5,
                            zeroLineColor: this.b2b2b2,
                            color: this.dfdfdf
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: true,
                            tickMarkLength: 6,
                        },
                        ticks: {
                            fontColor: this.b2b2b2,
                            padding: 6
                        },
                    }]
                },
                elements: {
                    line: {
                        tension: 0,
                        backgroundColor: this.accentBackground,
                        borderColor: '#a7bd69',
                    },
                    point: {
                        radius: 5,
                        backgroundColor: this.pointBackground,
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
                    backgroundColor: this.tooltipBackground,
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
            }
        },
        theme() {
            return this.$panel.theme.current
        },
        b2b2b2() {
            return this.theme == 'light' ? '#b2b2b2' : '#929292'
        },
        dfdfdf() {
            return this.theme == 'light' ? '#dfdfdf' : '#464646'
        },
        accentBackground() {
            return this.theme == 'light' ? 'rgba(211, 222, 180, 0.5)' : 'rgba(125, 172, 40, 0.1)'
        },
        pointBackground() {
            return this.theme == 'light' ? '#ffffff' : '#374818'
        },
        tooltipBackground() {
            return this.theme == 'light' ? '#282c34' : '#0f0f0f'
        },
    },
    watch: {
        currentPeriod: {
            handler() {
                this.syncContent()
            }
        },
        lang() { 
            this.syncContent() 
        },
        theme() {
            if(this.chartInstance) {
                this.chartInstance.options = this.options
                this.chartInstance.update()
            }
        }
    },
    mounted() { 
        this.syncContent() 
    },
    beforeUnmount() {
        this.requestId++
        this.chartInstance?.destroy()
        this.chartInstance = null
    },
    methods: {
        syncContent() {
            const id = ++this.requestId
            this.$emit('chartIsLoading')

            return this.$api
                .get('matomo-panel/get-chart-data', this.config)
                .then(response => {
                    if(id !== this.requestId) return
                    if(!response || response.status === 'empty' || response.status === 'error' || !Object.keys(response).length) throw new Error('No chart data')
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
                            return dayjs(key).locale(this.lang || 'en').format('MMMM')
                        })
                        tooltipLabels = labels.map(key => {
                            return dayjs(key).locale(this.lang || 'en').format('MMMM YYYY')
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
                            return dayjs(key).locale(this.lang || 'en').format('dddd, DD MMMM YYYY')
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
                            return dayjs(key).locale(this.lang || 'en').format('dddd, DD MMMM YYYY')
                        })
                        dataset = labels.map(key => {
                            return response[key];
                        })
                    }
                    // keep only the data of the current day
                    if(this.currentPeriod == 'day') {
                        let labels = Object.keys(response)
                        formatedLabels = labels.map(key => {
                            let hour = parseInt(key)
                            return Number.isFinite(hour) ? (hour + 1) + 'h' : ''
                        })
                        tooltipLabels = formatedLabels
                        dataset = labels.map(key => {
                            return response[key].nb_uniq_visitors;
                        })
                    }

                    if(!dataset?.length) throw new Error('No chart data')
                    const data = { labels: formatedLabels, tooltipLabels, datasets: [{ data: dataset }] }
                
                    if(!this.chartInstance) {
                        this.chartInstance = markRaw(new Chart(this.$refs.canvas, {
                            type: 'line', data, options: this.options
                        }))
                    } 
                    else {
                        this.chartInstance.data = data
                        this.chartInstance.update()
                    }

                    this.$emit('chartIsLoaded')
                })
                .catch(error => {
                    if(id === this.requestId) this.$emit('chartIsEmpty')
                })
        },
    }
}
</script>
