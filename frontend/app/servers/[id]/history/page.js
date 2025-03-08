"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { getHistoricalStats } from "../../../../utils/api";
import { LineChart, Line, XAxis, YAxis, Tooltip, CartesianGrid, ResponsiveContainer, Legend } from "recharts";
import { saveAs } from "file-saver";

export default function ServerHistory() {
  const { id } = useParams();
  const [history, setHistory] = useState([]);
  const [filteredHistory, setFilteredHistory] = useState([]);
  const [dateRange, setDateRange] = useState("1d");

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      router.push("/login");
      return;
    }

    getHistoricalStats(id, token).then((data) => {
      if (Array.isArray(data)) {
        const formattedData = data.map((entry) => ({
          ...entry,
          recorded_at: new Date(entry.recorded_at).toLocaleString("en-GB", {
            day: "2-digit",
            month: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
          }),
        }));

        setHistory(formattedData);
        filterData(formattedData, "1d");
      } else {
        console.error("[ERROR] Expected array but got:", data);
        setHistory([]);
      }
    });
  }, [id]);

  // Function to filter data by selected date range
  const filterData = (data, range) => {
    const now = new Date();
    let cutoff = new Date();

    switch (range) {
      case "1d":
        cutoff.setDate(now.getDate() - 1);
        break;
      case "7d":
        cutoff.setDate(now.getDate() - 7);
        break;
      case "30d":
        cutoff.setDate(now.getDate() - 30);
        break;
      default:
        cutoff = new Date(0); // Show all data
    }

    const filtered = data.filter((entry) => new Date(entry.recorded_at) >= cutoff);
    setFilteredHistory(filtered);
  };

  // CSV Export Function
  const exportToCSV = () => {
    if (!filteredHistory.length) return;

    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Time,CPU Usage,RAM Usage,Disk Usage\n";

    filteredHistory.forEach((entry) => {
      csvContent += `${entry.recorded_at},${entry.cpu_usage},${entry.ram_usage},${entry.disk_usage}\n`;
    });

    const blob = new Blob([csvContent], { type: "text/csv" });
    saveAs(blob, `server_${id}_history.csv`);
  };

  return (
 <div className="p-6 bg-gray-100 rounded-lg shadow-lg">
      <h1 className="text-3xl font-bold mb-6 text-center">Server {id} - Historical Stats</h1>

      {/* Date Range Filter & Export Button */}
      <div className="flex justify-between mb-4">
        <div>
          <label className="mr-2 font-semibold text-lg">Filter by:</label>
          <select
            value={dateRange}
            onChange={(e) => {
              setDateRange(e.target.value);
              filterData(history, e.target.value);
            }}
            className="border p-2 rounded bg-white text-gray-800 shadow-sm"
          >
            <option value="1d">Last 24 hours</option>
            <option value="7d">Last 7 days</option>
            <option value="30d">Last 30 days</option>
            <option value="all">All time</option>
          </select>
        </div>
        <button onClick={exportToCSV} className="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
          📥 Export CSV
        </button>
      </div>

      {/* Chart */}
      <ResponsiveContainer width="100%" height={700}>
        <LineChart data={history} margin={{ top: 20, right: 30, left: 20, bottom: 10 }}>
          <XAxis dataKey="recorded_at" tick={{ fill: "#555" }} />
          <YAxis tick={{ fill: "#555" }} />
          <Tooltip
            contentStyle={{ backgroundColor: "rgba(255, 255, 255, 0.9)", borderRadius: "8px" }}
            cursor={{ stroke: "gray", strokeWidth: 1 }}
          />
          <Legend verticalAlign="top" />
          <CartesianGrid strokeDasharray="3 3" stroke="#ddd" />
          <Line type="monotone" dataKey="cpu_usage" stroke="#ff4d4d" strokeWidth={3} dot={{ r: 1 }} name="CPU Usage" activeDot={{ r: 8 }} />
          <Line type="monotone" dataKey="ram_usage" stroke="#4d79ff" strokeWidth={3} dot={{ r: 1 }} name="RAM Usage" activeDot={{ r: 8 }} />
          <Line type="monotone" dataKey="disk_usage" stroke="#4db84d" strokeWidth={3} dot={{ r: 1 }} name="Disk Usage" activeDot={{ r: 8 }} />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}