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
                        title: { text: 'Discovery Pane Views' },
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
                        title: { text: 'Discovery Pane Views per Day' },
                        subtitle: { text: 'by page' },
                        yAxis: {
                            title: { text: 'Views' }
                        },
                        tooltip: {
                            formatter: function() {
                            	return ''+
                            		Highcharts.dateFormat('%a, %b %e, %Y', this.x) + ': <b>'+
                            		Highcharts.numberFormat(this.y, 0) +' views</b> (' + this.series.name + ')';
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