<?php
declare(strict_types=1);

/**
 * Dữ liệu DEMO dùng để seed vào MySQL (xem public/install.php).
 * Phim thực tế đang và sắp chiếu tại thị trường Việt Nam – tháng 11/2026.
 * Lịch chiếu và tình trạng ghế là mô phỏng, không phải lịch chiếu chính thức của các rạp.
 * Nguồn xác minh: CGV.vn, Moveek.com, Baomoi.com, Tuoitre.vn, Kenh14.vn, Thanhnien.vn
 */

function genres(): array
{
    return ['Hành động', 'Kinh dị', 'Hoạt hình', 'Tình cảm', 'Hài', 'Khoa học viễn tưởng', 'Tâm lý', 'Trinh thám', 'Gia đình', 'Phiêu lưu'];
}

function movies(): array
{
    static $m = null;
    if ($m !== null) {
        return $m;
    }
    $m = [

        // ===== PHIM ĐANG CHIẾU (now) =====

        // 1. Trại Buôn Người – Phim hành động Việt Nam, ra rạp 25/9/2026
        // Nguồn: Wikipedia, Thanhnien, Laodong, Galaxycine, Moveek
        ['id' => 'm01', 'title' => 'Trại Buôn Người',
         'genres' => ['Hành động'], 'duration' => 120, 'age' => 'T18', 'rating' => 8.3,
         'status' => 'now', 'release' => '2026-09-25',
         'director' => 'Toni Dương Bảo Anh',
         'cast' => ['Steven Nguyễn', 'Huỳnh Minh Kiên', 'Quách Ngọc Ngoan', 'Tùng Mint'],
         'synopsis' => 'Chàng trai Ny từ miền Tây lặn lội lên đường tìm kiếm em gái mất tích và vô tình sa chân vào sào huyệt tội phạm buôn người xuyên biên giới. Bị bắt làm nô dịch, Ny cùng các nạn nhân khác phải tìm cách sinh tồn và thực hiện cuộc đào tẩu sinh tử để thoát khỏi khu tự trị của tập đoàn tội phạm.',
         'palette' => ['#1a0505', '#7f1d1d', '#fbbf24'],
         'poster' => '/assets/images/posters/m01.webp',
         'trailer' => null, 'featured' => true],

        // 2. Chị Chị Em Em 3 – Phim tâm lý cổ trang Việt Nam, ra rạp 24/10/2026
        // Nguồn: Kenh14, Dcine, Thanhnien, Moveek, CGV
        ['id' => 'm02', 'title' => 'Chị Chị Em Em 3',
         'genres' => ['Tâm lý', 'Tình cảm'], 'duration' => 118, 'age' => 'T18', 'rating' => 7.2,
         'status' => 'now', 'release' => '2026-10-24',
         'director' => 'Nguyễn Hữu Hoàng',
         'cast' => ['Ninh Dương Lan Ngọc', 'Kỳ Duyên', 'Chi Pu', 'Quốc Anh', 'Steven Nguyễn'],
         'synopsis' => 'Lấy bối cảnh vùng Kinh Bắc xưa với lễ giáo và gia phong khắt khe, câu chuyện xoay quanh mối quan hệ tình cảm chồng chéo giữa năm nhân vật: cuộc hôn nhân sắp đặt giữa thiếu gia Văn Tài và tiểu thư Thị Mầu bỗng dưng sóng gió khi tình cũ Thị Liễu trở về, đẩy tất cả vào vòng xoáy ghen tuông và trả thù.',
         'palette' => ['#1c1917', '#7c2d12', '#fde68a'],
         'poster' => '/assets/images/posters/m02.webp',
         'trailer' => null, 'featured' => false],

        // 3. Verity: Bí Mật Bị Chôn Vùi – Phim tâm lý Mỹ, ra rạp 16/10/2026
        // Nguồn: Wikipedia, CGV.vn, Moveek, Phimrap.vn
        ['id' => 'm03', 'title' => 'Verity: Bí Mật Bị Chôn Vùi',
         'genres' => ['Tâm lý', 'Kinh dị'], 'duration' => 117, 'age' => 'T18', 'rating' => 7.8,
         'status' => 'now', 'release' => '2026-10-16',
         'director' => 'Michael Showalter',
         'cast' => ['Anne Hathaway', 'Dakota Johnson', 'Josh Hartnett'],
         'synopsis' => 'Nhà văn đang khủng hoảng sự nghiệp Lowen Ashleigh được thuê đến dinh thự hẻo lánh của gia đình Crawford để hoàn thành loạt tiểu thuyết ăn khách. Tại đây, cô vô tình tìm thấy bản thảo tự truyện bí ẩn của Verity Crawford – những ghi chép ám ảnh hé lộ sự thật ghê rợn về gia đình này và người chồng Jeremy đầy sức hút.',
         'palette' => ['#1c1917', '#292524', '#a8a29e'],
         'poster' => '/assets/images/posters/m03.webp',
         'trailer' => null, 'featured' => true],

        // 4. Clayface – Phim DCU kinh dị, ra rạp 30/10/2026
        // Nguồn: Wikipedia, DC.com, Moveek, Nhiepanhdoisong
        ['id' => 'm04', 'title' => 'Clayface',
         'genres' => ['Kinh dị', 'Hành động'], 'duration' => 108, 'age' => 'T18', 'rating' => 7.5,
         'status' => 'now', 'release' => '2026-10-30',
         'director' => 'James Watkins',
         'cast' => ['Tom Rhys Harries', 'Naomi Ackie', 'David Dencik', 'Max Minghella'],
         'synopsis' => 'Nam diễn viên Matt Hagen đang trên đỉnh cao sự nghiệp tại Hollywood bỗng bị tấn công dã man bởi trùm tội phạm Gotham, khuôn mặt bị tàn phá hoàn toàn. Tuyệt vọng tìm lại diện mạo, anh chấp nhận liệu pháp thực nghiệm đầy rủi ro – và điều đó biến anh thành thực thể kinh hoàng có thể biến đổi hình dạng.',
         'palette' => ['#0f172a', '#1e3a5f', '#64748b'],
         'poster' => '/assets/images/posters/m04.webp',
         'trailer' => null, 'featured' => false],

        // 5. Án Mạng Karaoke – Phim hài trinh thám Việt Nam, ra rạp 02/10/2026
        // Nguồn: Moveek, Vietnamplus, Baomoi, Tuoitre, CGV
        ['id' => 'm05', 'title' => 'Án Mạng Karaoke',
         'genres' => ['Hài', 'Trinh thám'], 'duration' => 105, 'age' => 'T13', 'rating' => 7.6,
         'status' => 'now', 'release' => '2026-10-02',
         'director' => 'Hoàng Lê',
         'cast' => ['NSƯT Vân Dung', 'Thanh Sơn', 'Đoàn Thiên Ân', 'NSƯT Đại Nghĩa', 'Tiểu Bảo Quốc'],
         'synopsis' => 'Một nhóm hàng xóm cùng nhau lên núi chữa lành, trong đó có bà Thủy – người có giọng hát karaoke bất kể ngày đêm khiến cả xóm ám ảnh. Đường về bị sạt lở cắt đứt, bà Thủy đột ngột bị sát hại với chiếc micro cắm vào cổ họng. Hung thủ phải nằm trong đoàn và trò chơi đấu trí đẫm máu bắt đầu.',
         'palette' => ['#14532d', '#15803d', '#fef08a'],
         'poster' => '/assets/images/posters/m05.webp',
         'trailer' => null, 'featured' => false],

        // 6. Bò Sữa Bay – Phim hài hành động Việt Nam, ra rạp 06/11/2026
        // Nguồn: CGV.vn, Baomoi, Voh.com.vn, Cafef
        ['id' => 'm06', 'title' => 'Bò Sữa Bay',
         'genres' => ['Hài', 'Hành động'], 'duration' => 110, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Nguyễn Phạm Thành Đạt',
         'cast' => ['Nguyễn Hùng', 'Ngọc Xuân', 'Tiểu Vy', 'Võ Điền Gia Huy', 'Lãnh Thanh'],
         'synopsis' => 'Thập niên 1990, tại nông trường bò sữa Mộc Châu đang nghiên cứu công thức sữa thần kỳ, chàng trai nghèo Trâu nhận lời đột nhập đánh cắp bí mật. Để thực hiện kế hoạch, anh tiếp cận nhà nghiên cứu San – nhưng rồi lại thật sự yêu cô. Tình yêu, gia đình và lòng tham đẩy Trâu vào thế tiến thoái lưỡng nan đầy tiếng cười.',
         'palette' => ['#14532d', '#166534', '#fde047'],
         'poster' => '/assets/images/posters/m06.webp',
         'trailer' => null, 'featured' => true],

        // 7. Chàng Mèo Mang Mũ – Phim hoạt hình Warner Bros, ra rạp 06/11/2026
        // Nguồn: Elle.vn, CGV.vn, Phunuvietnam
        ['id' => 'm07', 'title' => 'Chàng Mèo Mang Mũ',
         'genres' => ['Hoạt hình', 'Gia đình'], 'duration' => 95, 'age' => 'P', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Warner Bros. Animation',
         'cast' => ['Lồng tiếng: Bill Hader'],
         'synopsis' => 'Chú mèo tinh quái đội chiếc mũ sọc đỏ-trắng đặc trưng bất ngờ xuất hiện và biến ngôi nhà bình thường của hai đứa trẻ thành thế giới phiêu lưu kỳ diệu đầy trò nghịch ngợm. Chuyển thể từ tác phẩm kinh điển của Dr. Seuss, bộ phim hoạt hình vui nhộn và sáng tạo dành cho cả gia đình.',
         'palette' => ['#dc2626', '#b91c1c', '#fde047'],
         'poster' => '/assets/images/posters/m07.webp',
         'trailer' => null, 'featured' => false],

        // 8. Ngự Miêu Phá Án – Phim hoạt hình Trung Quốc, ra rạp 10/10/2026
        // Nguồn: Thegioidienanh, Cinestar, Saobiz, Moveek
        ['id' => 'm08', 'title' => 'Ngự Miêu Phá Án',
         'genres' => ['Hoạt hình', 'Trinh thám'], 'duration' => 102, 'age' => 'P', 'rating' => 7.9,
         'status' => 'now', 'release' => '2026-10-10',
         'director' => 'Cao Lương',
         'cast' => ['Lồng tiếng: Phát La', 'Lồng tiếng: Thanh Thủy'],
         'synopsis' => 'Tại thành cổ Kim Lăng đầy màu sắc cổ phong, chú mèo Triển Ngao – cựu cận vệ hoàng gia không có đuôi – bất đắc dĩ hợp tác với thiếu niên Bao Chửng để phá giải hàng loạt kỳ án bí ẩn: người dân đột nhiên biến thành động vật, Thái tử mất tích bí ẩn. Phim trinh thám võ thuật hài hước dành cho gia đình.',
         'palette' => ['#7c3aed', '#c026d3', '#fcd34d'],
         'poster' => '/assets/images/posters/m08.webp',
         'trailer' => null, 'featured' => false],

        // 9. Đơn Hàng Ma Ám – Phim kinh dị Indonesia, ra rạp 06/11/2026
        // Nguồn: CGV.vn, Momo.vn, Youtube
        ['id' => 'm09', 'title' => 'Đơn Hàng Ma Ám',
         'genres' => ['Kinh dị'], 'duration' => 98, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Dinna Jasanti',
         'cast' => ['Fatih Unru', 'Yasamin Jasem', 'Gabriella Ekaputri', 'Fadly Faisal'],
         'synopsis' => 'Nhóm sinh viên bất ngờ nhận được những kiện hàng bí ẩn không rõ nguồn gốc. Khi họ lần lượt mở từng món đồ, những cái chết kỳ dị và kinh hoàng bắt đầu xảy ra: thi thể bị đàn ong vây kín, tai nạn bất thường và sự mất kiểm soát tâm trí. Mỗi lần mở đơn hàng cũng là một bước gần hơn đến cái chết.',
         'palette' => ['#022c22', '#134e4a', '#6ee7b7'],
         'poster' => '/assets/images/posters/m09.webp',
         'trailer' => null, 'featured' => false],

        // 10. Godzilla Trừ Không – Phim hành động Nhật Bản, ra rạp 06/11/2026
        // Nguồn: Momo.vn, Cellphones, Vnexpress, Tuoitre
        ['id' => 'm10', 'title' => 'Godzilla Trừ Không',
         'genres' => ['Hành động', 'Khoa học viễn tưởng'], 'duration' => 135, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Takashi Yamazaki',
         'cast' => ['Ryusei Yokohama', 'Minami Hamabe', 'Yuki Yamada'],
         'synopsis' => 'Tiếp nối thành công lừng lẫy của Godzilla Minus One từng đoạt Oscar, phần mới đưa khán giả vào cuộc đối đầu sinh tử mới giữa nhân loại và quái thú khổng lồ huyền thoại trong bối cảnh hậu chiến đầy bi tráng và cảm xúc. Đạo diễn Takashi Yamazaki tiếp tục chinh phục màn ảnh rộng bằng kỹ xảo thị giác đỉnh cao.',
         'palette' => ['#1e1b4b', '#3730a3', '#60a5fa'],
         'poster' => '/assets/images/posters/m10.webp',
         'trailer' => null, 'featured' => false],

        // ===== PHIM SẮP CHIẾU (soon) =====

        // 11. Ngày Con Còn Mẹ – Phim gia đình Việt Nam, ra rạp 11/11/2026
        // Nguồn: Tuoitre, Bazaarvietnam, Baomoi, Suckhoedoisong, Thanhnien
        ['id' => 'm11', 'title' => 'Ngày Con Còn Mẹ',
         'genres' => ['Gia đình', 'Tâm lý'], 'duration' => 115, 'age' => 'P', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-11',
         'director' => 'Vũ Ngọc Đãng',
         'cast' => ['Hồng Đào', 'Phương Anh Đào', 'Huy Anh', 'Nguyên Thảo'],
         'synopsis' => 'Cô con gái Mây từ nhỏ luôn tủi thân vì cảm giác mẹ thiên vị anh trai Hai Pháo. Những hiểu lầm và khoảng cách tích tụ năm tháng đẩy hai mẹ con đến ranh giới rạn nứt. Chỉ khi người mẹ đổ bệnh nặng, đứng trước lằn ranh sinh tử, hai người mới bắt đầu thấu hiểu nhau qua những giọt nước mắt và tình thương chưa bao giờ phai.',
         'palette' => ['#78350f', '#b45309', '#fde68a'],
         'poster' => '/assets/images/posters/m11.webp',
         'trailer' => null, 'featured' => false],

        // 12. Đấu Trường Sinh Tử: Bình Minh Ngày Chiêu Quân – Bom tấn Hollywood, ra rạp 20/11/2026
        // Nguồn: Kenh14, CGV.vn, Tienphong, Sortiraparis, Businessinsider
        ['id' => 'm12', 'title' => 'Đấu Trường Sinh Tử: Bình Minh Ngày Chiêu Quân',
         'genres' => ['Hành động', 'Phiêu lưu'], 'duration' => 0, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-20',
         'director' => 'Francis Lawrence',
         'cast' => ['Joseph Zada', 'Mckenna Grace', 'Ralph Fiennes', 'Elle Fanning', 'Kieran Culkin'],
         'synopsis' => 'Lùi về 24 năm trước sự kiện của Katniss Everdeen, tại Kỳ Đấu Trường Sinh Tử lần thứ 50 đặc biệt khắc nghiệt, mỗi Quận phải cống nạp 4 vật tế. Câu chuyện xoay quanh Haymitch Abernathy 16 tuổi khi anh bước vào kỳ đấu trường đẫm máu nhất lịch sử và cách anh sống sót để trở thành người đàn ông mà chúng ta từng biết.',
         'palette' => ['#431407', '#b45309', '#fcd34d'],
         'poster' => '/assets/images/posters/m12.webp',
         'trailer' => null, 'featured' => true],

        // 13. Mẹ Mìn – Phim kinh dị Việt Nam, ra rạp 27/11/2026
        // Nguồn: Tuoitre, Yeah1, CGV.vn, Moveek, Cinestar, Download.com.vn
        ['id' => 'm13', 'title' => 'Mẹ Mìn',
         'genres' => ['Kinh dị', 'Tâm lý'], 'duration' => 0, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-27',
         'director' => 'Jack Carry On',
         'cast' => ['Minh Hằng', 'Nhật Kim Anh', 'Rima Thanh Vy', 'Khương Lê', 'Đại Nghĩa'],
         'synopsis' => 'Lấy cảm hứng từ hình tượng dân gian Việt Nam, Mẹ Mìn là người phụ nữ bí ẩn có khả năng biến mọi ước nguyện thành hiện thực. Nhưng không có điều ước nào là miễn phí – đằng sau mỗi mong muốn được đáp ứng luôn ẩn giấu một cái giá tăm tối, khiến những người tìm đến cô phải đối mặt với cơn ác mộng không lối thoát.',
         'palette' => ['#1c1917', '#44403c', '#ef4444'],
         'poster' => '/assets/images/posters/m13.webp',
         'trailer' => null, 'featured' => false],

        // 14. Gặp Gỡ Thông Gia: Dâu Mới Trình Làng – Phim hài Hollywood, ra rạp 27/11/2026
        // Nguồn: Moveek, CGV.vn, Aeonmall
        ['id' => 'm14', 'title' => 'Gặp Gỡ Thông Gia: Dâu Mới Trình Làng',
         'genres' => ['Hài', 'Tình cảm'], 'duration' => 0, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-27',
         'director' => 'Jay Chandrasekhar',
         'cast' => ['Robert De Niro', 'Zac Efron', 'Aubrey Plaza', 'Nicole Kidman'],
         'synopsis' => 'Hai gia đình thông gia lần đầu gặp mặt tại kỳ nghỉ lễ, và những khác biệt về văn hóa, thế hệ và lối sống nhanh chóng tạo nên những tình huống dở khóc dở cười không hồi kết. Một bộ phim hài gia đình ấm áp và đầy tiếng cười dành cho dịp cuối năm.',
         'palette' => ['#1e3a5f', '#2563eb', '#fde047'],
         'poster' => '/assets/images/posters/m14.webp',
         'trailer' => null, 'featured' => false],
    ];
    return $m;
}

