USE gestiune_utilizatori;

UPDATE utilizatori
SET parola_hash = SHA2('root', 256)
WHERE email IN (
    'admin@portal.md',
    'redactor@portal.md',
    'andrei@portal.md',
    'elena@portal.md'
);

SELECT email, denumire_rol, 'root' AS parola_noua
FROM utilizatori u
JOIN roluri r USING(id_rol)
WHERE email IN ('admin@portal.md','redactor@portal.md','andrei@portal.md');
