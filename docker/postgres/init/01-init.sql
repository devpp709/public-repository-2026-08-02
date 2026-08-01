DO
$$
    BEGIN
        IF NOT EXISTS (
            SELECT FROM pg_roles WHERE rolname = 'rentcar'
        ) THEN
            CREATE ROLE rentcar LOGIN PASSWORD 'rentcarpass';
        END IF;
    END
$$;

SELECT 'CREATE DATABASE rentcar_db OWNER rentcar'
WHERE NOT EXISTS (
    SELECT FROM pg_database WHERE datname = 'rentcar_db'
)\gexec