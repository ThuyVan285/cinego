-- CineGo demo schema (MySQL 5.7+/MariaDB 10.3+)
SET NAMES utf8mb4;

DROP TABLE IF EXISTS booking_combos;
DROP TABLE IF EXISTS booking_seats;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS showtimes;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS combos;
DROP TABLE IF EXISTS cinemas;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS movies;

CREATE TABLE movies (
  id          VARCHAR(8)   NOT NULL PRIMARY KEY,
  title       VARCHAR(120) NOT NULL,
  genres      VARCHAR(160) NOT NULL,
  duration    SMALLINT     NOT NULL,
  age         VARCHAR(4)   NOT NULL,
  rating      DECIMAL(3,1) NOT NULL DEFAULT 0,
  status      ENUM('now','soon') NOT NULL,
  release_date DATE        NOT NULL,
  director    VARCHAR(80)  NOT NULL,
  cast_list   VARCHAR(255) NOT NULL,
  synopsis    TEXT         NOT NULL,
  palette     VARCHAR(40)  NOT NULL,
  poster_url  VARCHAR(255) NULL,
  trailer_url VARCHAR(255) NULL,
  featured    TINYINT(1)   NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE brands (
  id    VARCHAR(16) NOT NULL PRIMARY KEY,
  name  VARCHAR(60) NOT NULL,
  color VARCHAR(9)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cinemas (
  id      VARCHAR(24)  NOT NULL PRIMARY KEY,
  brand   VARCHAR(16)  NOT NULL,
  name    VARCHAR(100) NOT NULL,
  city    VARCHAR(40)  NOT NULL,
  area    VARCHAR(40)  NOT NULL,
  address VARCHAR(160) NOT NULL,
  rooms   TINYINT      NOT NULL,
  FOREIGN KEY (brand) REFERENCES brands(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE showtimes (
  id             VARCHAR(64) NOT NULL PRIMARY KEY,
  movie_id       VARCHAR(8)  NOT NULL,
  cinema_id      VARCHAR(24) NOT NULL,
  show_date      DATE        NOT NULL,
  show_time      CHAR(5)     NOT NULL,
  room           TINYINT     NOT NULL,
  price_standard INT         NOT NULL,
  price_vip      INT         NOT NULL,
  price_couple   INT         NOT NULL,
  INDEX idx_date (show_date),
  INDEX idx_movie_date (movie_id, show_date),
  INDEX idx_cinema_date (cinema_id, show_date),
  FOREIGN KEY (movie_id) REFERENCES movies(id),
  FOREIGN KEY (cinema_id) REFERENCES cinemas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE combos (
  id          VARCHAR(16)  NOT NULL PRIMARY KEY,
  name        VARCHAR(60)  NOT NULL,
  description VARCHAR(160) NOT NULL,
  price       INT          NOT NULL,
  sort_order  TINYINT      NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE promotions (
  code         VARCHAR(20)  NOT NULL PRIMARY KEY,
  title        VARCHAR(120) NOT NULL,
  description  VARCHAR(255) NOT NULL,
  conditions   VARCHAR(255) NOT NULL,
  expires_at   DATE         NOT NULL,
  type         ENUM('pct','fixed') NOT NULL,
  value        INT          NOT NULL,
  max_discount INT          NOT NULL,
  min_spend    INT          NOT NULL,
  need_combo   TINYINT(1)   NOT NULL DEFAULT 0,
  tag          VARCHAR(12)  NOT NULL,
  color1       VARCHAR(9)   NOT NULL,
  color2       VARCHAR(9)   NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
  code           VARCHAR(20)  NOT NULL PRIMARY KEY,
  visitor_id     CHAR(32)     NOT NULL,
  showtime_id    VARCHAR(64)  NOT NULL,
  customer_name  VARCHAR(80)  NOT NULL,
  email          VARCHAR(120) NOT NULL,
  phone          VARCHAR(15)  NOT NULL,
  payment_method VARCHAR(20)  NOT NULL,
  promo_code     VARCHAR(20)  NULL,
  ticket_total   INT          NOT NULL,
  combo_total    INT          NOT NULL,
  discount       INT          NOT NULL,
  total          INT          NOT NULL,
  created_at     DATETIME     NOT NULL,
  INDEX idx_visitor (visitor_id, created_at),
  FOREIGN KEY (showtime_id) REFERENCES showtimes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- UNIQUE (showtime_id, seat_code) ngăn đặt trùng ghế ở tầng CSDL.
CREATE TABLE booking_seats (
  showtime_id  VARCHAR(64) NOT NULL,
  seat_code    VARCHAR(4)  NOT NULL,
  seat_type    ENUM('standard','vip','couple') NOT NULL,
  price        INT         NOT NULL,
  booking_code VARCHAR(20) NOT NULL,
  PRIMARY KEY (showtime_id, seat_code),
  FOREIGN KEY (booking_code) REFERENCES bookings(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_combos (
  booking_code VARCHAR(20) NOT NULL,
  combo_id     VARCHAR(16) NOT NULL,
  name         VARCHAR(60) NOT NULL,
  qty          TINYINT     NOT NULL,
  price        INT         NOT NULL,
  PRIMARY KEY (booking_code, combo_id),
  FOREIGN KEY (booking_code) REFERENCES bookings(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
