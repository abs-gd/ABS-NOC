"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { getServers, addServer, deleteServer, updateServer, toggleServerStatus } from "../../utils/api";

export default function Servers() {
  const [servers, setServers] = useState([]);
  const [editMode, setEditMode] = useState(null);
  const [name, setName] = useState("");
  const [newName, setNewName] = useState("");
  const [ipAddress, setIpAddress] = useState("");
  const [newIp, setNewIp] = useState("");
  const [message, setMessage] = useState(null);
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

  const handleAddServer = async (e) => {
    e.preventDefault();
    const token = localStorage.getItem("token");
    if (!token) return;

    const response = await addServer(name, ipAddress, token);
    setMessage(response.message || response.error);

    if (response.message) {
      setServers([...servers, { name, ip_address: ipAddress, status: "inactive" }]);
      setName("");
      setIpAddress("");
    }
  };

  const handleDelete = async (serverId) => {
    const token = localStorage.getItem("token");
    const response = await deleteServer(serverId, token);
    if (response.message) {
      setServers(servers.filter((s) => s.id !== serverId));
    }
  };

  const handleEdit = async (serverId) => {
    const token = localStorage.getItem("token");
    const response = await updateServer(serverId, newName, newIp, token);
    if (response.message) {
      setServers(servers.map((s) => (s.id === serverId ? { ...s, name: newName, ip_address: newIp } : s)));
      setEditMode(null);
    }
  };

  const handleToggleStatus = async (serverId, currentStatus) => {
    const token = localStorage.getItem("token");
    const newStatus = currentStatus === "active" ? "inactive" : "active";
    const response = await toggleServerStatus(serverId, newStatus, token);
    if (response.message) {
      setServers(servers.map((s) => (s.id === serverId ? { ...s, status: newStatus } : s)));
    }
  };

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold mb-6">Server Management</h1>

      {/* Server List */}
      <div className="bg-white shadow-md rounded-lg p-4 mb-6">
        <h2 className="text-xl font-bold mb-4">All Servers</h2>
        {servers.length > 0 ? (
          <ul className="divide-y divide-gray-200">
            {servers.map((server, index) => (
              <li key={server.id} className="p-4 flex justify-between items-center">
                {editMode === server.id ? (
                  <>
                    <input className="border p-1 mr-2" value={newName} onChange={(e) => setNewName(e.target.value)} />
                    <input className="border p-1 mr-2" value={newIp} onChange={(e) => setNewIp(e.target.value)} />
                    <button onClick={() => handleEdit(server.id)} className="bg-green-600 text-white px-2 py-1 rounded">✔</button>
                  </>
                ) : (
                  <>
                    <span className="font-semibold">{server.name} - {server.ip_address}</span>
                    <div>
                      <button onClick={() => setEditMode(server.id)} className="mr-2 bg-yellow-500 text-white px-2 py-1 rounded">✏</button>
                      <button onClick={() => handleDelete(server.id)} className="mr-2 bg-red-600 text-white px-2 py-1 rounded">🗑</button>
                      <button onClick={() => handleToggleStatus(server.id, server.status)} className="bg-gray-500 text-white px-2 py-1 rounded">
                        {server.status === "active" ? "Deactivate" : "Activate"}
                      </button>
                    </div>
                  </>
                )}
              </li>
            ))}
          </ul>
        ) : (
          <p className="text-gray-600">No servers found.</p>
        )}
      </div>

      {/* Add Server Form */}
      <div className="bg-white shadow-md rounded-lg p-4">
        <h2 className="text-xl font-bold mb-4">Add New Server</h2>
        {message && <p className="text-sm mb-2 text-gray-700">{message}</p>}
        <form onSubmit={handleAddServer} className="space-y-4">
          <input
            type="text"
            placeholder="Server Name"
            value={name}
            onChange={(e) => setName(e.target.value)}
            className="border p-2 w-full rounded"
            required
          />
          <input
            type="text"
            placeholder="IP Address"
            value={ipAddress}
            onChange={(e) => setIpAddress(e.target.value)}
            className="border p-2 w-full rounded"
            required
          />
          <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
            ➕ Add Server
          </button>
        </form>
      </div>
    </div>
  );
}