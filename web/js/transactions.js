/**
 * Retrieves all transactions from the API in a JSON. It processes it and creates
 * a highcharts StockChart.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 */

$(function () {
    var createChart = function () {
        $('#container').highcharts('StockChart', {
            rangeSelector: {
                selected: 4
            },
            yAxis: {
                labels: {
                    formatter: function () {
                        return (this.value > 0 ? ' + ' : '') + this.value + '%';
                    }
                },
                plotLines: [{
                    value: 0,
                    width: 2,
                    color: 'silver'
                }]
            },
            plotOptions: {
                series: {
                    compare: 'percent'
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
                valueDecimals: 2
            },
            series: seriesAmmountBuy
        });
    };

    var seriesAmmountBuy = Array(),
        mapAmmountBuy = {};

    // gets JSON from /mtp/transactions that contains all the transactions from db.
    $.getJSON('/mtp/transactions', function (transactions) {

        // generates mapAmmountBuy with transactions info with the format highcharts needs.
        // It can be reused to generate more maps for more graphs.
        transactions.forEach(function(transaction) {
            var currency = transaction.currencyTo,
                timestamp = new Date(transaction.timePlaced).getTime(),
                amount = parseInt(transaction.amountBuy);

            if (mapAmmountBuy[currency] == undefined) {
                mapAmmountBuy[currency] = {name: currency, data: []};
            }
            mapAmmountBuy[currency]["data"].push(Array(timestamp, amount));
        });

        // highcharts needs an array of series, not a map.
        var i = 0;
        for (var key in mapAmmountBuy) {
            seriesAmmountBuy.push(mapAmmountBuy[key]);
            i++;
        }

        createChart();
    });
});
