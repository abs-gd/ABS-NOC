"use client";

import axios from "axios";

const API_URL = process.env.NEXT_PUBLIC_API_URL;
const HISTORY_URL = process.env.NEXT_PUBLIC_HISTORY_URL;

export const getServers = async (token) => {
  return axios.get(`${API_URL}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
};

export const getServer = async (id, token) => {
  return axios.get(`${API_URL}?id=${id}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
};

export const sendMetrics = async (id, token, data) => {
  return axios.post(`${API_URL}?id=${id}`, data, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });
};

export const getHistoricalStats = async (serverId, token) => {
  return fetch(`${HISTORY_URL}?id=${serverId}`, {
    headers: { Authorization: `Bearer ${token}` },
  }).then((res) => res.json());
};

export const logout = () => {
  localStorage.removeItem("token");  // Remove JWT token
  window.location.href = "/login";   // Redirect to login page
};