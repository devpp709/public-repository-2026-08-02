INSERT INTO users (email, username, password, updated_at)
VALUES ('test@example.com', 'temp_user', '$2b$10$1U./mp/6x.yvV8uFOm/vce74CPxeeQKlsTf50P2xTjYsmNpjT22CO', NOW());

INSERT INTO public.zones (id, name, base_price_per_pixel, x_start, x_end, y_start, y_end, priority, color, size) VALUES (1, 'outer', 0.01, 0, 1200, 0, 1200, 1, 'eeeeee', 1200);
INSERT INTO public.zones (id, name, base_price_per_pixel, x_start, x_end, y_start, y_end, priority, color, size) VALUES (2, 'middle', 0.05, 200, 1000, 200, 1000, 2, '2ecc71', 800);
INSERT INTO public.zones (id, name, base_price_per_pixel, x_start, x_end, y_start, y_end, priority, color, size) VALUES (3, 'center', 0.1, 400, 800, 400, 800, 3, 'f1c40f', 400);
