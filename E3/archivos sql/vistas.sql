CREATE OR REPLACE VIEW usuario_agenda_participante AS
SELECT 
    u.correo,
    a.etiqueta,
    p.nombre as participante_nombre,
    p.edad as participante_edad
FROM agenda a
JOIN usuario u ON a.correo_usuario = u.correo
JOIN reserva r ON a.id = r.agenda_id
JOIN panorama pn ON r.id = pn.id 
LEFT JOIN participante p ON p.panorama_id = pn.id
ORDER BY a.id DESC;

CREATE OR REPLACE VIEW transporte_detallado AS
SELECT 
    t.id,
    t.lugar_origen,
    t.lugar_llegada,
    t.capacidad,
    t.tiempo_estimado,
    t.precio_asiento,
    t.empresa,
    t.fecha_salida,
    t.fecha_llegada,
    t.correo_empleado,
    r.id as reserva_id,
    r.fecha as fecha_reserva,
    r.monto,
    r.cantidad_personas,
    r.estado_disponibilidad,
    r.puntos,
    a.id as agenda_id,
    a.etiqueta as viaje_etiqueta,
    CASE 
        WHEN av.id IS NOT NULL THEN 'Avión'
        WHEN b.id IS NOT NULL THEN 'Bus'
        WHEN tr.id IS NOT NULL THEN 'Tren'
    END as tipo_transporte,
    av.clase as avion_clase,
    av.escalas as avion_escalas,
    b.tipo as bus_tipo,
    b.comodidades as bus_comodidades,
    tr.paradas as tren_paradas,
    tr.comodidades as tren_comodidades,
    a.correo_usuario
FROM transporte t
LEFT JOIN avion av ON t.id = av.id
LEFT JOIN bus b ON t.id = b.id
LEFT JOIN tren tr ON t.id = tr.id
JOIN reserva r ON t.id = r.id
JOIN agenda a ON r.agenda_id = a.id;


CREATE OR REPLACE VIEW panorama_detallado AS
SELECT 
    p.id,
    p.empresa,
    p.nombre,
    p.descripcion,
    p.ubicacion,
    p.duracion,
    p.precio_persona,
    p.capacidad,
    p.restricciones,
    p.fecha_panorama,
    r.id as reserva_id,
    r.fecha as fecha_reserva,
    r.monto,
    r.cantidad_personas,
    r.estado_disponibilidad,
    r.puntos,
    a.id as agenda_id,
    a.etiqueta as viaje_etiqueta,
    a.correo_usuario,
    STRING_AGG(part.nombre || ' (' || part.edad || ')', ', ') as participantes
FROM panorama p
JOIN reserva r ON p.id = r.id
JOIN agenda a ON r.agenda_id = a.id
LEFT JOIN participante part ON p.id = part.panorama_id
GROUP BY 
    p.id,
    p.empresa,
    p.nombre,
    p.descripcion,
    p.ubicacion,
    p.duracion,
    p.precio_persona,
    p.capacidad,
    p.restricciones,
    p.fecha_panorama,
    r.id,
    r.fecha,
    r.monto,
    r.cantidad_personas,
    r.estado_disponibilidad,
    r.puntos,
    a.id,
    a.etiqueta,
    a.correo_usuario;


CREATE OR REPLACE VIEW hospedaje_detallado AS
SELECT 
    h.id,
    h.nombre_hospedaje,
    h.ubicacion,
    h.precio_noche,
    h.estrellas,
    h.comodidades,
    h.fecha_checkin,
    h.fecha_checkout,
    r.id as reserva_id,
    r.fecha as fecha_reserva,
    r.monto,
    r.cantidad_personas,
    r.estado_disponibilidad,
    r.puntos,
    a.id as agenda_id,
    a.etiqueta as viaje_etiqueta,
    a.correo_usuario,
    CASE 
        WHEN hotel.id IS NOT NULL THEN 'Hotel'
        WHEN airbnb.id IS NOT NULL THEN 'Airbnb'
    END as tipo_hospedaje,
    hotel.politicas as hotel_politicas,
    airbnb.nombre_anfitrion as airbnb_anfitrion,
    airbnb.contacto_anfitrion as airbnb_contacto,
    airbnb.descripcion as airbnb_descripcion,
    airbnb.piezas as airbnb_piezas,
    airbnb.camas as airbnb_camas,
    airbnb.banos as airbnb_banos
FROM hospedaje h
LEFT JOIN hotel ON h.id = hotel.id
LEFT JOIN airbnb ON h.id = airbnb.id
JOIN reserva r ON h.id = r.id
JOIN agenda a ON r.agenda_id = a.id
GROUP BY 
    h.id,
    h.nombre_hospedaje,
    h.ubicacion,
    h.precio_noche,
    h.estrellas,
    h.comodidades,
    h.fecha_checkin,
    h.fecha_checkout,
    r.id,
    r.fecha,
    r.monto,
    r.cantidad_personas,
    r.estado_disponibilidad,
    r.puntos,
    a.id,
    a.etiqueta,
    a.correo_usuario,
    hotel.id,
    hotel.politicas,
    airbnb.id,
    airbnb.nombre_anfitrion,
    airbnb.contacto_anfitrion,
    airbnb.descripcion,
    airbnb.piezas,
    airbnb.camas,
    airbnb.banos;