define([
    'jquery',
    'chartJs',
    'jquery-ui-modules/widget',
    'moment'
], function ($, Chart) {
    'use strict';
    $.widget('magerubik.diagramsChart', {
        options: {
            updateUrl: '',
			chartType: 'bar',
			type: null,
            priceUtils: '',
            periodSelect: null,
			htmlElement: null,
			precision: 0,
            periodUnits: []
        },
        chart: null,
        _create: function () {
            this.createChart();
            if (this.options.periodSelect) {
                $(document).on('change', this.options.periodSelect, this.refreshChartData.bind(this));
                this.period = $(this.options.periodSelect).val();
            }
        },
        createChart: function () {
            this.refreshChartData();
        },
        refreshChartData: function () {
            var data = {};
            if (this.options.type) data.type = this.options.type;
            if (this.options.periodSelect) this.period = data.period = $(this.options.periodSelect).val();
			data.chartType = this.options.chartType;
            $.ajax({
                url: this.options.updateUrl,
                showLoader: true,
                data: data,
                dataType: 'json',
                type: 'POST',
                success: this.updateChart.bind(this)
            });
        },
        updateChart: function (response) {
			if(response){
				if (this.chart) {
				  this.chart.destroy();
				  this.chart = new Chart(this.element, this.getChartSettings());
				} else {
					this.chart = new Chart(this.element, this.getChartSettings());
				}
				if (this.options.chartType=='bar') {					
					this.chart.data.datasets[0].data = response.data;
					this.chart.data.datasets[0].label = response.label;
				} else {
					if(response.html!='No Data') {
						this.chart.data.labels = response.name_arr;
						this.chart.data.datasets[0].backgroundColor = response.chco;
						this.chart.data.datasets[0].data = response.percentage_arr;
					}
				}
				if (this.options.htmlElement && response.html!='No Data') {
					$(this.options.htmlElement).html(response.html);
				} else if (response.html=='No Data') {
					$(this.options.htmlElement).html('<p><strong style="color: #ff8f00;padding-left: 2rem;">' + response.html + ' Found</strong></p>');
				}
				this.chart.update();
			}	
        },
        getChartSettings: function () {
            if(this.options.chartType=='doughnut'){
				return {
					type: this.options.chartType,
					data: {
					  labels: [],
					  datasets: [{
						backgroundColor: ['#49763C'],
						data: [100],
						borderWidth: [1, 1, 1, 1]
					  }]
					},
					options: {
						maintainAspectRatio: false,
						cutoutPercentage: 75,
						plugins: {
							legend: {
							  position: 'bottom',
							  display: false,
							  labels: {
								boxWidth:8
							  }
							}
						},
						tooltips: {
						  displayColors:false,
						}
					}
				};
			} else {
				return {
					type: this.options.chartType,
					data: {
						datasets: [{
							data: [],
							backgroundColor: '#f1d4b3',
							borderColor: '#eb5202',
							borderWidth: 1
						}]
					},
					options: {
						"responsive": true,
						"maintainAspectRatio": false,
						legend: {
							onClick: this.handleChartLegendClick,
							position: 'bottom'
						},
						scales: {
							xAxes: [{
								offset: true,
								type: 'time',
								ticks: {
									autoSkip: true,
									source: 'data'
								}
							}],
							yAxes: [{
								ticks: {
									beginAtZero: true,
									precision: this.options.precision
								}
							}]
						}
					}
				};
			}
			
        },
        handleChartLegendClick: function () {
            // don't hide dataset on clicking into legend item
        }
    });
    return $.magerubik.diagramsChart;
});