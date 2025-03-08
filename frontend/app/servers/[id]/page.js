"use client";

import { useEffect, useState } from "react";
import { useRouter, useParams } from "next/navigation";
import { getServer } from "../../../utils/api";
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid,
  ResponsiveContainer,
} from "recharts";

export default function ServerDetails() {
  const { id } = useParams();
  const [server, setServer] = useState(null);
  const router = useRouter();

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      router.push("/login");
      return;
    }

    getServer(id, token)
      .then((res) => setServer(res.data))
      .catch(() => router.push("/"));
  }, [id]);

  if (!server) return <p>Loading...</p>;

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold">
        {server.name} - {server.ip_address}
      </h1>
      <p>Status: {server.status}</p>

      <h3 className="font-bold mt-4">Performance</h3>
      <ResponsiveContainer width="100%" height={200}>
        <LineChart
          data={[
            { name: "CPU", value: server.cpu_usage },
            { name: "RAM", value: server.ram_usage },
            { name: "Disk", value: server.disk_usage },
            { name: "Network", value: server.network_usage },
          ]}
        >
          <XAxis dataKey="name" />
          <YAxis />
          <Tooltip />
          <CartesianGrid stroke="#ccc" />
          <Line type="monotone" dataKey="value" stroke="#8884d8" />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}
