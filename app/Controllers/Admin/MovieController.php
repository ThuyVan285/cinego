<?php
declare(strict_types=1);

namespace Admin;

use Database;
use Genre;
use Movie;

/** Quản lý phim: danh sách, thêm, sửa, đổi trạng thái, xoá. (Chỉ admin) */
final class MovieController
{
    public const STATUS = ['now' => 'Đang chiếu', 'soon' => 'Sắp chiếu', 'ended' => 'Ngừng chiếu'];
    public const AGES = ['P', 'K', 'T13', 'T16', 'T18'];
    private const POSTER_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public static function index(): void
    {
        $f = ['q' => queryStr('q'), 'status' => queryStr('status'), 'genre' => queryStr('genre')];
        $where = [];
        $p = [];
        if ($f['q'] !== '') {
            $where[] = '(m.title LIKE ? OR m.director LIKE ? OR m.id = ?)';
            array_push($p, Ui::like($f['q']), Ui::like($f['q']), $f['q']);
        }
        if (isset(self::STATUS[$f['status']])) {
            $where[] = 'm.status = ?';
            $p[] = $f['status'];
        }
        if ($f['genre'] !== '') {
            $where[] = "FIND_IN_SET(?, REPLACE(m.genres, ', ', ',')) > 0";
            $p[] = $f['genre'];
        }
        $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $pg = Ui::paginate((int)Database::one("SELECT COUNT(*) n FROM movies m$w", $p)['n']);
        $rows = Database::all("SELECT m.*,
                (SELECT COUNT(*) FROM showtimes s WHERE s.movie_id = m.id AND s.show_date >= CURDATE()) AS upcoming_shows,
                (SELECT COUNT(*) FROM bookings b JOIN showtimes s ON s.id = b.showtime_id WHERE s.movie_id = m.id AND b.status = 'confirmed') AS bookings
            FROM movies m$w ORDER BY FIELD(m.status, 'now', 'soon', 'ended'), m.release_date DESC
            LIMIT " . Ui::PER_PAGE . ' OFFSET ' . $pg['offset'], $p);
        Ui::render('movies/index', 'Phim', 'movies', ['rows' => $rows, 'f' => $f, 'pg' => $pg, 'genres' => Genre::all()]);
    }

    public static function create(): void
    {
        self::form(null);
    }

    public static function edit(string $id): void
    {
        $m = Movie::find($id) ?? notFound();
        self::form($m);
    }

    private static function form(?array $m, array $errors = []): void
    {
        Ui::render('movies/form', $m ? 'Sửa phim' : 'Thêm phim', 'movies', [
            'm' => $m ?? ['id' => '', 'title' => '', 'genres' => [], 'duration' => 0, 'age' => 'P', 'rating' => 0, 'status' => 'soon',
                'release' => today(), 'director' => '', 'cast' => [], 'synopsis' => '', 'poster' => '', 'trailer' => '', 'featured' => false],
            'isNew' => $m === null || ($m['id'] ?? '') === '', 'errors' => $errors, 'genres' => Genre::all(),
        ]);
    }

    public static function save(): void
    {
        checkCsrf();
        $id = Ui::str('id');
        $old = $id !== '' ? Movie::find($id) : null;
        if ($id !== '' && !$old) {
            notFound();
        }
        $d = [
            'title' => Ui::str('title'),
            'genres' => array_values(array_intersect(Ui::list('genres'), Genre::all())),
            'duration' => max(0, Ui::int('duration')),
            'age' => in_array(Ui::str('age'), self::AGES, true) ? Ui::str('age') : '',
            'rating' => round(min(10, max(0, (float)str_replace(',', '.', Ui::str('rating', '0')))), 1),
            'status' => isset(self::STATUS[Ui::str('status')]) ? Ui::str('status') : 'soon',
            'release' => Ui::str('release'),
            'director' => Ui::str('director'),
            'cast' => array_values(array_filter(array_map('trim', explode(',', Ui::str('cast'))), 'strlen')),
            'synopsis' => Ui::str('synopsis'),
            'trailer' => Ui::str('trailer'),
            'featured' => !empty($_POST['featured']),
            'poster' => $old['poster'] ?? '',
        ];
        $errors = [];
        if (mb_strlen($d['title']) < 1 || mb_strlen($d['title']) > 120) {
            $errors['title'] = 'Tên phim từ 1 đến 120 ký tự.';
        }
        if (!$d['genres']) {
            $errors['genres'] = 'Chọn ít nhất một thể loại.';
        }
        if (!validDate($d['release'])) {
            $errors['release'] = 'Ngày khởi chiếu không hợp lệ.';
        }
        if ($d['status'] === 'now' && $d['duration'] <= 0) {
            $errors['duration'] = 'Phim đang chiếu cần có thời lượng để xếp suất chiếu.';
        }
        if ($d['director'] === '') {
            $errors['director'] = 'Nhập tên đạo diễn.';
        }
        if ($d['trailer'] !== '' && !preg_match('#^https?://#i', $d['trailer'])) {
            $errors['trailer'] = 'Link trailer phải bắt đầu bằng http(s)://';
        }
        if (mb_strlen($d['synopsis']) < 10) {
            $errors['synopsis'] = 'Nhập nội dung phim (ít nhất 10 ký tự).';
        }

        $newId = $old['id'] ?? self::nextId();
        $upload = $_FILES['poster'] ?? null;
        if (!$errors && is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            [$path, $err] = self::storePoster($upload, $newId);
            if ($err) {
                $errors['poster'] = $err;
            } else {
                self::deleteUploadedPoster($d['poster']);
                $d['poster'] = $path;
            }
        }
        if ($errors) {
            $d['id'] = $old['id'] ?? '';
            flash('error', 'Vui lòng kiểm tra lại các trường được đánh dấu.');
            self::form($d, $errors);
            return;
        }

        $params = [$d['title'], implode(', ', $d['genres']), $d['duration'], $d['age'], $d['rating'], $d['status'], $d['release'],
            $d['director'], implode(', ', $d['cast']), $d['synopsis'], $d['poster'] ?: null, $d['trailer'] ?: null, (int)$d['featured']];
        if ($old) {
            Database::exec('UPDATE movies SET title = ?, genres = ?, duration = ?, age = ?, rating = ?, status = ?, release_date = ?,
                director = ?, cast_list = ?, synopsis = ?, poster_url = ?, trailer_url = ?, featured = ? WHERE id = ?', [...$params, $newId]);
            Ui::back('/admin/movies', 'Đã cập nhật phim "' . $d['title'] . '".');
        }
        Database::exec('INSERT INTO movies (title, genres, duration, age, rating, status, release_date, director, cast_list, synopsis,
            poster_url, trailer_url, featured, palette, id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [...$params, self::palette($d['title']), $newId]);
        Ui::back('/admin/movies', 'Đã thêm phim "' . $d['title'] . '" (mã ' . $newId . ').'
            . ($d['status'] === 'now' ? ' Vào "Suất chiếu" để xếp lịch chiếu cho phim.' : ''));
    }

    /** Đổi nhanh trạng thái từ danh sách. */
    public static function status(string $id): void
    {
        checkCsrf();
        $m = Movie::find($id) ?? notFound();
        $s = Ui::str('status');
        if (!isset(self::STATUS[$s])) {
            Ui::back('/admin/movies', 'Trạng thái không hợp lệ.', 'error');
        }
        if ($s === 'now' && $m['duration'] <= 0) {
            Ui::back('/admin/movies', 'Phim "' . $m['title'] . '" chưa có thời lượng – sửa phim trước khi chuyển sang đang chiếu.', 'error');
        }
        Database::exec('UPDATE movies SET status = ? WHERE id = ?', [$s, $id]);
        Ui::back('/admin/movies', 'Đã chuyển "' . $m['title'] . '" sang ' . mb_strtolower(self::STATUS[$s]) . '.');
    }

    /** Xoá phim chưa có vé; phim đã có vé chỉ được chuyển "Ngừng chiếu" để giữ lịch sử. */
    public static function delete(string $id): void
    {
        checkCsrf();
        $m = Movie::find($id) ?? notFound();
        $n = (int)Database::one('SELECT COUNT(*) n FROM bookings b JOIN showtimes s ON s.id = b.showtime_id WHERE s.movie_id = ?', [$id])['n'];
        if ($n > 0) {
            Ui::back('/admin/movies', 'Phim "' . $m['title'] . '" đã có ' . $n . ' đơn đặt vé nên không xoá được. Hãy chuyển sang "Ngừng chiếu".', 'error');
        }
        Database::exec('DELETE FROM showtimes WHERE movie_id = ?', [$id]);
        Database::exec('DELETE FROM movies WHERE id = ?', [$id]);
        self::deleteUploadedPoster($m['poster']);
        Ui::back('/admin/movies', 'Đã xoá phim "' . $m['title'] . '" và các suất chiếu của phim.');
    }

    // ---------- Hỗ trợ ----------
    private static function nextId(): string
    {
        $max = (int)Database::one("SELECT COALESCE(MAX(CAST(SUBSTRING(id, 2) AS UNSIGNED)), 0) n FROM movies WHERE id REGEXP '^m[0-9]+$'")['n'];
        return 'm' . str_pad((string)($max + 1), 2, '0', STR_PAD_LEFT);
    }

    /** Bảng màu cho poster SVG dự phòng, suy ra ổn định từ tên phim. */
    private static function palette(string $title): string
    {
        $h = crc32($title);
        $c = fn(int $shift) => sprintf('#%06x', ($h >> $shift) & 0x7f7f7f);
        return implode(',', [$c(0), $c(3), '#fbbf24']);
    }

    /** @return array{0:?string,1:?string} [đường dẫn web, lỗi] */
    private static function storePoster(array $file, string $movieId): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return [null, 'Tải poster không thành công (tối đa 3 MB).'];
        }
        if ($file['size'] > 3 * 1024 * 1024) {
            return [null, 'Poster tối đa 3 MB.'];
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $info = @getimagesize($file['tmp_name']);
        if (!isset(self::POSTER_TYPES[$mime]) || !$info || $info[0] < 100 || $info[1] < 140) {
            return [null, 'Poster phải là ảnh JPG, PNG hoặc WEBP, tối thiểu 100×140 px.'];
        }
        $name = $movieId . '-' . bin2hex(random_bytes(4)) . '.' . self::POSTER_TYPES[$mime];
        if (!move_uploaded_file($file['tmp_name'], BASE_PATH . '/public/uploads/movies/' . $name)) {
            return [null, 'Không lưu được poster.'];
        }
        return ['/uploads/movies/' . $name, null];
    }

    /** Chỉ xoá poster do admin tải lên (thư mục uploads), không đụng tới poster gốc trong assets. */
    private static function deleteUploadedPoster(?string $path): void
    {
        if ($path && preg_match('#^/uploads/movies/[\w.-]+$#', $path) && is_file(BASE_PATH . '/public' . $path)) {
            unlink(BASE_PATH . '/public' . $path);
        }
    }
}
