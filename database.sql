
CREATE DATABASE IF NOT EXISTS liga_basquetbol
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE liga_basquetbol;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    city VARCHAR(100) NOT NULL,
    coach VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    jersey_number INT NOT NULL,
    position VARCHAR(40) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_players_team
        FOREIGN KEY (team_id)
        REFERENCES teams(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    UNIQUE KEY unique_jersey_per_team
        (team_id, jersey_number)
);

CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    match_date DATETIME NOT NULL,
    venue VARCHAR(150) NOT NULL,
    home_score INT UNSIGNED DEFAULT NULL,
    away_score INT UNSIGNED DEFAULT NULL,
    status ENUM('Programado', 'Finalizado')
        NOT NULL DEFAULT 'Programado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_match_home
        FOREIGN KEY (home_team_id)
        REFERENCES teams(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_match_away
        FOREIGN KEY (away_team_id)
        REFERENCES teams(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_different_teams
        CHECK (home_team_id <> away_team_id)
);
