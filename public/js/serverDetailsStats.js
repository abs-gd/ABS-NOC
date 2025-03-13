function fetchLatestStats() {
    fetch(`/api/server/${serverId}/stats`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("cpu").textContent = data.cpu_usage;
            document.getElementById("ram").textContent = data.ram_usage;
            document.getElementById("disk").textContent = data.disk_usage;
            document.getElementById("network").textContent = data.network_usage;
        });
}

// Load stats on page load
fetchLatestStats();
// Refresh every 30 sec
setInterval(fetchLatestStats, 30000);
