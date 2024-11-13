<?php

namespace App\Concerns;

use Filament\Support\RawJs;

trait FormatMoneyApexChart
{
    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
            {
                xaxis: {
                    floating: false,
                    labels: {
                        rotate: -10,
                        rotateAlways: true,
                        formatter: function (value) {
                            if (!isNaN(value)) {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(value)
                            }
                            
                            return value;
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            if (!isNaN(value)) {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(value)
                            }
                            
                            return value;
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(value, { seriesIndex, dataPointIndex, w }) {
                        if (w.config.labels[seriesIndex]) {
                            return value.toFixed(2)+"%";
                        }
                    },
                },
            }
        JS);
    }
}
