-- ============================================================
-- CONSULTA: Estadísticas de reviews por panorama incluyendo último comentario
-- Descripción: Obtiene para cada panorama (agrupado por nombre):
-- 1. El promedio de estrellas (redondeado a 2 decimales)
-- 2. La cantidad total de reviews
-- 3. El último comentario realizado (el de mayor ID)
-- ============================================================

SELECT 
    p.nombre AS "nombre panorama",
    ROUND(AVG(r.estrellas), 2) AS "prom estrellas",
    COUNT(r.id) AS "cant reviews",
    (
        SELECT r2.descripcion 
        FROM Review r2
        JOIN Reserva rs2 ON r2.reserva_id = rs2.id
        WHERE rs2.id = p.id
        ORDER BY r2.id DESC
        LIMIT 1
    ) AS "ult comentario"
FROM 
    Panorama p
JOIN 
    Reserva rs ON p.id = rs.id
JOIN 
    Review r ON rs.id = r.reserva_id
GROUP BY 
    p.nombre, p.id
ORDER BY 
    p.nombre;