CREATE TABLE meranie (
  id    INT NOT NULL AUTO_INCREMENT,
  time           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  temp       DECIMAL(5,2) NOT NULL,
  humid       DECIMAL(5,2) NOT NULL,
  press         DECIMAL(6,2) NOT NULL,
  ppm           DECIMAL(7,1) NOT NULL,
  PRIMARY KEY (id_merania),
  CONSTRAINT chk_teplota CHECK (teplota BETWEEN -40 AND 85),
  CONSTRAINT chk_vlhkost CHECK (vlhkost BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;