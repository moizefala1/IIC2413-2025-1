-- ============================================================
-- CONSULTA: Usuarios destacados por reservas o puntos
-- Descripción: Identifica usuarios con:
-- - Más de 70 reservas en total O
-- - Más de 1000 puntos acumulados
-- Muestra para cada usuario:
-- - Nombre
-- - Cantidad total de reservas
-- - Puntos acumulados
-- Ordenados por puntos de mayor a menor
-- ============================================================

SELECT 
    p.nombre AS "nombre usuario",
    COUNT(r.id) AS "cantidad reservas",
    u.puntos AS "puntos"
FROM 
    Usuario u
JOIN 
    Persona p ON u.correo = p.correo
LEFT JOIN 
    Agenda a ON u.correo = a.correo_usuario
LEFT JOIN 
    Reserva r ON a.id = r.agenda_id
GROUP BY 
    p.nombre, u.puntos
HAVING 
    COUNT(r.id) > 70 OR u.puntos > 1000
ORDER BY 
    u.puntos DESC;
