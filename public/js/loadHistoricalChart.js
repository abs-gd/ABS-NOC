let chartInstance = null;

function fetchHistoricalStats() {
    fetch(`/api/server/${serverId}/history`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                console.error("No historical data available.");
                return;
            }

            const labels = data.map(stat => new Date(stat.recorded_at).toLocaleTimeString());
            const cpuData = data.map(stat => stat.cpu_usage);
            const ramData = data.map(stat => stat.ram_usage);
            const diskData = data.map(stat => stat.disk_usage);

            // Destroy previous chart before creating a new one
            if (chartInstance) {
                chartInstance.destroy();
            }

            const ctx = document.getElementById("statsChart").getContext("2d");
            chartInstance = new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "CPU Usage (%)",
                            data: cpuData,
                            borderColor: "red",
                            backgroundColor: "rgba(255, 0, 0, 0.2)",
                            fill: true,
                            tension: 0.3, // Smooth curves
                        },
                        {
                            label: "RAM Usage (%)",
                            data: ramData,
                            borderColor: "blue",
                            backgroundColor: "rgba(0, 0, 255, 0.2)",
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: "Disk Usage (%)",
                            data: diskData,
                            borderColor: "green",
                            backgroundColor: "rgba(0, 255, 0, 0.2)",
                            fill: true,
                            tension: 0.3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { size: 14 },
                                boxWidth: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${tooltipItem.dataset.label}: ${tooltipItem.raw}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + "%";
                                }
                            }
                        },
                        x: {
                            display: true,
                            title: { display: true, text: "Time" }
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Error loading historical stats:", error));
}
fetchHistoricalStats();