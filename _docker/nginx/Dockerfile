FROM nginx:latest

ADD ./_docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN usermod -u 1000 www-data

WORKDIR /var/www