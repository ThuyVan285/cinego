-- CineGo demo schema (MySQL 5.7+/MariaDB 10.3+)
SET NAMES utf8mb4;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS booking_combos;
DROP TABLE IF EXISTS booking_seats;
DROP TABLE IF EXISTS seat_holds;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS showtimes;
DROP TABLE IF EXISTS seats;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS settings;
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
  status      ENUM('now','soon','ended') NOT NULL,
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

-- Phòng chiếu của từng rạp; sơ đồ ghế nằm ở bảng seats.
CREATE TABLE rooms (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  cinema_id  VARCHAR(24) NOT NULL,
  number     TINYINT     NOT NULL,
  name       VARCHAR(40) NOT NULL,
  format     VARCHAR(10) NOT NULL DEFAULT '2D',
  UNIQUE KEY uq_room (cinema_id, number),
  FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sơ đồ ghế theo phòng: mỗi dòng một ghế (ghế đôi tính là 1 ghế).
CREATE TABLE seats (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  room_id    INT         NOT NULL,
  code       VARCHAR(4)  NOT NULL,
  row_label  CHAR(1)     NOT NULL,
  col_no     TINYINT     NOT NULL,
  type       ENUM('standard','vip','couple') NOT NULL,
  active     TINYINT(1)  NOT NULL DEFAULT 1 COMMENT '0 = ghế hỏng/tạm ngưng',
  UNIQUE KEY uq_seat (room_id, code),
  FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE showtimes (
  id             VARCHAR(64) NOT NULL PRIMARY KEY,
  movie_id       VARCHAR(8)  NOT NULL,
  cinema_id      VARCHAR(24) NOT NULL,
  room_id        INT         NOT NULL,
  show_date      DATE        NOT NULL,
  show_time      CHAR(5)     NOT NULL,
  room           TINYINT     NOT NULL COMMENT 'Số phòng (sao chép từ rooms.number để hiển thị nhanh)',
  price_standard INT         NOT NULL,
  price_vip      INT         NOT NULL,
  price_couple   INT         NOT NULL,
  INDEX idx_date (show_date),
  INDEX idx_movie_date (movie_id, show_date),
  INDEX idx_cinema_date (cinema_id, show_date),
  FOREIGN KEY (movie_id) REFERENCES movies(id),
  FOREIGN KEY (cinema_id) REFERENCES cinemas(id),
  FOREIGN KEY (room_id) REFERENCES rooms(id)
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
  color2       VARCHAR(9)   NOT NULL,
  active       TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE genres (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(40) NOT NULL UNIQUE,
  sort_order SMALLINT    NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cấu hình chỉnh trong trang quản trị (vd. giá vé mặc định).
CREATE TABLE settings (
  k VARCHAR(40)  NOT NULL PRIMARY KEY,
  v VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Giữ lại tài khoản khi cài đặt lại: không DROP bảng users, chỉ tạo nếu chưa có.
CREATE TABLE IF NOT EXISTS users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(80)  NOT NULL,
  email       VARCHAR(120) NOT NULL UNIQUE,
  phone       VARCHAR(15)  NOT NULL,
  password    VARCHAR(255) NOT NULL,
  role        ENUM('user','staff','admin') NOT NULL DEFAULT 'user',
  locked      TINYINT(1)   NOT NULL DEFAULT 0,
  avatar      VARCHAR(255) NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- status: pending = chờ thanh toán online (ghế đang được giữ tới expires_at),
--         confirmed = vé hợp lệ, cancelled = khách huỷ / thanh toán thất bại, expired = quá hạn thanh toán.
CREATE TABLE bookings (
  code           VARCHAR(20)  NOT NULL PRIMARY KEY,
  visitor_id     CHAR(32)     NOT NULL,
  user_id        INT          NULL,
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
  status         ENUM('pending','confirmed','cancelled','expired') NOT NULL DEFAULT 'pending',
  expires_at     DATETIME     NULL,
  cancelled_at   DATETIME     NULL,
  created_at     DATETIME     NOT NULL,
  INDEX idx_visitor (visitor_id, created_at),
  INDEX idx_user (user_id),
  INDEX idx_status (status, expires_at),
  FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ghế của từng đơn. active = 1 khi đơn đang giữ/đã mua ghế, NULL khi đơn đã huỷ/hết hạn (vẫn giữ lịch sử).
-- UNIQUE (showtime_id, seat_code, active): MySQL cho phép nhiều NULL nên chỉ chặn trùng giữa các đơn còn hiệu lực
-- → ngăn đặt trùng ghế ở tầng CSDL.
CREATE TABLE booking_seats (
  booking_code VARCHAR(20) NOT NULL,
  showtime_id  VARCHAR(64) NOT NULL,
  seat_code    VARCHAR(4)  NOT NULL,
  seat_type    ENUM('standard','vip','couple') NOT NULL,
  price        INT         NOT NULL,
  active       TINYINT     NULL DEFAULT 1,
  PRIMARY KEY (booking_code, seat_code),
  UNIQUE KEY uq_active_seat (showtime_id, seat_code, active),
  FOREIGN KEY (booking_code) REFERENCES bookings(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ghế khách đang chọn (giữ tạm vài phút, chưa thanh toán). Hết hạn thì coi như trống.
CREATE TABLE seat_holds (
  showtime_id  VARCHAR(64) NOT NULL,
  seat_code    VARCHAR(4)  NOT NULL,
  visitor_id   CHAR(32)    NOT NULL,
  expires_at   DATETIME    NOT NULL,
  PRIMARY KEY (showtime_id, seat_code),
  INDEX idx_hold_visitor (visitor_id),
  INDEX idx_hold_expires (expires_at),
  FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
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

-- Giao dịch thanh toán của đơn đặt vé (một đơn có thể có nhiều lần thử thanh toán).
-- status: pending (đã chuyển sang cổng), success, failed, unpaid (trả tại quầy), refund_pending, refunded.
CREATE TABLE payments (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  booking_code     VARCHAR(20)  NOT NULL,
  provider         VARCHAR(20)  NOT NULL,
  txn_ref          VARCHAR(40)  NOT NULL UNIQUE,
  amount           INT          NOT NULL,
  status           ENUM('pending','success','failed','unpaid','refund_pending','refunded') NOT NULL DEFAULT 'pending',
  bank_code        VARCHAR(20)  NULL,
  provider_txn_no  VARCHAR(40)  NULL,
  response_code    VARCHAR(10)  NULL,
  message          VARCHAR(255) NULL,
  raw_response     TEXT         NULL,
  created_at       DATETIME     NOT NULL,
  paid_at          DATETIME     NULL,
  INDEX idx_pay_booking (booking_code),
  FOREIGN KEY (booking_code) REFERENCES bookings(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
