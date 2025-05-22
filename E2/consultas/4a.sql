-- ============================================================
-- CONSULTA: Top 3 meses con mayor cantidad de panoramas
-- Descripción: Identifica los 3 meses con más panoramas ofrecidos,
-- mostrando para cada uno:
-- - El mes en formato MM-YYYY
-- - Cantidad total de panoramas
-- - Total de participantes en esos panoramas
-- - Monto total generado
-- ============================================================

WITH DatosMensuales AS (
    SELECT
        TO_CHAR(p.fecha_panorama, 'MM-YYYY') AS mes,
        COUNT(DISTINCT p.id) AS cantidad_panoramas,
        SUM(pa.cantidad_personas) AS cantidad_participantes,
        SUM(pa.monto) AS monto_ganado
    FROM
        Panorama p
    JOIN
        Reserva pa ON p.id = pa.id
    GROUP BY
        TO_CHAR(p.fecha_panorama, 'MM-YYYY')
)

SELECT
    mes AS "mes (MM-YYYY)",
    cantidad_panoramas AS "cantidad panoramas",
    cantidad_participantes AS "cantidad participantes",
    monto_ganado AS "monto ganado"
FROM
    DatosMensuales
ORDER BY
    cantidad_panoramas DESC,
    cantidad_participantes DESC
LIMIT 3;