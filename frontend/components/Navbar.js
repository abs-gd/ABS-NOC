"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { logout } from "../utils/api";

export default function Navbar() {
  const pathname = usePathname(); // Get current page

  return (
    <header className="bg-gray-800 text-white p-4 flex justify-between">
      <h1 className="text-xl font-bold">ABS NOC</h1>
      <nav className="flex gap-4">
        <Link href="/" className={pathname === "/" ? "font-bold" : ""}>
          Home
        </Link>
        <Link href="/monitoring" className={pathname === "/monitoring" ? "font-bold" : ""}>
          Monitoring
        </Link>
        <Link href="/servers" className={pathname === "/servers" ? "font-bold" : ""}>
          Servers
        </Link>
      </nav>
      <button onClick={logout} className="bg-red-500 px-4 py-2 rounded">
        Logout
      </button>
    </header>
  );
}