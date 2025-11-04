ALTER TABLE excursio
  ADD COLUMN cim_nom     VARCHAR(200) AFTER distancia,
  ADD COLUMN cim_alcada  INT          AFTER cim_nom,
  ADD COLUMN cim_comarca VARCHAR(70)  AFTER cim_alcada;

ALTER TABLE excursio DROP FOREIGN KEY excursio_ibfk_1;

ALTER TABLE excursio MODIFY id_cim INT NULL;

UPDATE excursio e
JOIN cim c ON e.id_cim = c.id
SET e.cim_nom = c.nom, e.cim_alcada = c.alcada, e.cim_comarca = c.comarca
WHERE e.id_cim IS NOT NULL;
