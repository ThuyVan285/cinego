<?php
declare(strict_types=1);

/**
 * Dữ liệu DEMO dùng để seed vào MySQL (xem database/seeders/DatabaseSeeder.php).
 * Phim thực tế đang và sắp chiếu tại thị trường Việt Nam – tháng 11/2026.
 * Lịch chiếu và tình trạng ghế là mô phỏng, không phải lịch chiếu chính thức của các rạp.
 * Nguồn xác minh: CGV.vn, Moveek.com, Baomoi.com, Tuoitre.vn, Kenh14.vn, Thanhnien.vn
 */

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
         'genres' => ['Hành động'], 'duration' => 135, 'age' => 'T18', 'rating' => 8.3,
         'status' => 'now', 'release' => '2026-09-25',
         'director' => 'Toni Dương Bảo Anh',
         'cast' => ['Steven Nguyễn', 'Huỳnh Minh Kiên', 'Quách Ngọc Ngoan', 'Tùng Mint'],
         'synopsis' => 'Chàng trai Ny từ miền Tây lặn lội lên đường tìm kiếm em gái mất tích và vô tình sa chân vào sào huyệt tội phạm buôn người xuyên biên giới. Bị bắt làm nô dịch, Ny cùng các nạn nhân khác phải tìm cách sinh tồn và thực hiện cuộc đào tẩu sinh tử để thoát khỏi khu tự trị của tập đoàn tội phạm.',
         'palette' => ['#1a0505', '#7f1d1d', '#fbbf24'],
         'poster' => '/assets/images/movies/m01.webp',
         'trailer' => 'https://www.youtube.com/watch?v=xGiimAKN0TM', 'featured' => true],

        // 2. Chị Chị Em Em 3 – Phim tâm lý cổ trang Việt Nam, ra rạp 24/10/2026
        // Nguồn: Kenh14, Dcine, Thanhnien, Moveek, CGV
        ['id' => 'm02', 'title' => 'Chị Chị Em Em 3',
         'genres' => ['Tâm lý', 'Tình cảm'], 'duration' => 122, 'age' => 'T18', 'rating' => 7.2,
         'status' => 'now', 'release' => '2026-10-30',
         'director' => 'Nguyễn Hữu Hoàng',
         'cast' => ['Ninh Dương Lan Ngọc', 'Kỳ Duyên', 'Chi Pu', 'Quốc Anh', 'Steven Nguyễn'],
         'synopsis' => 'Lấy bối cảnh vùng Kinh Bắc xưa với lễ giáo và gia phong khắt khe, câu chuyện xoay quanh mối quan hệ tình cảm chồng chéo giữa năm nhân vật: cuộc hôn nhân sắp đặt giữa thiếu gia Văn Tài và tiểu thư Thị Mầu bỗng dưng sóng gió khi tình cũ Thị Liễu trở về, đẩy tất cả vào vòng xoáy ghen tuông và trả thù.',
         'palette' => ['#1c1917', '#7c2d12', '#fde68a'],
         'poster' => '/assets/images/movies/m02.webp',
         'trailer' => 'https://www.youtube.com/watch?v=lM4mrAELuz0', 'featured' => false],

        // 3. Verity: Bí Mật Bị Chôn Vùi – Phim tâm lý Mỹ, ra rạp 16/10/2026
        // Nguồn: Wikipedia, CGV.vn, Moveek, Phimrap.vn
        ['id' => 'm03', 'title' => 'Verity: Bí Mật Bị Chôn Vùi',
         'genres' => ['Tâm lý', 'Kinh dị'], 'duration' => 117, 'age' => 'T18', 'rating' => 7.8,
         'status' => 'now', 'release' => '2026-10-16',
         'director' => 'Michael Showalter',
         'cast' => ['Anne Hathaway', 'Dakota Johnson', 'Josh Hartnett'],
         'synopsis' => 'Nhà văn đang khủng hoảng sự nghiệp Lowen Ashleigh được thuê đến dinh thự hẻo lánh của gia đình Crawford để hoàn thành loạt tiểu thuyết ăn khách. Tại đây, cô vô tình tìm thấy bản thảo tự truyện bí ẩn của Verity Crawford – những ghi chép ám ảnh hé lộ sự thật ghê rợn về gia đình này và người chồng Jeremy đầy sức hút.',
         'palette' => ['#1c1917', '#292524', '#a8a29e'],
         'poster' => '/assets/images/movies/m03.webp',
         'trailer' => 'https://www.youtube.com/watch?v=ngRz7p3elTk', 'featured' => true],

        // 4. Clayface – Phim DCU kinh dị, ra rạp 30/10/2026
        // Nguồn: Wikipedia, DC.com, Moveek, Nhiepanhdoisong
        ['id' => 'm04', 'title' => 'Clayface',
         'genres' => ['Kinh dị', 'Hành động'], 'duration' => 108, 'age' => 'T18', 'rating' => 7.5,
         'status' => 'now', 'release' => '2026-10-30',
         'director' => 'James Watkins',
         'cast' => ['Tom Rhys Harries', 'Naomi Ackie', 'David Dencik', 'Max Minghella'],
         'synopsis' => 'Nam diễn viên Matt Hagen đang trên đỉnh cao sự nghiệp tại Hollywood bỗng bị tấn công dã man bởi trùm tội phạm Gotham, khuôn mặt bị tàn phá hoàn toàn. Tuyệt vọng tìm lại diện mạo, anh chấp nhận liệu pháp thực nghiệm đầy rủi ro – và điều đó biến anh thành thực thể kinh hoàng có thể biến đổi hình dạng.',
         'palette' => ['#0f172a', '#1e3a5f', '#64748b'],
         'poster' => '/assets/images/movies/m04.webp',
         'trailer' => 'https://www.youtube.com/watch?v=IVCsoSn1GPE', 'featured' => false],

        // 5. Án Mạng Karaoke – Phim hài trinh thám Việt Nam, ra rạp 02/10/2026
        // Nguồn: Moveek, Vietnamplus, Baomoi, Tuoitre, CGV
        ['id' => 'm05', 'title' => 'Án Mạng Karaoke',
         'genres' => ['Hài', 'Trinh thám'], 'duration' => 96, 'age' => 'T16', 'rating' => 7.6,
         'status' => 'now', 'release' => '2026-10-26',
         'director' => 'Hoàng Lê',
         'cast' => ['NSƯT Vân Dung', 'Thanh Sơn', 'Đoàn Thiên Ân', 'NSƯT Đại Nghĩa', 'Tiểu Bảo Quốc'],
         'synopsis' => 'Một nhóm hàng xóm cùng nhau lên núi chữa lành, trong đó có bà Thủy – người có giọng hát karaoke bất kể ngày đêm khiến cả xóm ám ảnh. Đường về bị sạt lở cắt đứt, bà Thủy đột ngột bị sát hại với chiếc micro cắm vào cổ họng. Hung thủ phải nằm trong đoàn và trò chơi đấu trí đẫm máu bắt đầu.',
         'palette' => ['#14532d', '#15803d', '#fef08a'],
         'poster' => '/assets/images/movies/m05.webp',
         'trailer' => 'https://www.youtube.com/watch?v=oMArx7yq-Ro', 'featured' => false],

        // 6. Bò Sữa Bay – Phim hài hành động Việt Nam, ra rạp 06/11/2026
        // Nguồn: CGV.vn, Baomoi, Voh.com.vn, Cafef
        ['id' => 'm06', 'title' => 'Bò Sữa Bay',
         'genres' => ['Hài', 'Hành động'], 'duration' => 108, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Nguyễn Phạm Thành Đạt',
         'cast' => ['Nguyễn Hùng', 'Ngọc Xuân', 'Tiểu Vy', 'Võ Điền Gia Huy', 'Lãnh Thanh'],
         'synopsis' => 'Thập niên 1990, tại nông trường bò sữa Mộc Châu đang nghiên cứu công thức sữa thần kỳ, chàng trai nghèo Trâu nhận lời đột nhập đánh cắp bí mật. Để thực hiện kế hoạch, anh tiếp cận nhà nghiên cứu San – nhưng rồi lại thật sự yêu cô. Tình yêu, gia đình và lòng tham đẩy Trâu vào thế tiến thoái lưỡng nan đầy tiếng cười.',
         'palette' => ['#14532d', '#166534', '#fde047'],
         'poster' => '/assets/images/movies/m06.webp',
         'trailer' => null, 'featured' => true],

        // 7. Chàng Mèo Mang Mũ – Phim hoạt hình Warner Bros, ra rạp 06/11/2026
        // Nguồn: Elle.vn, CGV.vn, Phunuvietnam
        ['id' => 'm07', 'title' => 'Chàng Mèo Mang Mũ',
         'genres' => ['Hoạt hình', 'Gia đình'], 'duration' => 107, 'age' => 'P', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Warner Bros. Animation',
         'cast' => ['Lồng tiếng: Bill Hader'],
         'synopsis' => 'Chú mèo tinh quái đội chiếc mũ sọc đỏ-trắng đặc trưng bất ngờ xuất hiện và biến ngôi nhà bình thường của hai đứa trẻ thành thế giới phiêu lưu kỳ diệu đầy trò nghịch ngợm. Chuyển thể từ tác phẩm kinh điển của Dr. Seuss, bộ phim hoạt hình vui nhộn và sáng tạo dành cho cả gia đình.',
         'palette' => ['#dc2626', '#b91c1c', '#fde047'],
         'poster' => '/assets/images/movies/m07.webp',
         'trailer' => 'https://www.youtube.com/watch?v=CJxagBUiIto', 'featured' => false],

        // 8. Ngự Miêu Phá Án – Phim hoạt hình Trung Quốc, ra rạp 10/10/2026
        // Nguồn: Thegioidienanh, Cinestar, Saobiz, Moveek
        ['id' => 'm08', 'title' => 'Ngự Miêu Phá Án',
         'genres' => ['Hoạt hình', 'Trinh thám'], 'duration' => 98, 'age' => 'K', 'rating' => 7.9,
         'status' => 'now', 'release' => '2026-10-28',
         'director' => 'Cao Lương',
         'cast' => ['Lồng tiếng: Phát La', 'Lồng tiếng: Thanh Thủy'],
         'synopsis' => 'Tại thành cổ Kim Lăng đầy màu sắc cổ phong, chú mèo Triển Ngao – cựu cận vệ hoàng gia không có đuôi – bất đắc dĩ hợp tác với thiếu niên Bao Chửng để phá giải hàng loạt kỳ án bí ẩn: người dân đột nhiên biến thành động vật, Thái tử mất tích bí ẩn. Phim trinh thám võ thuật hài hước dành cho gia đình.',
         'palette' => ['#7c3aed', '#c026d3', '#fcd34d'],
         'poster' => '/assets/images/movies/m08.webp',
         'trailer' => null, 'featured' => false],

        // 9. Đơn Hàng Ma Ám – Phim kinh dị Indonesia, ra rạp 06/11/2026
        // Nguồn: CGV.vn, Momo.vn, Youtube
        ['id' => 'm09', 'title' => 'Đơn Hàng Ma Ám',
         'genres' => ['Kinh dị'], 'duration' => 0, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-12-11',
         'director' => 'Dinna Jasanti',
         'cast' => ['Fatih Unru', 'Yasamin Jasem', 'Gabriella Ekaputri', 'Fadly Faisal'],
         'synopsis' => 'Nhóm sinh viên bất ngờ nhận được những kiện hàng bí ẩn không rõ nguồn gốc. Khi họ lần lượt mở từng món đồ, những cái chết kỳ dị và kinh hoàng bắt đầu xảy ra: thi thể bị đàn ong vây kín, tai nạn bất thường và sự mất kiểm soát tâm trí. Mỗi lần mở đơn hàng cũng là một bước gần hơn đến cái chết.',
         'palette' => ['#022c22', '#134e4a', '#6ee7b7'],
         'poster' => '/assets/images/movies/m09.webp',
         'trailer' => 'https://www.youtube.com/watch?v=7ewLUjo4ZMk', 'featured' => false],

        // 10. Godzilla Trừ Không – Phim hành động Nhật Bản, ra rạp 06/11/2026
        // Nguồn: Momo.vn, Cellphones, Vnexpress, Tuoitre
        ['id' => 'm10', 'title' => 'Godzilla Trừ Không',
         'genres' => ['Hành động', 'Khoa học viễn tưởng'], 'duration' => 135, 'age' => 'T16', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Takashi Yamazaki',
         'cast' => ['Ryusei Yokohama', 'Minami Hamabe', 'Yuki Yamada'],
         'synopsis' => 'Tiếp nối thành công lừng lẫy của Godzilla Minus One từng đoạt Oscar, phần mới đưa khán giả vào cuộc đối đầu sinh tử mới giữa nhân loại và quái thú khổng lồ huyền thoại trong bối cảnh hậu chiến đầy bi tráng và cảm xúc. Đạo diễn Takashi Yamazaki tiếp tục chinh phục màn ảnh rộng bằng kỹ xảo thị giác đỉnh cao.',
         'palette' => ['#1e1b4b', '#3730a3', '#60a5fa'],
         'poster' => '/assets/images/movies/m10.webp',
         'trailer' => 'https://www.youtube.com/watch?v=-HhqsE1gK38', 'featured' => false],

        // ===== PHIM SẮP CHIẾU (soon) =====

        // 11. Ngày Con Còn Mẹ – Phim gia đình Việt Nam, ra rạp 11/11/2026
        // Nguồn: Tuoitre, Bazaarvietnam, Baomoi, Suckhoedoisong, Thanhnien
        ['id' => 'm11', 'title' => 'Ngày Con Còn Mẹ',
         'genres' => ['Gia đình', 'Tâm lý'], 'duration' => 113, 'age' => 'T16', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-11',
         'director' => 'Vũ Ngọc Đãng',
         'cast' => ['Hồng Đào', 'Phương Anh Đào', 'Huy Anh', 'Nguyên Thảo'],
         'synopsis' => 'Cô con gái Mây từ nhỏ luôn tủi thân vì cảm giác mẹ thiên vị anh trai Hai Pháo. Những hiểu lầm và khoảng cách tích tụ năm tháng đẩy hai mẹ con đến ranh giới rạn nứt. Chỉ khi người mẹ đổ bệnh nặng, đứng trước lằn ranh sinh tử, hai người mới bắt đầu thấu hiểu nhau qua những giọt nước mắt và tình thương chưa bao giờ phai.',
         'palette' => ['#78350f', '#b45309', '#fde68a'],
         'poster' => '/assets/images/movies/m11.webp',
         'trailer' => 'https://www.youtube.com/watch?v=gihuep9BNSg', 'featured' => false],

        // 12. Đấu Trường Sinh Tử: Bình Minh Ngày Chiêu Quân – Bom tấn Hollywood, ra rạp 20/11/2026
        // Nguồn: Kenh14, CGV.vn, Tienphong, Sortiraparis, Businessinsider
        ['id' => 'm12', 'title' => 'Đấu Trường Sinh Tử: Bình Minh Ngày Chiêu Quân',
         'genres' => ['Hành động', 'Phiêu lưu'], 'duration' => 158, 'age' => 'T16', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-20',
         'director' => 'Francis Lawrence',
         'cast' => ['Joseph Zada', 'Mckenna Grace', 'Ralph Fiennes', 'Elle Fanning', 'Kieran Culkin'],
         'synopsis' => 'Lùi về 24 năm trước sự kiện của Katniss Everdeen, tại Kỳ Đấu Trường Sinh Tử lần thứ 50 đặc biệt khắc nghiệt, mỗi Quận phải cống nạp 4 vật tế. Câu chuyện xoay quanh Haymitch Abernathy 16 tuổi khi anh bước vào kỳ đấu trường đẫm máu nhất lịch sử và cách anh sống sót để trở thành người đàn ông mà chúng ta từng biết.',
         'palette' => ['#431407', '#b45309', '#fcd34d'],
         'poster' => '/assets/images/movies/m12.webp',
         'trailer' => 'https://www.youtube.com/watch?v=xRqWx_hQEI8', 'featured' => true],

        // 13. Mẹ Mìn – Phim kinh dị Việt Nam, ra rạp 27/11/2026
        // Nguồn: Tuoitre, Yeah1, CGV.vn, Moveek, Cinestar, Download.com.vn
        ['id' => 'm13', 'title' => 'Mẹ Mìn: Giao Kèo Với Quỷ',
         'genres' => ['Kinh dị', 'Tâm lý'], 'duration' => 0, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-27',
         'director' => 'Jack Carry On',
         'cast' => ['Minh Hằng', 'Nhật Kim Anh', 'Rima Thanh Vy', 'Khương Lê', 'Đại Nghĩa'],
         'synopsis' => 'Lấy cảm hứng từ hình tượng dân gian Việt Nam, Mẹ Mìn là người phụ nữ bí ẩn có khả năng biến mọi ước nguyện thành hiện thực. Nhưng không có điều ước nào là miễn phí – đằng sau mỗi mong muốn được đáp ứng luôn ẩn giấu một cái giá tăm tối, khiến những người tìm đến cô phải đối mặt với cơn ác mộng không lối thoát.',
         'palette' => ['#1c1917', '#44403c', '#ef4444'],
         'poster' => '/assets/images/movies/m13.webp',
         'trailer' => 'https://www.youtube.com/watch?v=4rTkZ9HYSA8', 'featured' => false],

        // 14. Gặp Gỡ Thông Gia: Dâu Mới Trình Làng – Phim hài Hollywood, ra rạp 27/11/2026
        // Nguồn: Moveek, CGV.vn, Aeonmall
        ['id' => 'm14', 'title' => 'Gặp Gỡ Thông Gia: Dâu Mới Trình Làng',
         'genres' => ['Hài', 'Tình cảm'], 'duration' => 0, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-27',
         'director' => 'Jay Chandrasekhar',
         'cast' => ['Robert De Niro', 'Zac Efron', 'Aubrey Plaza', 'Nicole Kidman'],
         'synopsis' => 'Hai gia đình thông gia lần đầu gặp mặt tại kỳ nghỉ lễ, và những khác biệt về văn hóa, thế hệ và lối sống nhanh chóng tạo nên những tình huống dở khóc dở cười không hồi kết. Một bộ phim hài gia đình ấm áp và đầy tiếng cười dành cho dịp cuối năm.',
         'palette' => ['#1e3a5f', '#2563eb', '#fde047'],
         'poster' => '/assets/images/movies/m14.webp',
         'trailer' => 'https://www.youtube.com/watch?v=A6V3Sj8X6Sk', 'featured' => false],

        // ===== Bổ sung 14/11/2026 – 20 phim lấy thông tin từ CGV.vn (trang phim đang chiếu / sắp chiếu) =====
        // Tóm tắt nội dung được viết lại; poster tải từ CGV (chỉ dùng cho mục đích demo/đồ án).
        ['id' => 'm15', 'title' => 'Ramayana: Khởi Nguồn Một Huyền Thoại',
         'genres' => ['Hành động', 'Phiêu lưu', 'Tâm lý', 'Thần thoại'], 'duration' => 168, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Nitesh Tiwari',
         'cast' => ['Ranbir Kapoor', 'Yash', 'Sai Pallavi', 'Ravie Dubey', 'Sunny Deol'],
         'synopsis' => 'Phần mở đầu chuyển thể sử thi Ấn Độ về hoàng tử Rama và quỷ vương Ravana – hai số phận bị ràng buộc bởi cùng một lời tiên tri. Một người đại diện cho sự hy sinh và chính nghĩa, người kia bị lòng báo thù thiêu đốt, mở ra cuộc đối đầu mang tầm thần thoại.',
         'palette' => ['#1c1206', '#92400e', '#fbbf24'],
         'poster' => '/assets/images/movies/m15.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=s3zEnSZVG1I', 'featured' => false],

        ['id' => 'm16', 'title' => 'Ebenezer: Phép Màu Đêm Giáng Sinh',
         'genres' => ['Thần thoại'], 'duration' => 116, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-13',
         'director' => 'Ti West',
         'cast' => ['Johnny Depp', 'Rupert Grint', 'Andrea Riseborough', 'Sam Claflin', 'Ian McKellen'],
         'synopsis' => 'Một phiên bản mới, u tối hơn của câu chuyện Giáng sinh kinh điển với Johnny Depp trong vai lão keo kiệt Ebenezer Scrooge. Trong một đêm Giáng sinh, những hồn ma lần lượt ghé thăm buộc ông nhìn lại quá khứ, hiện tại và tương lai của chính mình.',
         'palette' => ['#0b1d1a', '#14532d', '#fca5a5'],
         'poster' => '/assets/images/movies/m16.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=9i_w6YojcMY', 'featured' => false],

        ['id' => 'm17', 'title' => 'Dune: Hành Tinh Cát – Phần Một (Chiếu Lại)',
         'genres' => ['Hành động', 'Khoa học viễn tưởng'], 'duration' => 165, 'age' => 'T13', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-13',
         'director' => 'Denis Villeneuve',
         'cast' => ['Timothée Chalamet', 'Rebecca Ferguson', 'Zendaya'],
         'synopsis' => 'Chàng trai trẻ Paul Atreides theo gia tộc đến Arrakis – hành tinh sa mạc khắc nghiệt nhất vũ trụ và là nơi duy nhất có loại gia vị quý giá. Khi các thế lực lao vào tranh giành nguồn tài nguyên này, Paul phải chiến đấu để bảo vệ gia đình và đón nhận vận mệnh lớn lao của mình.',
         'palette' => ['#1c1408', '#a16207', '#fde68a'],
         'poster' => '/assets/images/movies/m17.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=R797Sf-2zt0', 'featured' => false],

        ['id' => 'm18', 'title' => 'Kẻ Hồi Sinh',
         'genres' => ['Hành động'], 'duration' => 101, 'age' => 'T16', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-13',
         'director' => 'Baek Jong-yeol',
         'cast' => ['Koo Kyo-hwan', 'Shin Seung-ho', 'Kang Ki-young'],
         'synopsis' => 'Chuyển thể từ webtoon hành động nổi tiếng, phim theo chân một người đàn ông có thể sống lại, và mỗi lần hồi sinh mọi thứ được thiết lập lại sau 72 giờ. Anh tận dụng vòng lặp ấy để lần lượt dẹp bỏ những kẻ cản đường trong các trận đánh nghẹt thở.',
         'palette' => ['#0f0f12', '#3f3f46', '#ef4444'],
         'poster' => '/assets/images/movies/m18.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=t33Uj0sXiSA', 'featured' => false],

        ['id' => 'm19', 'title' => 'Bí Kíp Luyện Cướp',
         'genres' => ['Hài'], 'duration' => 122, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-13',
         'director' => 'David Leitch',
         'cast' => ['Nicholas Hoult', 'Pete Davidson', 'Zoë Kravitz', 'Anna Sawai', 'John C. Reilly'],
         'synopsis' => 'Một băng cướp ngân hàng quay lại mọi phi vụ rồi phát trực tiếp lên mạng xã hội, không ngờ độ nổi tiếng khiến một đặc vụ FBI kỳ cựu và một kỹ sư phần mềm tài năng để mắt tới. Khi vòng vây ngày càng siết chặt, cả băng quyết định liều lĩnh hơn bao giờ hết trong phi vụ lớn nhất.',
         'palette' => ['#111827', '#1d4ed8', '#facc15'],
         'poster' => '/assets/images/movies/m19.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=r-NuTOD-PxA', 'featured' => false],

        ['id' => 'm20', 'title' => 'Thám Tử Siêu Mông: Ngôi Sao Và Mặt Trăng',
         'genres' => ['Hoạt hình', 'Trinh thám'], 'duration' => 77, 'age' => 'P', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-13',
         'director' => 'Shibata Hiroki',
         'cast' => ['Sanpei Yuko', 'Saito Ayaka', 'Sakurai Takahiro', 'Koyasu Takehito'],
         'synopsis' => 'Thám tử Siêu Mông đi tìm một người thân mất tích và tình cờ thắng cuộc thi thần tượng trên hòn đảo bí ẩn – nơi đặt trụ sở của một tổ chức tội phạm. Anh buộc phải hợp tác với đối thủ cũ để ngăn chặn một kế hoạch đen tối đang được triển khai.',
         'palette' => ['#3b0764', '#a855f7', '#fde047'],
         'poster' => '/assets/images/movies/m20.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=VQHhGUTvVcc', 'featured' => false],

        ['id' => 'm21', 'title' => 'Thòng Lọng Ma: Quỷ Cái Đòi Mạng',
         'genres' => ['Kinh dị'], 'duration' => 105, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-13',
         'director' => 'Liao Shih-han',
         'cast' => ['Lee Kang-sheng', 'Andovi Da Lopez', 'Wilson Hsu'],
         'synopsis' => 'Một buổi trừ tà bị cấm được phát trực tiếp tại Indonesia vô tình đánh thức Kuntilanak, nữ quỷ báo thù trong truyền thuyết dân gian. Lời nguyền lan sang tận Đài Loan, buộc con trai của nạn nhân đầu tiên mang tro cốt mẹ về Indonesia để tìm ra nguồn gốc cơn ác mộng.',
         'palette' => ['#0a0a0a', '#450a0a', '#d4d4d8'],
         'poster' => '/assets/images/movies/m21.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=f3cerkvG0-A', 'featured' => false],

        ['id' => 'm22', 'title' => 'Khi Thần Chết Đổ Chuông',
         'genres' => ['Kinh dị'], 'duration' => 86, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Jay Sukmo',
         'cast' => ['Bhisma Mulia', 'Ratu Sofya', 'Givina', 'Shaloom Razade', 'Mathias Muchus'],
         'synopsis' => 'Một người đàn ông trở về quê sau nhiều năm xa cách để tìm hiểu cái chết bí ẩn của người ông là pháp sư. Anh phát hiện một linh hồn không đầu đang gieo rắc kinh hoàng, trong khi chiếc chuông thiêng dùng để phong ấn nó đang bị đe dọa.',
         'palette' => ['#0c0a09', '#57534e', '#f59e0b'],
         'poster' => '/assets/images/movies/m22.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=eJZOJVGaXKQ', 'featured' => false],

        ['id' => 'm23', 'title' => 'Thiên Đường Tăm Tối',
         'genres' => ['Hồi hộp', 'Tâm lý'], 'duration' => 100, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-11-06',
         'director' => 'Yeon Sang-ho',
         'cast' => ['Kim Hyun-joo', 'Bae Hyeon-seong'],
         'synopsis' => 'Một người mẹ mất con đột ngột tìm được chút an ủi khi gặp lại hình ảnh con trai qua kính thực tế ảo. Nhiều năm sau người con trở về nhưng mọi thứ không còn như cũ, buộc bà phải lựa chọn giữa thực tại và một thế giới được dựng lên từ ký ức.',
         'palette' => ['#0f172a', '#334155', '#38bdf8'],
         'poster' => '/assets/images/movies/m23.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=Ut4iV_ttQB4', 'featured' => false],

        ['id' => 'm24', 'title' => 'Quỷ Ăn Tạng 4: Hổ Tinh',
         'genres' => ['Hài', 'Hành động', 'Kinh dị'], 'duration' => 111, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-10-16',
         'director' => 'Chalit Krileadmongkon, Thanadet Pradit, Narit Yuvaboon',
         'cast' => ['Nadech Kugimiya', 'Bow Maylada', 'Ongart Cheamcharoenpornkul', 'Bible Wichapas'],
         'synopsis' => 'Hai con người xa lạ bị cuốn vào cuộc truy tìm một người đàn ông mất tích trong khu rừng hiểm trở. Họ phải đối mặt với hổ tinh ăn thịt người có khả năng biến hóa khôn lường và buộc phải hợp sức để sống sót.',
         'palette' => ['#14120b', '#713f12', '#f97316'],
         'poster' => '/assets/images/movies/m24.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=0t29-MGiths', 'featured' => false],

        ['id' => 'm25', 'title' => 'Shaun The Sheep: "Quái Lạ" Ghé Nhà',
         'genres' => ['Gia đình', 'Hài', 'Hoạt hình', 'Phiêu lưu'], 'duration' => 81, 'age' => 'P', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-10-16',
         'director' => 'Steve Cox, Matthew Walker',
         'cast' => ['Justin Fletcher', 'John Sparkes', 'Nina Sosanya'],
         'synopsis' => 'Chú cừu Shaun nghịch ngợm vô tình phá hỏng vườn bí ngô cả đàn chuẩn bị cho lễ Halloween. Muốn chuộc lỗi, Shaun thử làm nhà khoa học nhưng thí nghiệm lại kéo theo hàng loạt rắc rối. Khi Ông chủ mất tích và một sinh vật lạ xuất hiện trong rừng, cả đàn lên đường đi tìm.',
         'palette' => ['#14532d', '#65a30d', '#fb923c'],
         'poster' => '/assets/images/movies/m25.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=8q6XZq8a0iA', 'featured' => false],

        ['id' => 'm26', 'title' => 'Oán Linh Đòi Mạng',
         'genres' => ['Bí ẩn', 'Kinh dị', 'Tâm lý'], 'duration' => 98, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-10-23',
         'director' => 'Awi Suryadi',
         'cast' => ['Titi Kamal', 'Andri Mashadi', 'Anantya Kirana'],
         'synopsis' => 'Một gia đình chuyển đến khu dân cư cao cấp với hy vọng về cuộc sống tốt đẹp hơn, nhưng những hiện tượng rùng rợn bắt đầu xuất hiện. Khi hàng xóm lần lượt bỏ đi, cả nhà phải tìm cách sinh tồn trước những bí mật đen tối của nơi này.',
         'palette' => ['#1c1917', '#3f1d1d', '#a8a29e'],
         'poster' => '/assets/images/movies/m26.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=ycdsf8OuqY0', 'featured' => false],

        ['id' => 'm27', 'title' => 'Ngọn Đồi Hoa Hồng Anh',
         'genres' => ['Hoạt hình', 'Tình cảm'], 'duration' => 91, 'age' => 'K', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-10-16',
         'director' => 'Goro Miyazaki',
         'cast' => ['Masami Nagasawa', 'Junichi Okada', 'Keiko Takeshita', 'Yuriko Ishida', 'Rumi Hiiragi'],
         'synopsis' => 'Umi, cô chị cả trong một gia đình ba anh em, ngày ngày treo cờ tưởng nhớ người cha đã khuất. Cô cùng một bạn học tham gia phong trào bảo tồn tòa nhà câu lạc bộ cũ của trường, cho đến khi một phát hiện bất ngờ khiến cả hai bối rối về mối quan hệ của mình.',
         'palette' => ['#0c4a6e', '#38bdf8', '#fda4af'],
         'poster' => '/assets/images/movies/m27.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=0BhMyVlrxdc', 'featured' => false],

        ['id' => 'm28', 'title' => 'Street Fighter',
         'genres' => ['Hành động'], 'duration' => 119, 'age' => 'T16', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-10-16',
         'director' => 'Kitao Sakurai',
         'cast' => ['Noah Centineo', 'Andrew Koji', 'Callina Liang', 'Joe "Roman Reigns" Anoai', 'David Dastmalchian'],
         'synopsis' => 'Năm 1993, hai đấu sĩ từng xa cách bị kéo trở lại võ đài khi được mời dự một giải đấu khốc liệt. Đằng sau giải đấu là một âm mưu nguy hiểm, buộc họ phải đối mặt với nhau và với quá khứ của chính mình.',
         'palette' => ['#172554', '#dc2626', '#facc15'],
         'poster' => '/assets/images/movies/m28.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=xwnZ_uvrFs0', 'featured' => false],

        ['id' => 'm29', 'title' => 'Vùng Đất Quỷ Dữ',
         'genres' => ['Hành động', 'Kinh dị'], 'duration' => 94, 'age' => 'T18', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-09-18',
         'director' => 'Zach Cregger',
         'cast' => ['Austin Abrams', 'Paul Walter Hauser', 'Zach Cherry', 'Kali Reis'],
         'synopsis' => 'Bryan, một nhân viên vận chuyển vật tư y tế, bất ngờ bị cuốn vào một đêm sinh tồn căng thẳng khi hàng loạt sự kiện kinh hoàng nổ ra quanh anh. Một câu chuyện hoàn toàn mới trong thế giới Resident Evil.',
         'palette' => ['#052e16', '#14532d', '#dc2626'],
         'poster' => '/assets/images/movies/m29.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=GWWJrOYzJZY', 'featured' => false],

        ['id' => 'm30', 'title' => 'Mộ Đom Đóm (Chiếu Lại)',
         'genres' => ['Hoạt hình', 'Tâm lý'], 'duration' => 89, 'age' => 'K', 'rating' => 0.0,
         'status' => 'now', 'release' => '2026-10-30',
         'director' => 'Takahata Isao',
         'cast' => ['Tatsumi Tsutomu', 'Shiraishi Ayano', 'Shinohara Yoshiko'],
         'synopsis' => 'Giữa những năm cuối Thế chiến II, hai anh em mất hết gia đình phải nương tựa vào nhau để sinh tồn. Dù thế giới quanh mình đổ vỡ, họ vẫn cố giữ những khoảnh khắc hồn nhiên cuối cùng – một câu chuyện buồn đẹp về tuổi thơ giữa thời loạn.',
         'palette' => ['#1c1917', '#78350f', '#fde68a'],
         'poster' => '/assets/images/movies/m30.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=xGW5msw7pw8', 'featured' => false],

        ['id' => 'm31', 'title' => 'Kiến Lé Phiêu Lưu Ké',
         'genres' => ['Hài', 'Hoạt hình'], 'duration' => 77, 'age' => 'P', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-20',
         'director' => 'Sean Heuston',
         'cast' => [],
         'synopsis' => 'Khi hạn hán đe dọa cả cộng đồng côn trùng, hai chú kiến vụng về Arnie và Barney quyết tự mình đi tìm nguồn nước. Bắt tay với một siêu anh hùng muỗi giấu mặt, cặp đôi lóng ngóng trải qua hàng loạt tình huống dở khóc dở cười và trở thành người hùng bất đắc dĩ.',
         'palette' => ['#422006', '#ca8a04', '#86efac'],
         'poster' => '/assets/images/movies/m31.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=wQvd1_T5Bkk', 'featured' => false],

        ['id' => 'm32', 'title' => 'Trại Giam Hạnh Phúc',
         'genres' => ['Gia đình', 'Hài', 'Tâm lý'], 'duration' => 127, 'age' => 'T16', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-20',
         'director' => 'Trung Lùn',
         'cast' => ['Tuấn Trần', 'Ma Ran Đô', 'Lương Thế Thành', 'Sally Hồng Anh', 'Khuyến Dương'],
         'synopsis' => 'Thầy giáo hiền lành Phúc phải vào tù sau một biến cố khi cố bảo vệ em gái. Giữa môi trường khắc nghiệt sau song sắt, sự tử tế của anh dần cảm hóa viên quản giáo nguyên tắc và các bạn tù, mang đến cả tiếng cười lẫn nước mắt.',
         'palette' => ['#1e293b', '#475569', '#fb923c'],
         'poster' => '/assets/images/movies/m32.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=4ijqx0q6aLY', 'featured' => false],

        ['id' => 'm33', 'title' => 'Dune: Hành Tinh Cát – Phần Hai (Chiếu Lại)',
         'genres' => ['Hành động', 'Khoa học viễn tưởng'], 'duration' => 177, 'age' => 'T16', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-11-18',
         'director' => 'Denis Villeneuve',
         'cast' => ['Timothée Chalamet', 'Zendaya', 'Rebecca Ferguson', 'Josh Brolin', 'Austin Butler'],
         'synopsis' => 'Paul Atreides cùng Chani và người Fremen bước vào hành trình đòi lại công bằng cho gia tộc mình. Anh phải lựa chọn giữa tình yêu và số phận của cả vũ trụ, đồng thời tìm cách ngăn chặn một tương lai đen tối mà chỉ mình anh nhìn thấy.',
         'palette' => ['#1c1408', '#9a3412', '#fdba74'],
         'poster' => '/assets/images/movies/m33.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=kCO-RO3q7U4', 'featured' => false],

        ['id' => 'm34', 'title' => 'Avengers: Ngày Tận Thế',
         'genres' => ['Hành động', 'Khoa học viễn tưởng', 'Phiêu lưu'], 'duration' => 0, 'age' => '', 'rating' => 0.0,
         'status' => 'soon', 'release' => '2026-12-18',
         'director' => 'Anthony Russo, Joe Russo',
         'cast' => ['Robert Downey Jr.', 'Chris Evans', 'Chris Hemsworth', 'Pedro Pascal', 'Florence Pugh'],
         'synopsis' => 'Những người hùng đến từ nhiều vũ trụ khác nhau bị cuốn vào một cuộc va chạm sinh tử và buộc phải sát cánh bên nhau trước một hiểm họa đe dọa sự tồn vong của đa vũ trụ.',
         'palette' => ['#0c0a09', '#166534', '#a3e635'],
         'poster' => '/assets/images/movies/m34.jpg',
         'trailer' => 'https://www.youtube.com/watch?v=jZMjK1Y1Fv0', 'featured' => true],
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

/** Tài khoản demo, dùng chung cho DatabaseSeeder.php và migration tạo bảng users. */
function seedUsers(): array
{
    return [
        ['name' => 'Quản trị viên CineGo', 'email' => 'admin@cinego.vn',     'phone' => '0901234567', 'password' => 'admin123', 'role' => 'admin'],
        ['name' => 'Nguyễn Văn A',         'email' => 'khachhang@gmail.com', 'phone' => '0912345678', 'password' => 'user123',  'role' => 'user'],
        ['name' => 'Trần Thị Nhân Viên',   'email' => 'nhanvien@cinego.vn',  'phone' => '0905123456', 'password' => 'staff123', 'role' => 'staff'],
    ];
}
