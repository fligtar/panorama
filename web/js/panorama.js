
$(document).ready(function() {
    // Perma-urls via hash
    if (window.location.hash) {
        $('nav li a[href="' + window.location.hash + '"]').click();
    }
});

var panorama = {
    currentReport: null,
    
    getReport: function(report, caller) {
        panorama.currentReport = report;
        $('nav .selected').removeClass('selected');
        $('#content .filters').remove();
        
        $(caller).addClass('loading').addClass('selected');
        
        // If the report has filters, we have to load those before we load any graphs
        if (report.filters) {
            panorama.loadFilters(report);
            return;
        }

        if (report.type == 'html') {
            panorama.getHTML(report);
        }
        else {
            panorama.getGraphs(report);
        }
    },
    
    getHTML: function(report) {
        $('#content .graph-container').remove();
        
        $('#content').append('<div class="graph-container"></div>');
        $('#content .graph-container').load(report.url, function() {
            $('nav .loading').removeClass('loading');
        });
    },
    
    getGraphs: function(report) {
        $('#content .graph-container').remove();
        
        for (var i in report.graphs) {
            panorama.reportAndChartIt(report.graphs[i]);
        }
    },
    
    loadFilters: function(report) {
        $.getJSON(report.filters.url, function(data) {
            $('#content').append('<div class="filters"></div>');
            var filtersel = $('#content .filters');
            $.each(data, function(key, values) {
                filtersel.append('<label>' + key + ': <select name="' + key + '"></select></label>');
               $.each (values, function (i, value) {
                   filtersel.find('select[name="' + key + '"]').append('<option' + (report.filters.defaults[key] == value ? ' selected="selected"' : '') + '>' + value + '</option>');
               });
            });
            filtersel.append('<button onclick="panorama.applyFilters();">apply</button>');
            panorama.applyFilters(report);
        });
    },
    
    applyFilters: function(report) {
        if (!report)
            report = panorama.currentReport;
        
        var filters = {};
        $('#content .filters select').each(function(i, select) {
            filters[$(select).attr('name')] = $(select).val();
        });
        
        if (report.type == 'html') {
            var url = report.base_url;
            $.each(filters, function(key, val) {
                url = url.replace('%' + key + '%', val);
            });
            report.url = url;
            
            panorama.getHTML(report);
        }
        else {
            $.each(report.graphs, function(i) {
                var url = report.graphs[i].base_url;
                var subtitle = report.graphs[i].options.subtitle.base_text;
            
                $.each(filters, function(key, val) {
                    url = url.replace('%' + key + '%', val);
                    subtitle = subtitle.replace('%' + key + '%', val);
                });
            
                report.graphs[i].url = url;
                report.graphs[i].options.subtitle.text = subtitle;
            });
        
            panorama.getGraphs(report);
        }
    },
    
    reportAndChartIt: function(chart) {
        panorama.newContainer(chart.options.chart.renderTo, chart.url);
        
        $.get(chart.url, function(data) {
            chart.options.series = panorama.getSeriesFromCSV(data, chart);
            
            panorama.createChart(chart.options);
            
            // This will remove loading after the first chart loads
            // Don't care enough about the second
            $('nav .loading').removeClass('loading');
        });
    },
    
    getSeriesFromCSV: function(csv, chart) {
        var series = [];
        
        // Split the lines
        var lines = csv.split('\n');

        // Go through each line
        $.each(lines, function(lineNo, line) {
            var items = line.split(',');

            // First line is the header with series names
            if (lineNo == 0) {
                $.each(items, function(itemNo, item) {
                    if (itemNo > 0) {
                        var s = {name: item, data: []};
                        
                        // Populate series options for this chart
                        if ('*' in chart.specificSeries) {
                            for (var key in chart.specificSeries['*']) {
                                s[key] = chart.specificSeries['*'][key];
                            }
                        }
                        
                        // Populate series options for this series
                        if (itemNo in chart.specificSeries) {
                            for (var key in chart.specificSeries[itemNo]) {
                                s[key] = chart.specificSeries[itemNo][key];
                            }
                        }
                        
                        series.push(s);
                    }                        
                });
            }
            // Other lines are the data with date/label in first column
            else {
                var label;
                $.each(items, function(itemNo, item) {
                    if (itemNo == 0) {
                        if (item.indexOf('-') != -1) {
                            // Split the date if it's a date
                            var dateparts = item.split('-');
                            label = Date.UTC(dateparts[0], dateparts[1] - 1, dateparts[2]);
                        }
                        else {
                            label = item;
                        }
                    } else {
                        series[itemNo - 1].data.push([label, parseFloat(item)]);
                    }
                });
            }
        });
        
        return series;
    },
    
    newContainer: function(id, csv) {
        $('#content').append('<div class="graph-container loading"><div id="' + id + '" class="graph"></div><p class="csv"><a href="' + csv + '" target="_blank">CSV</a></div>');
    },
    
    createChart: function(options) {
    	return new Highcharts.Chart(options);
    }
};

// Set default chart options
Highcharts.setOptions({
	chart: {
		renderTo: 'chart',
		zoomType: 'x',
        defaultSeriesType: 'line',
        marginBottom: 75,
        marginRight: 15,
        events: {
            load: function() {
                $('#' + this.options.chart.renderTo).parent().removeClass('loading');
            }
        }
	},
    title: {
		text: ''
	},
    subtitle: {
		text: null
	},
	credits: {
	    enabled: false
	},
	xAxis: {
		type: 'datetime',
		maxZoom: 14 * 24 * 3600000, // fourteen days
		title: {
			text: null
		},
		dateTimeLabelFormats: {
		    day: '%b %e'
		}
	},
	yAxis: {
		title: {
			text: null,
		},
        labels: { formatter: function() {
            return '' + Highcharts.numberFormat(this.value, 0);
        }},
		min: 0.6,
		startOnTick: false,
		showFirstLabel: false
	},
	tooltip: {
		formatter: function() {
			return ''+
				Highcharts.dateFormat('%A, %b %e, %Y', this.x) + '<br/><b>' +
				Highcharts.numberFormat(this.y, 0) + '</b> (' + this.series.name + ')';
		}
	},
    legend: {
		layout: 'horizontal',
		align: 'center',
		verticalAlign: 'bottom',
		x: 0,
		y: -5,
		borderWidth: 1
	},
	plotOptions: {
		area: {
			marker: {
				enabled: false,
				states: {
					hover: {
						enabled: true,
						radius: 3
					}
				}
			},
			shadow: false
		},
		line: {
		    marker: {
		        enabled: false,
		        states: {
					hover: {
						enabled: true,
						radius: 3
					}
				}
		    },
		    shadow: false
		},
		spline: {
		    marker: {
		        enabled: false,
		        states: {
					hover: {
						enabled: true,
						radius: 3
					}
				}
		    },
		    shadow: false
		},
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			dataLabels: {
				enabled: true,
				formatter: function() {
					if (this.y > 5) return this.point.name;
				}
			}
		}
	},
	series: []
});