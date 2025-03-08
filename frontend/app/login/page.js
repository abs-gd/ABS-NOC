"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import axios from "axios";

/*const API_URL = process.env.NEXT_PUBLIC_API_URL;*/


export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const router = useRouter();

  const handleLogin = async (e) => {
    e.preventDefault();
    try {
      const res = await axios.post(
        /*"http://noc.abs.test/api/auth.php",*/
        process.env.NEXT_PUBLIC_LOGIN_URL,
        {
          email,
          password,
        },
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        }
      );
      /*console.log("API Response:", res.data);*/
      localStorage.setItem("token", res.data.token);
      router.push("/");
    } catch (err) {
      /*console.error("Login error:", err.response?.data || err.message);*/
      setError("Invalid login credentials");
    }
  };

  return (
    <div className="flex h-screen items-center justify-center">
      <form
        onSubmit={handleLogin}
        className="p-6 bg-white shadow-md rounded-lg"
      >
        <h2 className="text-xl font-bold">Login</h2>
        {error && <p className="text-red-500">{error}</p>}
        <input
          type="email"
          placeholder="Email"
          className="border p-2 w-full mt-2"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />
        <input
          type="password"
          placeholder="Password"
          className="border p-2 w-full mt-2"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
        />
        <button
          type="submit"
          className="bg-blue-500 text-white p-2 mt-3 w-full"
        >
          Login
        </button>
      </form>
    </div>
  );
}
