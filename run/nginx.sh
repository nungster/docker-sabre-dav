#!/bin/sh
set -e
/bin/sed -i "s/\$REALM/${REALM}/" /etc/nginx/sites-available/default
exec /usr/sbin/nginx

