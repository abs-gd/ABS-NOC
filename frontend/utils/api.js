"use client";

import axios from "axios";

const API_URL = process.env.NEXT_PUBLIC_API_URL;
const ADD_SERVER_URL = process.env.NEXT_PUBLIC_ADD_SERVER_URL;
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

export const addServer = async (name, ipAddress, token) => {
  return fetch(`${ADD_SERVER_URL}`, {
    method: "POST",
    headers: { "Content-Type": "application/json", Authorization: `Bearer ${token}` },
    body: JSON.stringify({ name, ip_address: ipAddress }),
  }).then((res) => res.json());
};

export const deleteServer = async (serverId, token) => {
  return fetch(`${API_URL}`, {
    method: "DELETE",
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${serverId}`,
  }).then((res) => res.json());
};

export const updateServer = async (serverId, name, ipAddress, token) => {
  return fetch(`${API_URL}`, {
    method: "PUT",
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${serverId}&name=${name}&ip_address=${ipAddress}`,
  }).then((res) => res.json());
};

export const toggleServerStatus = async (serverId, status, token) => {
  return fetch(`${API_URL}`, {
    method: "PATCH",
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${serverId}&status=${status}`,
  }).then((res) => res.json());
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