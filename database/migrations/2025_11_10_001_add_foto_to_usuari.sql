ALTER TABLE usuari
  ADD COLUMN foto VARCHAR(255) NULL AFTER cognom;
  UPDATE usuari SET foto = 'uploads/avatars/default.png' WHERE foto IS NULL;

