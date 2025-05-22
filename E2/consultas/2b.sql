-- ============================================================
-- CONSULTA: Hoteles disponibles con múltiples políticas
-- Descripción: Esta consulta filtra hoteles que tienen más de una
-- política registrada, mostrando su información básica ordenada
-- primero por estrellas (descendente) y luego por precio (ascendente)
-- ============================================================

SELECT 
    h.nombre AS "nombre hospedaje",
    h.ubicacion,
    h.estrellas,
    h.precio_noche AS "precio noche"
FROM 
    Hospedaje h
JOIN 
    Hotel ht ON h.id = ht.id
JOIN 
    Reserva r ON h.id = r.id
WHERE 
    r.estado_disponibilidad = 'Disponible'
    AND array_length(ht.politicas, 1) > 1 
ORDER BY 
    h.estrellas DESC,  
    h.precio_noche ASC;  