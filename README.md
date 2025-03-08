# ABS Network Operation Center
  - Monitors server resources, puppet and proxmox hypervisors
  - Panel with links to all important management platforms and sites (proxmox, phpmyadmin, hoster panels, google analytics, office365, ... )
  - Buttons and forms for commands I use a lot (pulling in puppet code, adding a new website on one of the webservers, ...)
## Some notices
  - Do NOT make this accessible to the internet, while it has some basic security, I do not see it as secure
  enough for that use case. All my servers are accessible through an internal VPN and I can access the dashboard
  through another VPN.
  - This is mainly a hobby project for my own environment, I do not believe in reinventing the wheel in my
  professional projects and would recommend more mature open source projects for that. It's a great way to learn and to
  build something custom for your own needs though, so feel free to fork it and play around!
## Project structure
### Backend (PHP API with MySQL and JWT auth)
- Handles server registration
- Provides an API for data collection
- Stores historical server stats in MySQL
- Manages alerts and notifications (email, discord bot, webhook support)
- Provides WebSockets-powered real-time updates
- Has scripts that can be executed on servers through SSH and can be triggered from the frontend
### Frontend (next.js)
- Displays real-time server stats
- Shows historical performance trends
- Handles user authentication & dashboards
- Interactive charts (Recharts/D3.js)
- WebSockets for live updates
- Admin panel for server management
- Panel with links to all important management platforms and sites
- Easy access for commands I use a lot (pulling puppet code from git, updating servers, ...)
### Server Agent (Python)
- Runs on each monitored server.
- Collects CPU, RAM, Disk, and Network stats. (psutil)
- Sends data to the PHP API at intervals.
- Can push real-time updates with WebSockets
- Can be installed as a systemd service on Linux.

# Some commands to test the API
## Get token
curl -X POST http://noc.abs.test/auth.php \
     -H "Content-Type: application/json" \
     -d '{"email": "user@example.com", "password": "password"}'
## Add a server
curl -X POST http://noc.abs.test/servers.php \
     -H "Authorization: Bearer TOKEN_HERE" \
     -H "Content-Type: application/json" \
     -d '{"name": "Server 1", "ip_address": "192.168.1.100"}'
## List servers
curl -X GET http://noc.abs.test/servers.php \
     -H "Authorization: Bearer TOKEN_HERE"
## Update metrics on a server
curl -X POST http://noc.abs.test/servers.php?id=1 \
     -H "Authorization: Bearer TOKEN_HERE" \
     -H "Content-Type: application/json" \
     -d '{"cpu_usage": 45.3, "ram_usage": 78.2, "disk_usage": 60.1, "network_usage": 1.5}'     

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
