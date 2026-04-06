<?php

namespace App\Filament\Widgets;
use Filament\Widgets\ChartWidget;

class MyWidget extends ChartWidget
{
    

    protected ?string $heading = 'My Dashboard';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        return [
            // 'md' => 4,
            // 'xl' => 5,
        'datasets' => [
            [
                'label' => 'Valores',
                'data' => [0,10, 5, 2, 21, 32, 45, 75, 65, 45, 77, 89],
                'backgroundVolor' => '#36A2EB',
                'borderColor' => '#9BD0F5',
            ],
        ],
          'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],  
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
