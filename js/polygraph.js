
$(document).ready(function() {
    // Perma-urls via hash
    if (window.location.hash) {
        $('nav li a[href="' + window.location.hash + '"]').click();
    }
});

var polygraph = {
    
    getReport: function(report, caller) {
        $('nav .selected').removeClass('selected');
        $('#content .graph').remove();
        
        $(caller).addClass('selected').addClass('loading');
        
        for (var i in report.graphs) {
            var chart = report.graphs[i];
            
            $.get(chart.url, function(data) {
                chart.options.series = polygraph.getSeriesFromCSV(data, chart);
                
                polygraph.newContainer(chart.options.chart.renderTo);
                polygraph.createChart(chart.options);
                
                // This will remove loading after the first chart loads
                // Don't care enough about the second
                $('nav .loading').removeClass('loading');
            });
        }
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
                        console.log(s);
                    }                        
                });
            }
            // Other lines are the data with date in first column
            else {
                var date;
                $.each(items, function(itemNo, item) {
                    if (itemNo == 0) {
                        // Split the date
                        var dateparts = item.split('-');
                        date = Date.UTC(dateparts[0], dateparts[1], dateparts[2]);
                    } else {
                        series[itemNo - 1].data.push([date, parseInt(item)]);
                    }
                });
            }
        });
        
        return series;
    },
    
    newContainer: function(id) {
        $('#content').append('<div id="' + id + '" class="graph"></div>');
    },
    
    createChart: function(options) {
    	return new Highcharts.Chart(options);
    }
};

var reports = {
    addons: {
        creation: {
            graphs: [
                {
                    url: 'reports/addons/creation.php',
                    options: {
                        chart: {
                            renderTo: 'addon-creation',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Add-ons Created per Day' },
                        subtitle: { text: 'by Add-on Type' },
                        yAxis: {
                            title: { text: 'Add-ons Created' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': '+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                        '*': {
                            visible: false
                        },
                        1: {
                            visible: true
                        }
                    }
                }
            ]
        }
    }
};

// Set default chart options
Highcharts.setOptions({
	chart: {
		renderTo: 'chart',
		zoomType: 'x',
		marginRight: 150,
        marginBottom: 25,
        defaultSeriesType: 'line'
	},
    title: {
		text: ''
	},
    subtitle: {
		text: ''
	},
	credits: {
	    enabled: false
	},
	xAxis: {
		type: 'datetime',
		maxZoom: 14 * 24 * 3600000, // fourteen days
		title: {
			text: null
		}
	},
	yAxis: {
		title: {
			text: ''
		},
		min: 0.6,
		startOnTick: false,
		showFirstLabel: false
	},
	tooltip: {
		formatter: function() {
			return ''+
				Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': '+
				Highcharts.numberFormat(this.y, 0);
		}
	},
    legend: {
		layout: 'vertical',
		align: 'right',
		verticalAlign: 'top',
		x: -10,
		y: 100,
		borderWidth: 0
	},
	plotOptions: {
		area: {
			fillColor: {
				linearGradient: [0, 0, 0, 300],
				stops: [
					[0, '#4572A7'],
					[1, 'rgba(2,0,0,0)']
				]
			},
			lineWidth: 1,
			marker: {
				enabled: false,
				states: {
					hover: {
						enabled: true,
						radius: 5
					}
				}
			},
			shadow: false,
			states: {
				hover: {
					lineWidth: 1						
				}
			}
		},
		line: {
		    lineWidth: 1,
		    marker: { enabled: false },
		    shadow: false
		}
	},
	series: []
});