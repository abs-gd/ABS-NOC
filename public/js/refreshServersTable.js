/* Refresh table every 30sec */
function refreshServersTable() {
    fetch('/api/servers')
        .then(response => response.json())
        .then(servers => {
            let tableBody = document.getElementById('servers-body');
            tableBody.innerHTML = '';

            servers.forEach(server => {
                let row = `
                    <tr>
                        <td>${server.name}</td>
                        <td>${server.ip_address}</td>
                        <td>${server.created_at}</td>
                        <td class="usage" data-value="${ server.cpu_usage }">${server.cpu_usage}</td>
                        <td class="usage" data-value="${ server.ram_usage }">${server.ram_usage}</td>
                        <td class="usage" data-value="${ server.disk_usage }">${server.disk_usage}</td>
                        <td>${server.network_usage}</td>
                        <td>${server.last_updated}</td>
                        <td>
                            <form method="POST" action="/servers/delete" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="{{ csrf_token }}">
                                <input type="hidden" name="id" value="{{ server.id }}">
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                            <a href="/servers/${server.id}">Details</a>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });

            // Apply color coding after table updates
            applyColorCoding();
        })
        .catch(error => console.error("Error loading server data:", error));
}

/* Refresh every 30 seconds */
setInterval(refreshServersTable, 30000);