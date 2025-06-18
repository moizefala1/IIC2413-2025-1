CREATE OR REPLACE FUNCTION unaccent(text)
RETURNS text AS $$
DECLARE
    input_text text := $1;
BEGIN
    input_text := translate(input_text, 'áéíóúÁÉÍÓÚñÑ', 'aeiouAEIOUnN');
    RETURN input_text;
END;
$$ LANGUAGE plpgsql;