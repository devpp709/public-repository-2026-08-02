FROM minio/minio:latest

# Можно добавить скрипты инициализации
# Но мы вынесем создание бакета в отдельный сервис minio-init

EXPOSE 9000 9001
CMD ["server", "/data", "--console-address", ":9001"]