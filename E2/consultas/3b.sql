-- ============================================================
-- CONSULTA: Estadísticas completas de reviews por panorama
-- Descripción: Obtiene para cada panorama:
-- - El promedio de calificaciones (estrellas)
-- - La cantidad total de reviews
-- - El último comentario realizado (descripción más reciente)
-- ============================================================

WITH ReviewsPanoramas AS (
    SELECT 
        p.nombre AS panorama_nombre,
        rv.estrellas,
        rv.descripcion,
        rv.id AS review_id,
        rs.fecha AS fecha_review,
        FIRST_VALUE(rv.descripcion) OVER (
            PARTITION BY p.id 
            ORDER BY rs.fecha DESC, rv.id DESC
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS ultimo_comentario
    FROM 
        Panorama p
    JOIN 
        Reserva rs ON p.id = rs.id
    JOIN 
        Review rv ON rs.id = rv.reserva_id
)

SELECT 
    panorama_nombre AS "nombre panorama",
    ROUND(AVG(estrellas), 2) AS "prom estrellas",
    COUNT(review_id) AS "cant reviews",
    MAX(ultimo_comentario) AS "ult comentario"
FROM 
    ReviewsPanoramas
GROUP BY 
    panorama_nombre
ORDER BY 
    "prom estrellas" DESC;