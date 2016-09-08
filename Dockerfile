FROM eyevinntechnology/packager-base:0.1.0
MAINTAINER Eyevinn Technology <info@eyevinn.se>
RUN apt-get update && apt-get install -y --force-yes \
  apache2 \
  libapache2-mod-php5
RUN a2enmod rewrite
RUN a2enmod allowmethods
RUN a2enmod headers
RUN pip install hls2dash
RUN mkdir -p /var/packager && \
  chown www-data.www-data /var/packager
COPY www/index.html /var/packager/index.html
COPY packager/config/apache2/ /etc/apache2/
COPY packager/php/ /var/packager/
COPY entrypoint.sh /root/entrypoint.sh
EXPOSE 80
CMD /root/entrypoint.sh
