import psutil
import requests
import time
import os
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()
API_URL = os.getenv("API_URL")
API_KEY = os.getenv("API_KEY")
SERVER_ID = int(os.getenv("SERVER_ID"))

def get_system_stats():
    """Collect system statistics."""
    return {
        "server_id": SERVER_ID,
        "cpu_usage": psutil.cpu_percent(interval=1),
        "ram_usage": psutil.virtual_memory().percent,
        "disk_usage": psutil.disk_usage('/').percent,
        "network_usage": psutil.net_io_counters().bytes_sent + psutil.net_io_counters().bytes_recv
    }

def send_data():
    """Send collected stats to the API."""
    headers = {"Content-Type": "application/json", "X-API-KEY": API_KEY}
    data = get_system_stats()

    try:
        response = requests.post(API_URL, json=data, headers=headers, timeout=10)
        if response.status_code == 200:
            print(f"[SUCCESS] Data sent: {data}")
        else:
            print(f"[ERROR] API response: {response.status_code}, {response.text}")
    except requests.RequestException as e:
        print(f"[ERROR] Failed to send data: {e}")

if __name__ == "__main__":
    print("Starting server monitoring agent...")
    while True:
        send_data()
        time.sleep(60)  # Send data every 60 seconds