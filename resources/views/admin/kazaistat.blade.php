<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">{{ $kazaYil }} (Toplam Kaza:{{ $toplamKaza }})</h4>
            </div>
            <div class="card-body">
                <div id="column_stacked2" data-colors='["--vz-warning", "--vz-danger", "--vz-info", "--vz-success"]'
                    class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">{{ $kazaYil2 }} (Toplam Kaza:{{ $toplamKaza2 }})</h4>
            </div>
            <div class="card-body">
                <div id="column_stacked3" data-colors='["--vz-warning", "--vz-danger", "--vz-info", "--vz-success"]'
                    class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">{{ $kazaYil2 }} Oranı (Toplam Kaza:{{ $toplamKaza2 }})</h4>
            </div>
            <div class="card-body">
                <div id="column_stacked4" data-colors='["--vz-primary", "--vz-secondary"]' class="apex-charts"
                    dir="ltr"></div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    function getChartColorsArray(e) {
        if (document.getElementById(e) !== null) {
            let colors = document.getElementById(e).getAttribute("data-colors");
            if (colors) {
                return JSON.parse(colors).map(function(color) {
                    let t = color.trim();
                    if (t.indexOf(",") === -1) {
                        return getComputedStyle(document.documentElement).getPropertyValue(t) || t;
                    } else {
                        color = t.split(",");
                        if (color.length === 2) {
                            return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(color[
                                0]) + "," + color[1] + ")";
                        } else {
                            return t;
                        }
                    }
                });
            }
        }
        return [];
    }

    var chartColumnStackedColors = getChartColorsArray("column_stacked2");
    // PHP'den gelen verileri JS değişkenlerine atıyoruz
    var aylar = @json($kazaGrafik->pluck('ay_ad'));
    var kaza = @json($kazaGrafik->pluck('kaza'));
    var vefat = @json($kazaGrafik->pluck('vefat'));
    var yarali = @json($kazaGrafik->pluck('yarali'));
    var carpisma = @json($kazaGrafik->pluck('carp'));

    if (chartColumnStackedColors.length) {
        var options = {
            series: [{
                    name: "Kaza",
                    data: kaza
                },
                {
                    name: "Vefat",
                    data: vefat
                },
                {
                    name: "Yaralı",
                    data: yarali
                },
                {
                    name: "Çarpışma",
                    data: carpisma
                }
            ],
            chart: {
                type: "bar",
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: "bottom",
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                }
            },
            xaxis: {
                categories: aylar
            },
            legend: {
                position: "right",
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            colors: chartColumnStackedColors
        };

        var chart = new ApexCharts(document.querySelector("#column_stacked2"), options);
        chart.render();
    }
</script>
<script>
    function getChartColorsArray(e) {
        if (document.getElementById(e) !== null) {
            let colors = document.getElementById(e).getAttribute("data-colors");
            if (colors) {
                return JSON.parse(colors).map(function(color) {
                    let t = color.trim();
                    if (t.indexOf(",") === -1) {
                        return getComputedStyle(document.documentElement).getPropertyValue(t) || t;
                    } else {
                        color = t.split(",");
                        if (color.length === 2) {
                            return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(color[
                                0]) + "," + color[1] + ")";
                        } else {
                            return t;
                        }
                    }
                });
            }
        }
        return [];
    }

    var chartColumnStackedColors = getChartColorsArray("column_stacked3");
    // PHP'den gelen verileri JS değişkenlerine atıyoruz
    var aylar = @json($kazaGrafik2->pluck('ay_ad'));
    var kaza = @json($kazaGrafik2->pluck('kaza'));
    var vefat = @json($kazaGrafik2->pluck('vefat'));
    var yarali = @json($kazaGrafik2->pluck('yarali'));
    var carpisma = @json($kazaGrafik2->pluck('carp'));

    if (chartColumnStackedColors.length) {
        var options = {
            series: [{
                    name: "Kaza",
                    data: kaza
                },
                {
                    name: "Vefat",
                    data: vefat
                },
                {
                    name: "Yaralı",
                    data: yarali
                },
                {
                    name: "Çarpışma",
                    data: carpisma
                }
            ],
            chart: {
                type: "bar",
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: "bottom",
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                }
            },
            xaxis: {
                categories: aylar
            },
            legend: {
                position: "right",
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            colors: chartColumnStackedColors
        };

        var chart = new ApexCharts(document.querySelector("#column_stacked3"), options);
        chart.render();
    }
</script>
<script>
    function getChartColorsArray(e) {
        if (document.getElementById(e) !== null) {
            let colors = document.getElementById(e).getAttribute("data-colors");
            if (colors) {
                return JSON.parse(colors).map(function(color) {
                    let t = color.trim();
                    if (t.indexOf(",") === -1) {
                        return getComputedStyle(document.documentElement).getPropertyValue(t) || t;
                    } else {
                        color = t.split(",");
                        if (color.length === 2) {
                            return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(color[
                                0]) + "," + color[1] + ")";
                        } else {
                            return t;
                        }
                    }
                });
            }
        }
        return [];
    }
    var chartColumnStackedColors = getChartColorsArray("column_stacked4");
    // PHP'den gelen verileri JS değişkenlerine atıyoruz
    var yuzde = @json($kazaOran->pluck('yuzde'));
    var kaza = @json($kazaOran->pluck('kaza'));
    var kkno = @json($kazaOran->pluck('kkno'));
    if (chartColumnStackedColors.length) {
        var options = {
            series: [{
                    name: "Kaza",
                    data: kaza
                },
                {
                    name: "Oran %",
                    data: yuzde
                }
            ],
            chart: {
                type: "bar",
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: "bottom",
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                }
            },
            xaxis: {
                type: "month",
                categories: kkno
            },
            legend: {
                position: "right",
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            colors: chartColumnStackedColors
        };
        var chart = new ApexCharts(document.querySelector("#column_stacked4"), options);
        chart.render();
    }
</script>
