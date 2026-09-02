create table automotive_api.cars (
	idCars SERIAL PRIMARY KEY,
    nama_mobil varchar(100) not null,
    idMerek_fk INTEGER,
    idJenis_fk INTEGER,
    horse_power INTEGER
);

alter table automotive_api.cars
	add column nama_foto varchar(100);
alter table automotive_api.cars
	ALTER COLUMN idStatus_fk TYPE INTEGER USING idStatus_fk::INTEGER;

CREATE TABLE automotive_api.merek (
	idMerek SERIAL PRIMARY KEY,
	namaMerek VARCHAR(100)
);

CREATE TABLE automotive_api.jenis (
	idMerek SERIAL PRIMARY KEY,
	namaJenis VARCHAR(100)
);
-- Salah penamaan idMerek, seharusnya idJenis
ALTER TABLE automotive_api.jenis
	RENAME COLUMN idMerek TO idJenis;
	

CREATE TABLE automotive_api."status" (
	idStatus SERIAL PRIMARY KEY,
	namaStatus VARCHAR(100)
);

--Add Constraint for each Cars dropdown selection
ALTER TABLE automotive_api.cars
ADD CONSTRAINT fk_cars_merek
FOREIGN KEY (idMerek_fk) REFERENCES automotive_api.merek(idMerek);

ALTER TABLE automotive_api.cars
ADD CONSTRAINT fk_cars_jenis
FOREIGN KEY (idJenis_fk) REFERENCES automotive_api.jenis(idJenis);

ALTER TABLE automotive_api.cars
ADD CONSTRAINT fk_cars_status
FOREIGN KEY (idStatus_fk) REFERENCES automotive_api."status"(idStatus);

-- Insert the Types (jenis), Brand (Merek), and Status
INSERT INTO automotive_api.jenis(namaJenis)
VALUES
	('Hatchback'), ('MPV'), ('SUV'), ('Minivan'), ('Van');
INSERT INTO automotive_api.jenis(namajenis)
	values('Low-MPV');

INSERT INTO automotive_api.merek(namaMerek)
VALUES
	('Toyota'), ('Honda'), ('Suzuki'), ('Mitsubishi');
	
INSERT INTO automotive_api.status(namaStatus)
VALUES
	('Need Preview'), ('Approved');

-- Insert data mobil
INSERT INTO automotive_api.cars(nama_mobil, idMerek_fk, idJenis_fk, horse_power)
VALUES
		('Toyota Yaris', 1, 1, 106),
        ('Honda Jazz', 2, 1, 118),
        ('Suzuki Swift', 3, 1, 89),
        ('Mitsubishi Mirage', 4, 1, 76),
        ('Toyota Avanza', 1, 2, 103),
        ('Honda Mobilio', 2, 2, 118),
		('Suzuki Ertiga', 3, 2, 103),
        ('Mitsubishi Expander', 4, 2, 104),
        ('Toyota RAV4', 1, 3, 203),
        ('Honda CR-V', 2, 3, 190),
        ('Suzuki Jimny', 3, 3, 101),
        ('Mitsubishi Outlander', 4, 3, 181),
        ('Toyota Alphard', 1, 4, 275),
        ('Honda Odyssey', 2, 4, 212),
        ('Suzuki Every', 3, 5, 63),
        ('Mitsubishi Delica', 4, 5, 147),
        ('Toyota Sienta', 1, 6, 109),
        ('Honda Freed', 2, 6, 129),
        ('Suzuki Spacia', 3, 6, 52),
        ('Mitsubishi Expander', 4, 6, 104);

INSERT INTO automotive_api.cars(nama_mobil, idMerek_fk, idJenis_fk, horse_power, idStatus_fk)
VALUES
	('Test Carrr', 3, 3, 1001, 1);

-- Update the Status of each Cars Above
UPDATE automotive_api.cars
SET idStatus_fk = 2
WHERE idCars BETWEEN 1 AND 49;

SELECT * FROM cars;
SELECT * FROM merek;
SELECT * FROM jenis;
SELECT * FROM status;