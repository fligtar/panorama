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
                        subtitle: { text: 'by add-on type' },
                        yAxis: {
                            title: { text: 'Add-ons Created' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons</b> (' + this.series.name + ')';
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
        },
        downloads: {
            graphs: [
                {
                    url: 'reports/addons/downloads.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-downloads-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Add-on Downloads per Day' },
                        subtitle: { text: 'by add-on type' },
                        yAxis: {
                            title: { text: 'Downloads' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' downloads</b> (' + this.series.name + ')';
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
                },
                {
                    url: 'reports/addons/downloads.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-downloads-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Add-on Downloads by Type' },
                        subtitle: { text: 'yesterday' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' downloads (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/addons/downloads-sources.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-downloads-sources-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Add-on Downloads per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: 'Downloads' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' downloads</b> (' + this.series.name + ')';
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
                },
                {
                    url: 'reports/addons/downloads-sources.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-downloads-sources-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Add-on Downloads by Source' },
                        subtitle: { text: 'yesterday' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' downloads (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        eulas: {
            graphs: [
                {
                    url: 'reports/addons/eulas.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-eulas-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'EULA Distribution' },
                        subtitle: { text: 'real-time data' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' add-ons (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/addons/eulas.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-eulas-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'EULA Distribution per Day' },
                        yAxis: {
                            title: { text: 'Total Add-ons' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        impressions: {
            graphs: [
                {
                    url: 'reports/addons/impressions.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-impressions-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Adblock Plus Impressions per Day' },
                        subtitle: { text: 'by impression source' },
                        yAxis: {
                            title: { text: 'ABP Impressions' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' impressions</b> (' + this.series.name + ')';
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
                },
                {
                    url: 'reports/addons/impressions.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-impressions-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Adblock Plus Impressions by Known Source' },
                        subtitle: { text: 'yesterday' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' impressions (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        packager: {
            graphs: [
                {
                    url: 'reports/addons/packager.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-packager-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Add-on Packager Use per Day' },
                        subtitle: { text: 'by UI elements' },
                        yAxis: {
                            title: { text: 'Add-ons Created' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons created</b> (' + this.series.name + ')';
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
                },
                {
                    url: 'reports/addons/packager.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-packager-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Add-on Packager Use by UI Element' },
                        subtitle: { text: 'yesterday' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' add-ons created (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        privacy: {
            graphs: [
                {
                    url: 'reports/addons/privacy.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-privacy-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Privacy Policy Distribution' },
                        subtitle: { text: 'real-time data' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' add-ons (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/addons/privacy.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-privacy-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Privacy Policy Distribution per Day' },
                        yAxis: {
                            title: { text: 'Total Add-ons' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        publicstats: {
            graphs: [
                {
                    url: 'reports/addons/publicstats.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-publicstats-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Public Stats Distribution' },
                        subtitle: { text: 'real-time data' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' add-ons (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/addons/publicstats.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-publicstats-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Public Stats Distribution per Day' },
                        yAxis: {
                            title: { text: 'Total Add-ons' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        reviews: {
            graphs: [
                {
                    url: 'reports/addons/reviews.php',
                    options: {
                        chart: {
                            renderTo: 'addon-reviews',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Reviews Added per Day' },
                        subtitle: { text: 'by add-on type' },
                        yAxis: {
                            title: { text: 'Reviews Created' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' reviews</b> (' + this.series.name + ')';
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
                },
                {
                    url: 'reports/addons/ratings.php',
                    options: {
                        chart: {
                            renderTo: 'addon-ratings',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Ratings Added per Day' },
                        subtitle: { text: 'by rating' },
                        yAxis: {
                            title: { text: 'Ratings Added' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' ratings</b> (' + this.series.name + ')';
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
        },
        status: {
            graphs: [
                {
                    url: 'reports/addons/status.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-status-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Add-on Status per Day' },
                        subtitle: { text: '' },
                        yAxis: {
                            title: { text: 'Add-ons' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/addons/status.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-status-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Add-on Status' },
                        subtitle: { text: 'yesterday' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' add-ons (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        tags: {
            graphs: [
                {
                    url: 'reports/addons/tags.php',
                    options: {
                        chart: {
                            renderTo: 'addon-tags',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Tags Added per Day' },
                        subtitle: { text: 'by add-on type' },
                        yAxis: {
                            title: { text: 'Tags Added' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' tags</b> (' + this.series.name + ')';
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
        },
        viewsource: {
            graphs: [
                {
                    url: 'reports/addons/viewsource.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'addon-viewsource-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Online Source Viewing Distribution' },
                        subtitle: { text: 'real-time data' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' add-ons (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/addons/viewsource.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'addon-viewsource-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Online Source Viewing Distribution per Day' },
                        yAxis: {
                            title: { text: 'Total Add-ons' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        }
    },
    collections: {
        creation: {
            graphs: [
                {
                    url: 'reports/collections/creation.php',
                    options: {
                        chart: {
                            renderTo: 'collection-creation',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Collections Created per Day' },
                        subtitle: { text: 'by collection type' },
                        yAxis: {
                            title: { text: 'Collections Created' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' collections</b> (' + this.series.name + ')';
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
        },
        privacy: {
            graphs: [
                {
                    url: 'reports/collections/privacy.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'collection-privacy-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Collection Privacy Distribution' },
                        subtitle: { text: 'real-time data' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' collections (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/collections/privacy.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'collection-privacy-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Collection Privacy Distribution per Day' },
                        yAxis: {
                            title: { text: 'Total Collections' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' collections</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        publishing: {
            graphs: [
                {
                    url: 'reports/collections/publishing.php',
                    options: {
                        chart: {
                            renderTo: 'collection-publishing',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Add-ons Published per Day' },
                        subtitle: { text: 'by collection type' },
                        yAxis: {
                            title: { text: 'Add-ons Published' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' add-ons</b> (' + this.series.name + ')';
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
        },
        votes: {
            graphs: [
                {
                    url: 'reports/collections/votes.php',
                    options: {
                        chart: {
                            renderTo: 'collection-votes',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Collection Votes per Day' },
                        subtitle: { text: 'by collection type' },
                        yAxis: {
                            title: { text: 'Collection Votes' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' votes</b> (' + this.series.name + ')';
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
                },
                {
                    url: 'reports/collections/votes-vote.php',
                    options: {
                        chart: {
                            renderTo: 'collection-votes-vote',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Collection Votes per Day' },
                        subtitle: { text: 'by vote' },
                        yAxis: {
                            title: { text: 'Collection Votes' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' votes</b> (' + this.series.name + ')';
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
        },
        watchers: {
            graphs: [
                {
                    url: 'reports/collections/watchers.php',
                    options: {
                        chart: {
                            renderTo: 'collection-watchers',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Collections Watched per Day' },
                        subtitle: { text: 'by collection type' },
                        yAxis: {
                            title: { text: 'Collections Watched' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' collections</b> (' + this.series.name + ')';
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
    },
    contributions: {
        summary: {
            graphs: [
                {
                    url: 'reports/contributions/summary.php?graph=total',
                    options: {
                        chart: {
                            renderTo: 'contributions-summary-total',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Received per Day' },
                        subtitle: { text: null },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
                            }
                        },
                        legend: {
                    		enabled: false
                    	},
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/summary.php?graph=amt',
                    options: {
                        chart: {
                            renderTo: 'contributions-summary-amt',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Average, Maximum, and Minimum Contributions per Day' },
                        subtitle: { text: null },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/summary.php?graph=suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-summary-suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Response to Suggested Contributions per Day' },
                        subtitle: { text: null },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/summary.php?graph=tx',
                    options: {
                        chart: {
                            renderTo: 'contributions-summary-tx',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Transaction Completion per Day' },
                        subtitle: { text: null },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' transactions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                }
            ]
        },
        annoyance: {
            graphs: [
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=amt_earned',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-amt_earned',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Received per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=amt_avg',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-amt_avg',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Average Contribution Amount per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=amt_min',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-amt_min',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Minimum Contribution Amount per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=amt_max',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-amt_max',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Maximum Contribution Amount per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=amt_eq_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-amt_eq_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Equal to Suggested Amount per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=amt_gt_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-amt_gt_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Greater than Suggested Amount per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=amt_lt_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-amt_lt_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Less than Suggested Amount per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=tx_success',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-tx_success',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Succeeded per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/annoyance.php?graph=history&field=tx_abort',
                    options: {
                        chart: {
                            renderTo: 'contributions-annoyance-tx_abort',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Aborted per Day' },
                        subtitle: { text: 'by annoyance level' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                }
            ]
        },
        recipients: {
            graphs: [
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=amt_earned',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-amt_earned',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Received per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=amt_avg',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-amt_avg',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Average Contribution Amount per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=amt_min',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-amt_min',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Minimum Contribution Amount per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=amt_max',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-amt_max',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Maximum Contribution Amount per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=amt_eq_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-amt_eq_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Equal to Suggested Amount per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=amt_gt_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-amt_gt_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Greater than Suggested Amount per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=amt_lt_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-amt_lt_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Less than Suggested Amount per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=tx_success',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-tx_success',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Succeeded per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/recipients.php?graph=history&field=tx_abort',
                    options: {
                        chart: {
                            renderTo: 'contributions-recipients-tx_abort',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Aborted per Day' },
                        subtitle: { text: 'by recipient' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                }
            ]
        },
        sources: {
            graphs: [
                {
                    url: 'reports/contributions/sources.php?graph=history&field=amt_earned',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-amt_earned',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Received per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=amt_avg',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-amt_avg',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Average Contribution Amount per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=amt_min',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-amt_min',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Minimum Contribution Amount per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=amt_max',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-amt_max',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Maximum Contribution Amount per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null },
                            labels: { formatter: function() {
                                return '$' + Highcharts.numberFormat(this.value, 2);
                            }}
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>$'+
                            		Highcharts.numberFormat(this.y, 2) +'</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=amt_eq_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-amt_eq_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Equal to Suggested Amount per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=amt_gt_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-amt_gt_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Greater than Suggested Amount per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=amt_lt_suggested',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-amt_lt_suggested',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Less than Suggested Amount per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=tx_success',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-tx_success',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Succeeded per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                },
                {
                    url: 'reports/contributions/sources.php?graph=history&field=tx_abort',
                    options: {
                        chart: {
                            renderTo: 'contributions-sources-tx_abort',
                            defaultSeriesType: 'area',
                            marginBottom: 60,
                            marginRight: 15
                        },
                        title: { text: 'Contributions Aborted per Day' },
                        subtitle: { text: 'by source' },
                        yAxis: {
                            title: { text: null }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' contributions</b> (' + this.series.name + ')';
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
                        series: []
                    },
                    specificSeries: {}
                }
            ]
        }
    },
    editors: {
        queues: {
            graphs: [
                {
                    url: 'reports/editors/queues.php',
                    options: {
                        chart: {
                            renderTo: 'editors-queues',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Editor Review Queues' },
                        subtitle: { text: 'by type' },
                        yAxis: {
                            title: { text: 'Size of Queue' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) + ' ' + this.series.name.toLowerCase() + '</b>';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        }
    },
    services: {
        api: {
            graphs: [
                {
                    url: 'reports/services/api.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'services-api-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'API Method Usage' },
                        subtitle: { text: 'yesterday' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' API requests (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/services/api.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'services-api-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'API Usage per Day' },
                        subtitle: { text: 'by method called' },
                        yAxis: {
                            title: { text: 'API requests' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' requests</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        },
        discovery: {
            graphs: [
                {
                    url: 'reports/services/discovery.php?graph=current',
                    options: {
                        chart: {
                            renderTo: 'services-discovery-current',
                            defaultSeriesType: 'pie',
                            marginRight: 80
                        },
                        title: { text: 'Discovery Pane Interaction' },
                        subtitle: { text: 'yesterday' },
                        tooltip: {
                            formatter: function() {
                            	return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0) +' views (' + Highcharts.numberFormat(this.percentage, 2) + '%)';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                },
                {
                    url: 'reports/services/discovery.php?graph=history',
                    options: {
                        chart: {
                            renderTo: 'services-discovery-history',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Discovery Pane Interaction per Day' },
                        subtitle: { text: 'by interaction' },
                        yAxis: {
                            title: { text: 'Interactions' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' interactions</b> (' + this.series.name + ')';
                            }
                        },
                        series: []
                    },
                    specificSeries: {
                    }
                }
            ]
        }
    },
    users: {
        creation: {
            graphs: [
                {
                    url: 'reports/users/creation.php',
                    options: {
                        chart: {
                            renderTo: 'user-creation',
                            defaultSeriesType: 'area'
                        },
                        title: { text: 'Users Created per Day' },
                        yAxis: {
                            title: { text: 'Users Created' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' users</b> (' + this.series.name + ')';
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