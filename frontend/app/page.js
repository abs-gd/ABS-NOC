"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { getServers } from "../utils/api";
import ServerCard from "../components/ServerCard";

export default function Dashboard() {
  /*const [servers, setServers] = useState([]);
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
  }, []);*/

  return (
    <h2 className="text-2xl font-bold mb-4">Welcome!</h2>
  );
}
