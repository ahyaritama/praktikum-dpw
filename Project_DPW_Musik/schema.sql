CREATE DATABASE IF NOT EXISTS undangan_db CHARACTER SET utf8mb4;
USE undangan_db;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

-- Hanya menyimpan nilai yang diubah dari dashboard; default ada di fields.php
CREATE TABLE IF NOT EXISTS content (
  k VARCHAR(60) PRIMARY KEY,
  v TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS guests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code CHAR(8) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NULL,
  phone VARCHAR(30) NULL,
  pax INT NOT NULL DEFAULT 1,
  status ENUM('menunggu','hadir','tidak_hadir') NOT NULL DEFAULT 'menunggu',
  confirmed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wishes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  guest_id INT NULL,
  name VARCHAR(100) NOT NULL,
  message VARCHAR(300) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE SET NULL
);

-- Admin awal: admin@gmail.com / admin123 (ganti di menu Pengaturan)
INSERT IGNORE INTO admins (email, password_hash)
VALUES ('admin@gmail.com', '$2y$10$vdlc2VONNNB4h9NLcFc3pO4N1p8Hkxj.qScxvZSOIh46UEZKVaL6a');

-- Ucapan contoh (hanya jika tabel masih kosong)
INSERT INTO wishes (name, message)
SELECT * FROM (
  SELECT 'Tio' a, 'Mantap cuy, akhirnya menikah juga!' b
  UNION ALL SELECT 'Siti', 'Sakinah selalu ya buat kalian berdua.'
  UNION ALL SELECT 'Budi', 'Congratulations bro! Ikut bahagia.'
  UNION ALL SELECT 'Cika', 'Wow congratss! Semoga lancar sampai hari H.'
  UNION ALL SELECT 'Amira', 'Happy wedding Mufid dan Cia tercinta!'
  UNION ALL SELECT 'Rizki', 'Barakallah, semoga sakinah mawaddah warahmah.'
  UNION ALL SELECT 'Dinda', 'Selamat menempuh hidup baru ya!'
  UNION ALL SELECT 'Vito', 'Happy Wedding!'
) t
WHERE NOT EXISTS (SELECT 1 FROM wishes);
