"use client";

import { useEffect, useState } from "react";

export default function Monitoring() {
  const [servers, setServers] = useState({});

  useEffect(() => {
    const ws = new WebSocket("ws://localhost:8080");

    ws.onopen = () => console.log("WebSocket connected");
    
    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      console.log("Received WebSocket update:", data);

      setServers((prevServers) => ({
        ...prevServers,
        [data.server_id]: data,
      }));
    };

    ws.onclose = () => console.log("WebSocket disconnected");

    return () => ws.close();
  }, []);

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold mb-4">Live Server Monitoring</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {Object.values(servers).map((server) => (
          <div key={server.server_id} className="p-4 bg-white shadow-lg rounded">
            <h2 className="text-lg font-bold">Server {server.server_id}</h2>
            <p>CPU Usage: {server.cpu_usage}%</p>
            <p>RAM Usage: {server.ram_usage}%</p>
            <p>Disk Usage: {server.disk_usage}%</p>
            <p>Network Usage: {server.network_usage} KB</p>
          </div>
        ))}
      </div>
    </div>
  );
}