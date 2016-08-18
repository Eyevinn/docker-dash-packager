FROM eyevinntechnology/packager-base:0.1.0
MAINTAINER Eyevinn Technology <info@eyevinn.se>
RUN apt-get update && apt-get install -y --force-yes \
  apache2 \
  libapache2-mod-php5
RUN a2enmod rewrite
RUN a2enmod allowmethods
RUN a2enmod headers
RUN pip install hls2dash
COPY www/index.html /var/www/html/index.html
EXPOSE 80
CMD chown -R www-data:www-data /data && /usr/sbin/apache2ctl -D FOREGROUND
