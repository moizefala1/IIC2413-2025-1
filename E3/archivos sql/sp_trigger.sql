
CREATE OR REPLACE FUNCTION calcular_puntos_usuario(agenda_id INTEGER)
RETURNS void AS $$
DECLARE
    puntos_agenda NUMERIC;
BEGIN
    puntos_agenda = (SELECT (SUM(r.monto) / 1000) 
                    FROM reserva r 
                    WHERE r.agenda_id = calcular_puntos_usuario.agenda_id);
    UPDATE usuario u
    SET puntos = puntos + puntos_agenda
    WHERE u.correo = (SELECT correo_usuario 
                     FROM agenda
                     WHERE id = calcular_puntos_usuario.agenda_id);
END;
$$ LANGUAGE plpgsql;