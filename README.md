# ABS Network Operation Center
- Goal: DevOps & Server Monitoring Platform
  - Monitors server resources and puppet
  - Panel with links to all important management platforms and sites (promox, phpmyadmin, hoster panels, google analytics, office, ... )
  - Buttons for commands I use a lot (pulling puppet code from git, updating servers, ...)
- Stack:
  - Backend: Custom PHP API with MySL and secured with JWT
  - Agent: Python
  - Frontend: Next.js with router and JWT authentication
  - WebSockets: Real-time updates via PHP WebSockets
- Planned enhancements:
  - Fetch and display live monitoring data on the dashboard
  - Live real-time monitoring with websockets
  - Alerts and notifications
  - Historical data storage and trends
  - More system metrics
  - Puppet metrics
  - Links to management sites
  - Buttons for commands I use a lot
  - UI improvements
    - Dark mode
    - Responsive design
    - Nicer layout
  - Enhancing API security
  - Logout button
  - Role-based access
  - 

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
nano /etc/systemd/system/server-agent.service
'''
[Unit]
Description=Server Monitoring Agent
After=network.target

[Service]
ExecStart=/usr/bin/python3 /path/to/agent.py
Restart=always
User=root

[Install]
WantedBy=multi-user.target
'''

systemctl daemon-reload
systemctl enable server-agent
systemctl start server-agent
systemctl status server-agent

## ON WINDOWS
- Open Task Scheduler
- Create a New Task
- Set Trigger → "At startup" or "Every 5 minutes"
- Set Action → "Start a program"
  - Program: python
  - Arguments: "C:\path\to\agent.py"
- Save & Start the Task
