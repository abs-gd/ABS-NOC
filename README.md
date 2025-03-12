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
`nano /etc/systemd/system/server-agent.service`
```
[Unit]
Description=Server Monitoring Agent
After=network.target

[Service]
ExecStart=/usr/bin/python3 /path/to/agent.py
Restart=always
User=root

[Install]
WantedBy=multi-user.target
```

`systemctl daemon-reload`  
`systemctl enable server-agent`  
`systemctl start server-agent`  
`systemctl status server-agent`  

## ON WINDOWS
- Open Task Scheduler
- Create a New Task
- Set Trigger → "At startup" or "Every 5 minutes"
- Set Action → "Start a program"
  - Program: python
  - Arguments: "C:\path\to\agent.py"
- Save & Start the Task
