import requests
import psutil
import time
import json
import os

SERVER_ID = 1
INTERVAL = 60

API_URL = "http://noc.abs.test/api/servers.php?id=" + str(SERVER_ID)
AUTH_URL = "http://noc.abs.test/api/auth.php?register_agent=1"
TOKEN_FILE = "agent_token.txt"

def get_agent_token():
    """Fetch or generate the agent token"""
    if os.path.exists(TOKEN_FILE):
        with open(TOKEN_FILE, "r") as f:
            return f.read().strip()

    # Request a new token from the server
    response = requests.post(AUTH_URL, json={"server_id": SERVER_ID})
    #print("RES:", response.status_code, response.text)
    if response.status_code == 200:
        agent_token = response.json().get("agent_token")
        with open(TOKEN_FILE, "w") as f:
            f.write(agent_token)
        return agent_token
    else:
        print(f"[ERROR] Failed to register agent: {response.text}")
        exit(1)

def get_system_metrics():
    """Collect system performance metrics"""
    return {
        "cpu_usage": psutil.cpu_percent(interval=1),
        "ram_usage": psutil.virtual_memory().percent,
        "disk_usage": psutil.disk_usage('/').percent,
        "network_usage": psutil.net_io_counters().bytes_sent / 1024
    }

def send_data():
    """Send system metrics to the API"""
    agent_token = get_agent_token()
    headers = {"Authorization": f"Bearer {agent_token}", "Content-Type": "application/json"}

    while True:
        metrics = get_system_metrics()
        response = requests.post(API_URL, json=metrics, headers=headers)

        if response.status_code == 200:
            print(f"[INFO] Data sent successfully: {json.dumps(metrics)}")
        else:
            print(f"[ERROR] Failed to send data: {response.text}")

        time.sleep(INTERVAL)

if __name__ == "__main__":
    send_data()