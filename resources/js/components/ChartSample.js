import React from 'react';
import { Line } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

export default function ChartSample({ initialData }) {
    const [chartData, setChartData] = React.useState(initialData);

    React.useEffect(() => {
        // Subscribe to the chart-updates channel
        window.Echo.channel('chart-updates')
            .listen('ChartDataUpdated', (e) => {
                setChartData(e.chartData);
            });

        return () => {
            window.Echo.leave('chart-updates');
        };
    }, []);

    const options = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Sales Comparison 2023-2024'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    return (
        <div className="p-6 bg-white rounded-lg shadow-lg">
            <Line data={chartData} options={options} />
        </div>
    );
}
