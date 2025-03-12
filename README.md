# ABS Network Operation Center
  - Monitors server resources, puppet and proxmox hypervisors
  - Panel with links to all important management platforms and sites (proxmox, phpmyadmin, hoster panels, google analytics, office365, ... )
  - Buttons and forms for commands I use a lot (pulling in puppet code, adding a new website on one of the webservers, ...)

# Some commands to test the API
## Update metrics on a server
curl -X POST http://noc.abs.test/api/server-stats \
     -H "Content-Type: application/json" \
     -H "X-API-KEY: your-secret-api-key" \
     -d '{
          "server_id": 1,
          "cpu_usage": 30.5,
          "ram_usage": 45.2,
          "disk_usage": 60.1,
          "network_usage": 12.4
     }' 

# How to install the python agent as a service
## ON LINUX
- Make it executable:
`chmod +x agent.py`
- Create systemd service file:
`nano /etc/systemd/system/abs-server-monitor.service`
```
[Unit]
Description=ABS Server Monitoring Agent
After=network.target

[Service]
ExecStart=/usr/bin/python3 /path/to/agent.py
WorkingDirectory=/path/to
Restart=always
User=userYouWantItToRunAs
Environment="PYTHONUNBUFFERED=1"

[Install]
WantedBy=multi-user.target
```
- Reload services:
`systemctl daemon-reload`  
- Enable the service:
`systemctl enable abs-server-monitor`  
- Start the service:
`systemctl start abs-server-monitor`  
- Check if it worked:
`systemctl status abs-server-monitor`  

## ON WINDOWS
- Press Win + R, type taskschd.msc, and press Enter.
- Click "Create Basic Task" in the right panel.
- Name the Task and click next
- Select trigger "when the computer starts" and click next
- Select action "start a program" and click next
     - Program: full path to python3
     - Arguments: full path to agent.py
     - Start in: directory where agent.py is located
- Click next, then finish
- Modify task to auto restart
     - In Task Scheduler, find your task
     - Right-click and select "Properties"
     - Go to "Triggers", click "Edit", and set:
          - Delay task for: 30 seconds (To ensure networking is available at startup)
     - Go to "Settings" and enable:
          - Allow task to be run on demand
          - Restart the task if it fails
          - Stop the task if it runs longer than 1 hour
          - Run task as soon as possible if missed
     - Click OK to save changes.
