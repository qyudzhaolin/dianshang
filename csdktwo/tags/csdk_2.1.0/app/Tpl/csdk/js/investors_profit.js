$(function () {
    //   图 1
                                              //显示载入动画
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            backgroundColor: '#fafafa'
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        xAxis: {
            showEmpty:null,
            categories: ['','','',''],
            tickPositions:null,
            tickWidth:0
        },
        yAxis: {
            min: 0,
            title: {
                text: null
            },
            labels: {
                formatter: function() {
                    return Highcharts.numberFormat(this.value,0,"",",")+'万';
                }
            },

            valueSuffix: ' 万',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:0f} 万</b></td></tr>',
            //提醒框的头部，文字
            headerFormat: '<small>{point.key}<br/></small><table>',
            style: {
                padding: 10,
                fontWeight: 'bold',
                fontSize:'12px',
                color:'#666666'
            }
        },
        tooltip: {
            shared: true,
            useHTML: true,
            borderRadius: 10,
            headerFormat: '<small></small><table>',
            pointFormat: '<tr><td style="color: {series.color}"></td>' +
            '<td style="text-align: right"><b>{point.y} 万</b></td></tr>',
            footerFormat: '</table>',
            animation: true,
            valueDecimals: 2
        },
        //
        legend: {
            enabled: false
        },
        //右下角 的 Highcharts.com 没有了
        credits: {
            enabled: false
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                colorByPoint :true
            },
            pie: {

                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    formatter:function() {
                        return '<b>' + this.point.name + '</b>:' + this.point.percentage.toFixed(2) + "%";
                    },
                    onnectorWidth:0,
                    connectorPadding:0,
                    distance:-30
                },
                showInLegend: false
            }

        },
        series: [{
            type: 'column',
            name: ['aa'],
            data: [49.9,500,60,70]

        }]
    });

    //   图 2
    $('#container2').highcharts({

        chart: {
            type: 'line',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            backgroundColor: '#fafafa'
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        xAxis: {
            showEmpty:null,
            categories: ['','','',''],
            tickPositions:null,
            tickWidth:0
        },
        yAxis: {
            min: 0,
            title: {
                text: null
            },
            labels: {
                formatter: function() {
                    return Highcharts.numberFormat(this.value,0,"",",")+'万';
                },
            },

            valueSuffix: ' 万',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:0f} 万</b></td></tr>',
            //提醒框的头部，文字
            headerFormat: '<small>{point.key}<br/></small><table>',
            style: {
                padding: 10,
                fontWeight: 'bold',
                fontSize:'12px',
                color:'#666666'
            }
        },
        tooltip: {
            shared: true,
            useHTML: true,
            borderRadius: 10,
            headerFormat: '<small></small><table>',
            pointFormat: '<tr><td style="color: {series.color}"></td>' +
            '<td style="text-align: right"><b>{point.y} 万</b></td></tr>',
            footerFormat: '</table>',
            animation: true,
            valueDecimals: 2
        },
        legend: {
            enabled: false
        },

//右下角 的 Highcharts.com 没有了
        credits: {
            enabled: false
        },
    plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                colorByPoint :true
            },
            pie: {

                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    formatter:function() {
                        return '<b>' + this.point.name + '</b>:' + this.point.percentage.toFixed(2) + "%";
                    },
                    onnectorWidth:0,
                    connectorPadding:0,
                    distance:-30
                },
                showInLegend: false
            }

        },
        series: [{
            type: 'column',
            name: ['aa'],
            data: [4999.9,500,6000,7000]

        }]
    });
    //   图 3
    $('#container3').highcharts({

        chart: {
            type: 'line',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            backgroundColor: '#fafafa'
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        xAxis: {
            showEmpty:null,
            categories: ['','','',''],
            tickPositions:null,
            tickWidth:0
        },
        yAxis: {
            min: 0,
            title: {
                text: null
            },
            labels: {
                formatter: function() {
                    return Highcharts.numberFormat(this.value,0,"",",")+'万';
                },
            },

            valueSuffix: ' 万',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:0f} 万</b></td></tr>',
            //提醒框的头部，文字
            headerFormat: '<small>{point.key}<br/></small><table>',
            style: {
                padding: 10,
                fontWeight: 'bold',
                fontSize:'12px',
                color:'#666666'
            }
        },
        tooltip: {
            shared: true,
            useHTML: true,
            borderRadius: 10,
            headerFormat: '<small></small><table>',
            pointFormat: '<tr><td style="color: {series.color}"></td>' +
            '<td style="text-align: right"><b>{point.y} 万</b></td></tr>',
            footerFormat: '</table>',
            animation: true,
            valueDecimals: 2
        },
        legend: {
            enabled: false
        },
//右下角 的 Highcharts.com 没有了
        credits: {
            enabled: false
        },

        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                colorByPoint :true
            },
            pie: {

                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    formatter:function() {
                        return '<b>' + this.point.name + '</b>:' + this.point.percentage.toFixed(2) + "%";
                    },
                    onnectorWidth:0,
                    connectorPadding:0,
                    distance:-30
                },
                showInLegend: false
            }

        },
        series: [{
            type: 'column',
            name: ['aa'],
            data: [4999.9,500,6000,7000]

        }]
    });
});