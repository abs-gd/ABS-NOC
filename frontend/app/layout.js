"use client";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import { logout } from "../utils/api";
import Link from "next/link";
import { usePathname } from "next/navigation";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

/*export*/ const metadata = {
  title: "ABS NOC",
  description: "The ABS Network Operation Center.",
};

export default function RootLayout({ children }) {
  const pathname = usePathname(); // Get current page

  return (
    <html lang="en">
      <body
        className={`${geistSans.variable} ${geistMono.variable} antialiased`}
      >
        <div className="flex flex-col min-h-screen">
          <header className="bg-gray-800 text-white p-4 flex justify-between">
            <h1 className="text-xl font-bold">ABS NOC</h1>
            <nav className="flex gap-4">
              <Link href="/" className={pathname === "/" ? "font-bold" : ""}>
                Home
              </Link>
              <Link href="/monitoring" className={pathname === "/monitoring" ? "font-bold" : ""}>
                Monitoring
              </Link>
            </nav>
            <button onClick={logout} className="bg-red-500 px-4 py-2 rounded">
              Logout
            </button>
          </header>
          <main className="flex-grow p-4">{children}</main>
        </div>
      </body>
    </html>
  );
}