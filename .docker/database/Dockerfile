FROM mariadb:10.3.34

HEALTHCHECK  --interval=3s --timeout=1s \
  CMD mysqladmin ping -p${MYSQL_ROOT_PASSWORD}

EXPOSE 3306