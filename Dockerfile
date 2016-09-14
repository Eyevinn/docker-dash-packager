FROM eyevinntechnology/packager-base:0.1.0
MAINTAINER Eyevinn Technology <info@eyevinn.se>
RUN apt-get update && apt-get install -y --force-yes \
  apache2 \
  libapache2-mod-php5 \
  curl
RUN a2enmod rewrite
RUN a2enmod allowmethods
RUN a2enmod headers
RUN a2enmod proxy
RUN a2enmod proxy_http
RUN pip install hls2dash
RUN mkdir -p /var/packager && \
  chown www-data.www-data /var/packager
COPY www/ /var/packager/
COPY packager/config/apache2/ /etc/apache2/
COPY packager/php/ /var/packager/
RUN curl -sL https://deb.nodesource.com/setup_4.x | bash - && \
  apt-get install -y nodejs
COPY admin/ /var/packager/admin/
RUN cd /var/packager/admin && npm install
COPY entrypoint.sh /root/entrypoint.sh
EXPOSE 80
CMD /root/entrypoint.sh
