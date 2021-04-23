#!/bin/sh
## Start the supervisor queue worker
touch /var/run/supervisor.sock
supervisord -c /etc/supervisor/supervisord.conf
## Start crontab process
service cron start
# Pass execution back to commandline
exec "$@"