function brands(): array
{
    return [
        'cgv'      => ['name' => 'CGV', 'color' => '#e11d48'],
        'lotte'    => ['name' => 'Lotte Cinema', 'color' => '#dc2626'],
        'galaxy'   => ['name' => 'Galaxy Cinema', 'color' => '#f97316'],
        'bhd'      => ['name' => 'BHD Star', 'color' => '#2563eb'],
        'beta'     => ['name' => 'Beta Cinemas', 'color' => '#0891b2'],
        'cinestar' => ['name' => 'Cinestar', 'color' => '#7c3aed'],
        'premium'  => ['name' => 'CineGo Premium', 'color' => '#a21caf'],
        'dancing'  => ['name' => 'Rap Cong Dong', 'color' => '#059669'],
    ];
}

function cities(): array
{
    return ['Da Nang'];
}

function cinemas(): array
{
    static $c = null;
    if ($c !== null) {
        return $c;
    }
    $rows = [
        ['cgv-q1',      'cgv',      'CGV Vincom Da Nang',          'Da Nang', 'Son Tra',      '910A Ngo Quyen, Son Tra',                        5],
        ['cgv-cg',      'cgv',      'CGV Vinh Trung Plaza',        'Da Nang', 'Thanh Khe',    '255-257 Hung Vuong, Thanh Khe',                  6],
        ['lotte-q7',    'lotte',    'Lotte Cinema Da Nang',        'Da Nang', 'Hai Chau',     'Tang 5 Lotte Mart, 6 Nai Nam, Hai Chau',         6],
        ['lotte-hk',    'lotte',    'Lotte Cinema Son Tra',        'Da Nang', 'Son Tra',      '128 Ngo Quyen, Son Tra',                         4],
        ['galaxy-tb',   'galaxy',   'Galaxy Nguyen Tri Phuong',    'Da Nang', 'Thanh Khe',    '86 Nguyen Tri Phuong, Thanh Khe',                5],
        ['galaxy-td',   'galaxy',   'Galaxy Lien Chieu',           'Da Nang', 'Lien Chieu',   '12 Nguyen Sinh Sac, Lien Chieu',                 4],
        ['bhd-q3',      'bhd',      'BHD Star Helio',              'Da Nang', 'Hai Chau',     'Duong 2 Thang 9, Hai Chau',                      5],
        ['bhd-hd',      'bhd',      'BHD Star Ngu Hanh Son',       'Da Nang', 'Ngu Hanh Son', '120 Tran Dai Nghia, Ngu Hanh Son',               4],
        ['beta-cg',     'beta',     'Beta Thanh Khe',              'Da Nang', 'Thanh Khe',    '210 Dien Bien Phu, Thanh Khe',                   3],
        ['cinestar-hc', 'cinestar', 'Cinestar Hai Chau',           'Da Nang', 'Hai Chau',     '55 Bach Dang, Hai Chau',                         4],
        ['premium-q1',  'premium',  'CineGo Premium My Khe',       'Da Nang', 'Son Tra',      '35 Vo Nguyen Giap, Son Tra',                     3],
        ['dancing-nk',  'dancing',  'Rap Cong Dong Cam Le',        'Da Nang', 'Cam Le',       '15 Ong Ich Duong, Cam Le',                       3],
    ];
    $c = [];
    foreach ($rows as [$id, $brand, $name, $city, $area, $addr, $rooms]) {
        $c[] = compact('id', 'brand', 'name', 'city', 'area', 'addr', 'rooms');
    }
    return $c;
}

