-- ============================================================
-- CONSULTA: Hospedajes disponibles ordenados por calificación
-- Descripción: Esta consulta recupera los hospedajes disponibles
-- mostrando su nombre, ubicación, precio por noche y calificación
-- en estrellas, ordenados de mayor a menor calificación.
-- ============================================================

SELECT 
    h.nombre AS "nombre hospedaje",
    h.ubicacion,
    h.estrellas,
    h.precio_noche AS "precio noche"
FROM 
    Hospedaje h
JOIN 
    Reserva r ON h.id = r.id
WHERE 
    r.estado_disponibilidad = 'Disponible'
ORDER BY 
    h.estrellas DESC;