"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { getHistoricalStats } from "../../../../utils/api";
import { LineChart, Line, XAxis, YAxis, Tooltip, CartesianGrid, ResponsiveContainer } from "recharts";

export default function ServerHistory() {
  const { id } = useParams();
  const [history, setHistory] = useState([]);

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      router.push("/login");
      return;
    }

    getHistoricalStats(id, token).then((data) => setHistory(data));
  }, [id]);

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold">Server {id} - Historical Stats</h1>
      <ResponsiveContainer width="100%" height={300}>
        <LineChart data={history}>
          <XAxis dataKey="recorded_at" />
          <YAxis />
          <Tooltip />
          <CartesianGrid stroke="#ccc" />
          <Line type="monotone" dataKey="cpu_usage" stroke="red" />
          <Line type="monotone" dataKey="ram_usage" stroke="blue" />
          <Line type="monotone" dataKey="disk_usage" stroke="green" />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}