#!/bin/bash

chown www-data:www-data /data
/usr/sbin/apache2ctl -D FOREGROUND
