#!/bin/bash
while true; do
    php /var/www/html/system/ee/eecli.php queue:consume --limit=3
    sleep 1
done

# This is the only way to get the script to run every second. Crontab only supports every minute at minimum,
# thus the React app does not show live updates of the queue size and job list. Running this script and looping
# every second helps the React app reflect the changes in real time.

# nohup bash addons/queue/loop.sh &
