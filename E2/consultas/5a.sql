-- ============================================================
-- CONSULTA: Top 5 usuarios con más puntos y sus reservas futuras
-- Descripción: Identifica los 5 usuarios con mayor cantidad de puntos,
-- mostrando para cada uno:
-- - Nombre del usuario
-- - Puntos acumulados
-- - Cantidad de reservas futuras (a partir del 27 de mayo de 2024)
-- ============================================================

SELECT 
    pe.nombre AS "nombre usuario",
    u.puntos AS "puntos",
    COUNT(CASE WHEN r.fecha > '2024-05-27' THEN 1 END) AS "cantidad reservas"
FROM 
    Usuario u
JOIN 
    Persona pe ON u.correo = pe.correo
LEFT JOIN 
    Agenda a ON u.correo = a.correo_usuario
LEFT JOIN 
    Reserva r ON a.id = r.agenda_id
GROUP BY 
    pe.nombre, u.puntos
ORDER BY 
    u.puntos DESC
LIMIT 5;