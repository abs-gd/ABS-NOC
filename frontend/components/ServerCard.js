import { useRouter } from "next/navigation";
import Link from "next/link";

export default function ServerCard({ server }) {
  return (
    <div className="p-4 bg-white shadow-lg rounded">
      <h2 className="text-lg font-bold">{server.name}</h2>
      <p>IP: {server.ip_address}</p>
      <p>Status: {server.status}</p>
      <Link href={`/servers/${server.id}`} className="text-blue-500 mt-2 block">
        View Details →
      </Link>
    </div>
  );
}
