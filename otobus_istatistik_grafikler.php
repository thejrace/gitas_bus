<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Filo Plan ',
		'action_id' => Actions::FILO_PLAN_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: ". MAIN_URL );
	}

	$JQUERYUI = true;
	require 'inc/header.php';



?>	

	
	
	<div class="section-header">
		GRAF
	</div>


	<div class="section-content">
		<div id="graf">


		
		</div>

		<div id="pie" style="margin-top:40px;"></div>

		<div id="gauge" style="width:300px; margin-top:40px" ></div>
		
		
	</div>
	
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/highcharts-more.js"></script>
	<script src="https://code.highcharts.com/highcharts-3d.js"></script>
	<script type="text/javascript">

	

		AHReady(function(){

			Highcharts.theme = {
			   colors: ['#f45b5b', '#8085e9', '#8d4654', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
			      '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
			   chart: {
			      backgroundColor: '#fff',
			      style: {
			         fontFamily: 'Open Sans'
			      }
			   },
			   title: {
			      style: {
			         color: 'black',
			         fontSize: '16px',
			         fontWeight: 'bold'
			      }
			   },
			   subtitle: {
			      style: {
			         color: 'black'
			      }
			   },
			   tooltip: {
			      borderWidth: 0
			   },
			   legend: {
			      itemStyle: {
			         fontWeight: 'bold',
			         fontSize: '13px'
			      }
			   },
			   xAxis: {
			      labels: {
			         style: {
			            color: '#6e6e70'
			         }
			      }
			   },
			   yAxis: {
			      labels: {
			         style: {
			            color: '#6e6e70'
			         }
			      }
			   },
			   plotOptions: {
			      series: {
			         shadow: true
			      },
			      candlestick: {
			         lineColor: '#404048'
			      },
			      map: {
			         shadow: false
			      }
			   },

			   // Highstock specific
			   navigator: {
			      xAxis: {
			         gridLineColor: '#D0D0D8'
			      }
			   },
			   rangeSelector: {
			      buttonTheme: {
			         fill: 'white',
			         stroke: '#C0C0C8',
			         'stroke-width': 1,
			         states: {
			            select: {
			               fill: '#D0D0D8'
			            }
			         }
			      }
			   },
			   scrollbar: {
			      trackBorderColor: '#C0C0C8'
			   },

			   // General
			   background2: '#E0E0E8'

			};


			// Apply the theme
			Highcharts.setOptions(Highcharts.theme);

			Highcharts.chart('graf', {
		        title: {
		            text: 'Son 6 Ayın İptal Seferler Grafiği',
		            x: 0 //center
		        },
		        xAxis: {
		            categories: ['Ekim', 'Kasım', 'Aralık', 'Ocak']
		        },
		        yAxis: {
		            title: {
		                text: 'Sefer Sayısı'
		            },
		            plotLines: [{
		                value: 0,
		                width: 1,
		                color: '#808080'
		            }]
		        },
		        tooltip: {
		            valueSuffix: 'Sefer'
		        },
		        legend: {
		            layout: 'vertical',
		            align: 'right',
		            verticalAlign: 'middle',
		            borderWidth: 0
		        },
		        plotOptions: {
		            line: {
		                dataLabels: {
		                    enabled: true
		                },
		                enableMouseTracking: false
		            }
		        },
		        series: [{
		            name: 'İptal Seferler',
		            data: [15, 17, 19, 15]
		        }]
		    });

		    Highcharts.chart('gauge', {

		        chart: {
		            type: 'gauge',
		            plotBackgroundColor: null,
		            plotBackgroundImage: null,
		            plotBorderWidth: 0,
		            plotShadow: false
		        },

		        title: {
		            text: 'B-1744 KM'
		        },

		        pane: {
		            startAngle: -150,
		            endAngle: 150,
		            background: [{
		                backgroundColor: {
		                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
		                    stops: [
		                        [0, '#FFF'],
		                        [1, '#333']
		                    ]
		                },
		                borderWidth: 0,
		                outerRadius: '109%'
		            }, {
		                backgroundColor: {
		                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
		                    stops: [
		                        [0, '#333'],
		                        [1, '#FFF']
		                    ]
		                },
		                borderWidth: 1,
		                outerRadius: '107%'
		            }, {
		                // default background
		            }, {
		                backgroundColor: '#DDD',
		                borderWidth: 0,
		                outerRadius: '105%',
		                innerRadius: '103%'
		            }]
		        },

		        // the value axis
		        yAxis: {
		            min: 0,
		            max: 500000,

		            minorTickInterval: 'auto',
		            minorTickWidth: 1,
		            minorTickLength: 10,
		            minorTickPosition: 'inside',
		            minorTickColor: '#666',

		            tickPixelInterval: 30,
		            tickWidth: 2,
		            tickPosition: 'inside',
		            tickLength: 10,
		            tickColor: '#666',
		            labels: {
		                step: 2,
		                rotation: 'auto'
		            },
		            title: {
		                text: 'km'
		            },
		            plotBands: [{
		                from: 0,
		                to: 100000,
		                color: '#55BF3B' // green
		            }, {
		                from: 100000,
		                to: 400000,
		                color: '#DDDF0D' // yellow
		            }, {
		                from: 400000,
		                to: 500000,
		                color: '#DF5353' // red
		            }]
		        },

		        series: [{
		            name: 'Toplam KM',
		            data: [365487],
		            tooltip: {
		                valueSuffix: ' km'
		            }
		        }]

		    });

		    Highcharts.chart('pie', {
		        chart: {
		            type: 'pie',
		            options3d: {
		                enabled: true,
		                alpha: 45,
		                beta: 0
		            }
		        },
		        title: {
		            text: 'Hatlara Göre Otobüslerin Dağılımı'
		        },
		        tooltip: {
		            pointFormat: '{series.name}: <b>{point.y}</b><br/>',
		        },
		        plotOptions: {
		            pie: {
		                allowPointSelect: true,
		                cursor: 'pointer',
		                depth: 35,
		                dataLabels: {
		                    enabled: true,
		                    format: '{point.name}'
		                }
		            }
		        },
		        series: [{
		            type: 'pie',
		            name: 'Otobüs Sayısı',
		            data: [
		                ['54E', 2],
		                ['11ÜS', 2],
		                ['14A', 3],
		                ['15BK', 1],
		                ['39İ', 2]
		            ]
		        }]
		    });

		    var credits = $AHC('highcharts-credits'); 
		    for( var x = 0; x < credits.length; x++ ) hide(credits[x]);
		    
		
		});

	</script>

<?php
	require 'inc/footer.php';