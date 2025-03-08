"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { getServers } from "../utils/api";
import ServerCard from "../components/ServerCard";

export default function Dashboard() {
  const [servers, setServers] = useState([]);
  const router = useRouter();

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      router.push("/login");
      return;
    }

    getServers(token)
      .then((res) => setServers(res.data))
      .catch(() => router.push("/login"));
  }, []);

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold mb-4">Server Monitoring</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {servers.map((server) => (
          <ServerCard key={server.id} server={server} />
        ))}
      </div>
    </div>
  );
}
