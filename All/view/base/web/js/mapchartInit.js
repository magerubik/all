define([
    'jquery',
    'mr_jvectormap',
    'mr_jvectormapWorld'
], function ($) {
    'use strict';
    $.widget('magerubik.mapchartInit', {
        options: {
            updateUrl: '',
            priceUtils: '',
            periodSelect: null,
            htmlElement: null
        },
        _create: function () {
			var self = this;
			var worldMap = new jvm.Map({
				map: 'world_mill_en',
				container: $(self.element),
				backgroundColor: 'transparent',
				borderColor: '#818181',
				borderOpacity: 0.25,
				borderWidth: 1,
				zoomOnScroll: false,
				color: '#009efb',
				regionStyle : {
					initial : {
					  fill : '#008cff'
					}
				},
				series: {
				  regions: [{
					values: {},
					scale: ['#6e4204', '#fc9505'],
					normalizeFunction: 'polynomial'
				  }]
				}, 
				onRegionTipShow: function(e, el, code){
				  var value = worldMap.series.regions[0].values[code] ? worldMap.series.regions[0].values[code] :0;
				  el.html(el.html()+' ('+ self.options.priceUtils + value +')');
				}
			  });
			if (self.options.periodSelect) {
				$(self.options.periodSelect).on('change', function () {
					self.refreshMapData(worldMap);
				});
            }
			self.refreshMapData(worldMap);
        },
		refreshMapData: function (map) {
			var self = this;
			var data = {};
            if (this.options.periodSelect) {
                this.dateType = data.dateType = $(this.options.periodSelect).val();
            }
			data.chartType = 'location';	
            $.ajax({
                url: this.options.updateUrl,
                showLoader: true,
                data: data,
                type : 'post',
                dataType : 'json',
                success: function (res) {
					var regions = map.series.regions[0];
						for (const key in regions.values) {
						  delete regions.values[key];
						  map.regions[key].element.shape.style.current.fill = '#008cff';
						  map.regions[key].element.shape.style.selected.fill = '#008cff';
						  map.regions[key].element.shape.properties.fill = '#008cff';
						}
						$(self.element).find('path.jvectormap-region').attr('fill','#008cff');
						regions.params.min = 1;
						regions.params.max = 6000;
						regions.setValues(res.country_sale_arr);
						if (self.options.htmlElement) $(self.options.htmlElement).html(res.html);
                },
                error: function () {
                    alert({
                        content: 'err'
                    });
                }
            });
			
        }
    });
    return $.magerubik.mapchartInit;
});
