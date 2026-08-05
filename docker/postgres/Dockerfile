FROM postgres:16-alpine

# Копируем init скрипты в стандартную директорию
COPY ./init/* /docker-entrypoint-initdb.d/

# Даем права на выполнение (опционально)
RUN chmod +x /docker-entrypoint-initdb.d/*.sh 2>/dev/null || true

EXPOSE 5432