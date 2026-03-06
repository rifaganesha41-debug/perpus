CREATE DATABASE IF NOT EXISTS perpus_rifa;
USE perpus_rifa;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL,
    kelas VARCHAR(50) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (username),
    INDEX (role)
);

CREATE TABLE buku (
    id_buku INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) NOT NULL,
    tahun_terbit YEAR NOT NULL,
    stok INT DEFAULT 0,
    lokasi_rak VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (judul)
);

CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_buku INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE DEFAULT NULL,
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku) ON DELETE CASCADE,
    INDEX (status),
    INDEX (id_user),
    INDEX (id_buku)
);

-- Insert default admin (admin123)
-- MD5: 0192023a7bbd73250516f069df18b500
INSERT INTO users (nama, username, password, role) VALUES 
('Administrator', 'admin', '0192023a7bbd73250516f069df18b500', 'admin');

-- Insert sample books
INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, stok, lokasi_rak) VALUES
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 5, 'RAK-01'),
('Bumi', 'Tere Liye', 'Gramedia', 2014, 3, 'RAK-02'),
('Filosofi Kopi', 'Dee Lestari', 'Truedee Books', 2006, 2, 'RAK-01');