function combos(): array
{
    return [
        ['id' => 'popcorn', 'name' => 'Bap rang bo',   'desc' => 'Bap rang bo size vua, gion va thom.',   'price' => 45000],
        ['id' => 'drink',   'name' => 'Nuoc ngot',      'desc' => 'Ly nuoc ngot 22oz, tuy chon vi.',       'price' => 35000],
        ['id' => 'combo1',  'name' => 'Combo 1 nguoi',  'desc' => '1 bap rang bo + 1 nuoc ngot.',          'price' => 69000],
        ['id' => 'combo2',  'name' => 'Combo 2 nguoi',  'desc' => '1 bap lon + 2 nuoc ngot.',              'price' => 99000],
    ];
}

/**
 * Quy tac ma uu dai (DEMO).
 * type: pct (giam %, toi da max) | fixed (giam so tien co dinh). min: tong tam tinh toi thieu.
 */
function promos(): array
{
    return [
        ['code' => 'CINEGO10',  'title' => 'Giam 10% cho don tu 150.000d',    'desc' => 'Giam 10% tong don, toi da 40.000d.',                            'cond' => 'Don toi thieu 150.000d. Moi don dung mot ma.',           'expires' => addDays(today(), 30), 'type' => 'pct',   'value' => 10,    'max' => 40000, 'min' => 150000, 'tag' => '-10%', 'colors' => ['#4f46e5', '#c026d3']],
        ['code' => 'WELCOME30', 'title' => 'Chao ban moi - giam 30.000d',     'desc' => 'Giam thang 30.000d cho luot dat ve dau tien tren CineGo.',      'cond' => 'Don toi thieu 120.000d. Chi ap dung ghe thuong va VIP.', 'expires' => addDays(today(), 60), 'type' => 'fixed', 'value' => 30000, 'max' => 30000, 'min' => 120000, 'tag' => '-30K', 'colors' => ['#0f766e', '#22d3ee']],
        ['code' => 'BAPNUOC20', 'title' => 'Mua combo giam 20.000d',          'desc' => 'Giam 20.000d khi don co it nhat mot combo bap nuoc.',           'cond' => 'Don toi thieu 200.000d va co combo bap nuoc.',           'expires' => addDays(today(), 14), 'type' => 'fixed', 'value' => 20000, 'max' => 20000, 'min' => 200000, 'tag' => '-20K', 'colors' => ['#b45309', '#f59e0b'], 'needCombo' => true],
    ];
}