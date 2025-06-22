import React, { useEffect, useRef } from 'react';
import Chart from 'chart.js/auto';

const ChartSample = ({ initialData }) => {
    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    useEffect(() => {
        if (chartRef.current) {
            // Destroy existing chart if it exists
            if (chartInstance.current) {
                chartInstance.current.destroy();
            }

            // Create new chart
            const ctx = chartRef.current.getContext('2d');
            chartInstance.current = new Chart(ctx, {
                type: 'line',
                data: initialData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Set up WebSocket connection
        const ws = new WebSocket(`ws://localhost:${window.reverbPort}`);

        ws.onopen = () => {
            console.log('Connected to WebSocket');
            ws.send(JSON.stringify({
                type: 'subscribe',
                channel: 'chart-updates',
                key: window.reverbKey
            }));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.type === 'chart-update' && chartInstance.current) {
                chartInstance.current.data = data.data;
                chartInstance.current.update();
            }
        };

        return () => {
            if (chartInstance.current) {
                chartInstance.current.destroy();
            }
            ws.close();
        };
    }, [initialData]);

    return (
        <div style={{ height: '400px' }}>
            <canvas ref={chartRef}></canvas>
        </div>
    );
};

export default ChartSample;
