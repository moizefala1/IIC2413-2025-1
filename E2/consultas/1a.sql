-- ============================================================
-- CONSULTA 1: Cantidad de reservas y monto total por mes
-- Descripción: Esta consulta calcula el número de reservas y el 
-- monto total acumulado agrupado por mes y año.
-- ============================================================

SELECT 
    TO_CHAR(fecha, 'MM-YYYY') AS mes,
    COUNT(*) AS cantidad_reservas,
    SUM(monto) AS monto_total
FROM 
    Reserva
GROUP BY 
    TO_CHAR(fecha, 'MM-YYYY')
ORDER BY 
    TO_CHAR(fecha, 'MM-YYYY');