CREATE DATABASE hall_of_legends;
USE hall_of_legends;

CREATE TABLE users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    rep         INT NOT NULL DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE entries (
    entry_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    title        VARCHAR(150) NOT NULL,
    game         VARCHAR(100) NOT NULL,
    type         VARCHAR(50) NOT NULL,
    description  TEXT NOT NULL,
    file_path    VARCHAR(255) DEFAULT NULL,
    rep_awarded  INT NOT NULL DEFAULT 0,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE likes (
    like_id     INT AUTO_INCREMENT PRIMARY KEY,
    entry_id    INT NOT NULL,
    user_id     INT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (entry_id, user_id),
    FOREIGN KEY (entry_id) REFERENCES entries(entry_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE comments (
    comment_id  INT AUTO_INCREMENT PRIMARY KEY,
    entry_id    INT NOT NULL,
    user_id     INT NOT NULL,
    text        TEXT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entry_id) REFERENCES entries(entry_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- View 1: feed data — JOIN + correlated SUBQUERY (like_count)
CREATE VIEW entry_feed_view AS
SELECT
    e.entry_id, e.user_id, e.title, e.game, e.type, e.description,
    e.file_path, e.rep_awarded, e.created_at,
    u.username,
    (SELECT COUNT(*) FROM likes l WHERE l.entry_id = e.entry_id) AS like_count
FROM entries e
JOIN users u ON u.user_id = e.user_id;

-- View 2: leaderboard — WINDOW FUNCTION (RANK)
CREATE VIEW leaderboard_view AS
SELECT
    user_id, username, rep,
    RANK() OVER (ORDER BY rep DESC) AS position
FROM users;

-- Seed data. Demo password for every account below is: password
INSERT INTO users (username, email, password, role, rep) VALUES
('admin_dragon', 'admin@hall.dev', 'password', 'admin', 9200),
('mika.exe', 'mika@hall.dev', 'password', 'user', 9910),
('jrunner_45', 'jrunner@hall.dev', 'password', 'user', 8420),
('10noblade', 'noblade@hall.dev', 'password', 'user', 7540),
('coldstreak', 'cold@hall.dev', 'password', 'user', 6110);

INSERT INTO entries (user_id, title, game, type, description, rep_awarded) VALUES
(3, 'Sub 20 speedrun, First clean deathless run.', 'Celeste', 'Speedrun', 'Route skips the crystal heart pickup in chapter 4 and takes the golden feather shortcut instead.', 350),
(2, 'Solo Malenia kill, no summons, no bleed.', 'Elden Ring', 'Boss Kill', 'Pure melee, no summons, no bleed build. Took about 40 attempts.', 90),
(5, 'Pantheon of Hollownest, First Clear', 'Hollow Knight', 'Speedrun', 'First deathless clear of the Pantheon of Hollownest.', 350),
(4, 'Heat 32 clear, No boons repeated', 'Hades', 'Screenshot', 'Cleared a Heat 32 run without repeating a single boon the whole run.', 210);

INSERT INTO likes (entry_id, user_id) VALUES
(1, 2), (1, 4), (2, 1), (2, 3), (2, 4), (3, 1), (4, 2), (4, 3);

INSERT INTO comments (entry_id, user_id, text) VALUES
(1, 2, 'that route is filthy, stealing this'),
(1, 3, 'new record, congrats'),
(2, 4, 'insane execution honestly');