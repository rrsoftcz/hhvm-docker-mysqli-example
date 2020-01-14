FROM hhvm/hhvm-proxygen:latest

RUN rm -rf /var/www
# ADD . /var/www
# ADD ./config/server.ini /etc/hhvm/server.ini 
ADD site.ini /etc/hhvm/site.ini

# COPY ./config/server.ini /etc/hhvm/server.ini 
EXPOSE 80
