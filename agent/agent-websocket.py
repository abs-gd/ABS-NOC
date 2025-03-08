import requests
import psutil
import json
import websocket
import os
import time


SERVER_ID = 1
INTERVAL = 5
API_URL = "http://noc.abs.test/api/servers.php?id=" + str(SERVER_ID)
AUTH_URL = "http://noc.abs.test/api/auth.php?register_agent=1"
TOKEN_FILE = "agent_token.txt"
WS_URL = "ws://localhost:8080"  # PHP WebSocket Server
#TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXJ2ZXJfaWQiOjEsImlhdCI6MTc0MTQzODAyOX0.7KLm1CN-rfxf5gTdBMAK2s9ou5xFE2DYgkCJX2pMfU8"
#INTERVAL = 5  # Send data every 5 seconds

def get_agent_token():
    """Fetch or retrieve a permanent token."""
    if os.path.exists(TOKEN_FILE):
        with open(TOKEN_FILE, "r") as f:
            return f.read().strip()

    response = requests.post(AUTH_URL, json={"server_id": SERVER_ID})
    if response.status_code == 200:
        data = response.json()
        agent_token = data.get("agent_token")
        if not agent_token:
            print("[ERROR] No agent_token received!")
            exit(1)
        with open(TOKEN_FILE, "w") as f:
            f.write(agent_token)
        return agent_token
    else:
        print(f"[ERROR] Failed to register agent: {response.text}")
        exit(1)

def get_system_metrics():
    """Collect system performance metrics."""
    return {
        "server_id": SERVER_ID,
        "cpu_usage": psutil.cpu_percent(interval=1),
        "ram_usage": psutil.virtual_memory().percent,
        "disk_usage": psutil.disk_usage('/').percent,
        "network_usage": psutil.net_io_counters().bytes_sent / 1024,
    }

def send_data():
    """Send system metrics to the API and WebSocket."""
    agent_token = get_agent_token()
    headers = {"Authorization": f"Bearer {agent_token}", "Content-Type": "application/json"}
    ws = websocket.WebSocket()
    
    try:
        ws.connect(WS_URL)
    except Exception as e:
        print(f"[ERROR] WebSocket connection failed: {e}")
        exit(1)

    while True:
        metrics = get_system_metrics()

        # ✅ Send to PHP API
        try:
            response = requests.post(API_URL, json=metrics, headers=headers)
            if response.status_code == 200:
                print(f"[API] Data sent: {metrics}")
            elif response.status_code == 401:  # Unauthorized -> Request new token
                print("[ERROR] Token expired or invalid. Requesting new token...")
                os.remove(TOKEN_FILE)
                agent_token = get_agent_token()
                headers["Authorization"] = f"Bearer {agent_token}"
        except requests.exceptions.RequestException as e:
            print(f"[ERROR] API Request failed: {e}")

        # ✅ Send to WebSocket
        try:
            ws.send(json.dumps(metrics))
            print(f"[WS] Sent WebSocket update: {metrics}")
        except Exception as e:
            print(f"[ERROR] WebSocket error: {e}")
            ws.connect(WS_URL)  # Reconnect on failure

        time.sleep(INTERVAL)

if __name__ == "__main__":
    send_data()