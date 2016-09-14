#!/bin/bash

chown www-data:www-data /data
cd /var/packager/admin && DATAPATH=/data/live npm start &
/usr/sbin/apache2ctl -D FOREGROUND
