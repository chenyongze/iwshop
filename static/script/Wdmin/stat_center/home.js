
define(['jquery', 'util', 'highcharts'], function($, util, highcharts) {
    $.get(shoproot + '?/WdminAjax/ajaxLoadStatHome/', function(res) {
        $('#card1-subtab1').html(res);
        ajaxLoadPageViewChart();
        ajaxLoadSaleViewChart();
        LoadWchatChart();
    });
});

/**
 * 页面浏览量数据
 * @returns {undefined}
 */
function ajaxLoadPageViewChart() {
    $.get(shoproot + '?/Wdmin/ajaxGetPageViewData', function(res) {
        res = res.toJson();
        for (var k in res.x) {
            res.x[k] = k + '时';
        }
        $('#daytot').html(res.daytot);
        $('#montot').html(res.montot);
        $('#pageviewchart').highcharts({
            title: {
                text: '微店浏览量', x: 0
            },
            chart: {
                type: 'line'
            },
            xAxis: {
                categories: res.x,
                lineWidth: 0
            },
            yAxis: {
                title: {
                    text: ''
                },
                minPadding: 0
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            series: [{
                    name: '浏览量',
                    data: res.y
                }]
        });
    });
}

/**
 * 销售分析数据
 * @returns {undefined}
 */
function ajaxLoadSaleViewChart() {
    $.get(shoproot + '?/Wdmin/ajaxLoadSaleHisCartData/', function(res) {
        res = res.toJson();
        Loading.finish();
        $('#saletReachart').highcharts({
            title: {
                text: '本月销售趋势', x: 0
            },
            chart: {
                type: 'line'
            },
            xAxis: {
                categories: res.x,
                lineWidth: 0
            },
            yAxis: {
                title: {
                    text: ''
                },
                minPadding: 0
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            series: [{
                    name: '销售额',
                    data: res.y
                }]
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function LoadWchatChart() {
    var count = intArray($('#wechatCount').val().split(','));
    var day = intArray($('#wechatDay').val().split(','));
    // 微信服务号订阅量 
    $('#Wechatchart').highcharts({
        title: {
            text: '服务号订阅量', x: 0
        },
        chart: {
            type: 'line'
        },
        xAxis: {
            categories: day
        },
        yAxis: {
            title: {
                text: ''
            },
            minPadding: 0
        },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        series: [{
                name: '订阅量',
                data: count,
                color: '#555'
            }]
    });
    count = null;
    day = null;
}

function intArray(arr) {
    var index;
    for (index in arr) {
        arr[index] = parseInt(arr[index]);
    }
    index = null;
    return arr;
}