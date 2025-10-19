FROM nginx:1.25

COPY server.crt /etc/nginx/server.crt
COPY server.key /etc/nginx/server.key
COPY default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
EXPOSE 443
