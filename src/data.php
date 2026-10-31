<?php
declare(strict_types=1);

/**
 * Dữ liệu mẫu (DEMO) dùng để seed vào MySQL (xem public/install.php) – toàn bộ phim, rạp, combo, ưu đãi đều là hư cấu.
 * Không phải lịch chiếu hay tình trạng ghế thực tế.
 */

function genres(): array
{
    return ['Hành động', 'Kinh dị', 'Hoạt hình', 'Tình cảm', 'Hài', 'Khoa học viễn tưởng'];
}

function movies(): array
{
    static $m = null;
    if ($m !== null) {
        return $m;
    }
    $t = today();
    $m = [
        ['id' => 'm01', 'title' => 'Hỏa Tốc Truy Kích', 'genres' => ['Hành động'], 'duration' => 128, 'age' => 'T16', 'rating' => 8.1, 'status' => 'now', 'release' => addDays($t, -20),
         'director' => 'Trần Quang Huy', 'cast' => ['Lê Minh Khang', 'Phạm Thảo Vy', 'Đỗ Hoàng Long'],
         'synopsis' => 'Một cựu tay đua bị cuốn vào cuộc truy đuổi xuyên thành phố trong đúng một đêm. Để cứu em gái, anh phải vượt qua những đường đua nguy hiểm nhất và đối đầu với băng nhóm đứng sau.',
         'palette' => ['#1e1b4b', '#be123c', '#fbbf24'], 'trailer' => 'https://www.youtube.com/results?search_query=action+movie+trailer', 'featured' => true],
        ['id' => 'm02', 'title' => 'Căn Nhà Số 13', 'genres' => ['Kinh dị'], 'duration' => 104, 'age' => 'T18', 'rating' => 7.4, 'status' => 'now', 'release' => addDays($t, -12),
         'director' => 'Nguyễn Hải Yến', 'cast' => ['Vũ Ngọc Anh', 'Trịnh Đức Thịnh', 'Bùi Mai Chi'],
         'synopsis' => 'Gia đình nhỏ chuyển đến căn nhà cũ giá rẻ và dần nhận ra mỗi đêm, tiếng gõ cửa lại vang lên từ căn phòng chưa từng được mở.',
         'palette' => ['#0f172a', '#334155', '#94a3b8'], 'trailer' => null, 'featured' => false],
        ['id' => 'm03', 'title' => 'Biệt Đội Sao Biển', 'genres' => ['Hoạt hình', 'Hài'], 'duration' => 96, 'age' => 'P', 'rating' => 8.6, 'status' => 'now', 'release' => addDays($t, -30),
         'director' => 'Hoàng Thanh Mai', 'cast' => ['Lồng tiếng: Quốc Bảo', 'Lồng tiếng: Thu Hà', 'Lồng tiếng: Anh Tú'],
         'synopsis' => 'Năm chú sao biển nhỏ lập biệt đội bảo vệ rạn san hô khỏi đám rác khổng lồ trôi dạt từ phía đất liền. Một cuộc phiêu lưu đầy tiếng cười dành cho cả gia đình.',
         'palette' => ['#0369a1', '#06b6d4', '#fde68a'], 'trailer' => 'https://www.youtube.com/results?search_query=animation+family+trailer', 'featured' => true],
        ['id' => 'm04', 'title' => 'Mùa Thu Ở Hà Nội', 'genres' => ['Tình cảm'], 'duration' => 112, 'age' => 'T13', 'rating' => 7.9, 'status' => 'now', 'release' => addDays($t, -8),
         'director' => 'Lương Bảo Châu', 'cast' => ['Hà Gia Hân', 'Nguyễn Tuấn Kiệt'],
         'synopsis' => 'Hai người xa lạ gặp nhau trong một chiều thu bên hồ Tây và cùng nhau đi qua những góc phố cũ, những quyết định lớn của cuộc đời.',
         'palette' => ['#7c2d12', '#ea580c', '#fed7aa'], 'trailer' => null, 'featured' => false],
        ['id' => 'm05', 'title' => 'Ông Bố Lắm Chiêu', 'genres' => ['Hài'], 'duration' => 101, 'age' => 'T13', 'rating' => 7.2, 'status' => 'now', 'release' => addDays($t, -15),
         'director' => 'Phan Đình Nam', 'cast' => ['Chí Trung Nghĩa', 'Kim Ngọc Lan', 'Bé Tôm'],
         'synopsis' => 'Ông bố thất nghiệp quyết tâm giả làm siêu anh hùng để con gái nghĩ bố mình vẫn là người giỏi nhất, và rồi mọi chuyện rối tung lên.',
         'palette' => ['#14532d', '#16a34a', '#fde047'], 'trailer' => null, 'featured' => false],
        ['id' => 'm06', 'title' => 'Hành Tinh Cuối Cùng', 'genres' => ['Khoa học viễn tưởng', 'Hành động'], 'duration' => 142, 'age' => 'T13', 'rating' => 8.8, 'status' => 'now', 'release' => addDays($t, -5),
         'director' => 'Đặng Quốc Thịnh', 'cast' => ['Trần Bảo Ngọc', 'Lý Hoàng Phúc', 'Mai Khánh Linh'],
         'synopsis' => 'Năm 2187, phi hành đoàn cuối cùng của nhân loại tìm thấy một hành tinh có thể sống được, nhưng nơi đó dường như đã có người đến trước.',
         'palette' => ['#1e1b4b', '#4f46e5', '#22d3ee'], 'trailer' => 'https://www.youtube.com/results?search_query=sci-fi+movie+trailer', 'featured' => true],
        ['id' => 'm07', 'title' => 'Mật Danh Sương Mù', 'genres' => ['Hành động'], 'duration' => 119, 'age' => 'T16', 'rating' => 7.7, 'status' => 'now', 'release' => addDays($t, -10),
         'director' => 'Võ Thanh Sơn', 'cast' => ['Ngô Bảo Châu', 'Đinh Quang Vinh'],
         'synopsis' => 'Một nữ điệp viên bị tổ chức của chính mình truy lùng sau nhiệm vụ thất bại. Cô chỉ có 48 giờ để chứng minh sự trong sạch.',
         'palette' => ['#18181b', '#52525b', '#f43f5e'], 'trailer' => null, 'featured' => false],
        ['id' => 'm08', 'title' => 'Tiếng Gọi Từ Giếng Cổ', 'genres' => ['Kinh dị'], 'duration' => 98, 'age' => 'T18', 'rating' => 7.1, 'status' => 'now', 'release' => addDays($t, -3),
         'director' => 'Cao Thị Hồng Nhung', 'cast' => ['Phùng Thu Trang', 'Đoàn Minh Triết'],
         'synopsis' => 'Nhóm sinh viên về làng làm đề tài văn hóa và vô tình đánh thức điều đã bị phong ấn dưới chiếc giếng cổ suốt trăm năm.',
         'palette' => ['#022c22', '#115e59', '#a7f3d0'], 'trailer' => null, 'featured' => false],
        ['id' => 'm09', 'title' => 'Cánh Diều Và Con Rồng', 'genres' => ['Hoạt hình'], 'duration' => 92, 'age' => 'P', 'rating' => 8.3, 'status' => 'now', 'release' => addDays($t, -25),
         'director' => 'Hoàng Thanh Mai', 'cast' => ['Lồng tiếng: Bảo Nam', 'Lồng tiếng: Diệu Linh'],
         'synopsis' => 'Cậu bé làng chài thả cánh diều lên trời và đánh thức một chú rồng con. Hai người bạn cùng nhau tìm đường về nhà cho rồng.',
         'palette' => ['#6d28d9', '#c026d3', '#fcd34d'], 'trailer' => null, 'featured' => false],
        ['id' => 'm10', 'title' => 'Nhật Ký Mùa Hè', 'genres' => ['Tình cảm', 'Hài'], 'duration' => 108, 'age' => 'T13', 'rating' => 7.6, 'status' => 'now', 'release' => addDays($t, -14),
         'director' => 'Lương Bảo Châu', 'cast' => ['Hà Gia Hân', 'Đỗ Hoàng Long', 'Mai Khánh Linh'],
         'synopsis' => 'Kỳ nghỉ hè cuối cấp của nhóm bạn thân ở Đà Nẵng với những cuộc phiêu lưu, những lời thú nhận muộn màng và một bí mật nhỏ.',
         'palette' => ['#0e7490', '#f472b6', '#fef08a'], 'trailer' => null, 'featured' => false],
        ['id' => 'm11', 'title' => 'Vùng Đất Hậu Tận Thế', 'genres' => ['Khoa học viễn tưởng'], 'duration' => 135, 'age' => 'T16', 'rating' => 0.0, 'status' => 'soon', 'release' => addDays($t, 9),
         'director' => 'Đặng Quốc Thịnh', 'cast' => ['Lý Hoàng Phúc', 'Ngô Bảo Châu'],
         'synopsis' => 'Hai mươi năm sau thảm họa, một nhóm người sống sót quyết định băng qua vùng đất hoang để tìm nguồn nước cuối cùng.',
         'palette' => ['#431407', '#b45309', '#fcd34d'], 'trailer' => null, 'featured' => false],
        ['id' => 'm12', 'title' => 'Thám Tử Mèo Béo', 'genres' => ['Hoạt hình', 'Hài'], 'duration' => 94, 'age' => 'P', 'rating' => 0.0, 'status' => 'soon', 'release' => addDays($t, 12),
         'director' => 'Phạm Thị Ngân', 'cast' => ['Lồng tiếng: Quốc Bảo', 'Lồng tiếng: Thu Hà'],
         'synopsis' => 'Chú mèo béo ú nhất phố bỗng trở thành thám tử khi cả khu chợ mất sạch cá khô chỉ trong một đêm.',
         'palette' => ['#9a3412', '#fb923c', '#fff7ed'], 'trailer' => null, 'featured' => false],
        ['id' => 'm13', 'title' => 'Lời Nguyền Chiếc Gương', 'genres' => ['Kinh dị'], 'duration' => 101, 'age' => 'T18', 'rating' => 0.0, 'status' => 'soon', 'release' => addDays($t, 16),
         'director' => 'Nguyễn Hải Yến', 'cast' => ['Vũ Ngọc Anh', 'Bùi Mai Chi'],
         'synopsis' => 'Chiếc gương cổ mua ở chợ đồ cũ phản chiếu một căn phòng không có thật. Càng nhìn lâu, người trong gương càng giống cô hơn.',
         'palette' => ['#1c1917', '#7f1d1d', '#fca5a5'], 'trailer' => null, 'featured' => false],
        ['id' => 'm14', 'title' => 'Đêm Cuối Sài Gòn', 'genres' => ['Hành động', 'Tình cảm'], 'duration' => 123, 'age' => 'T16', 'rating' => 0.0, 'status' => 'soon', 'release' => addDays($t, 20),
         'director' => 'Trần Quang Huy', 'cast' => ['Phạm Thảo Vy', 'Lê Minh Khang'],
         'synopsis' => 'Một đêm duy nhất, hai con người đối nghịch cùng chạy trốn khắp thành phố và nhận ra họ có thể đã yêu nhau từ lâu.',
         'palette' => ['#312e81', '#db2777', '#fde68a'], 'trailer' => null, 'featured' => false],
    ];
    return $m;
}

function brands(): array
{
    return [
        'cgv' => ['name' => 'CGV', 'color' => '#e11d48'],
        'lotte' => ['name' => 'Lotte Cinema', 'color' => '#dc2626'],
        'galaxy' => ['name' => 'Galaxy Cinema', 'color' => '#f97316'],
        'bhd' => ['name' => 'BHD Star', 'color' => '#2563eb'],
        'beta' => ['name' => 'Beta Cinemas', 'color' => '#0891b2'],
        'cinestar' => ['name' => 'Cinestar', 'color' => '#7c3aed'],
        'premium' => ['name' => 'CineGo Premium', 'color' => '#a21caf'],
        'dancing' => ['name' => 'Rạp Cộng Đồng', 'color' => '#059669'],
    ];
}

function cities(): array
{
    return ['Đà Nẵng'];
}

function cinemas(): array
{
    static $c = null;
    if ($c !== null) {
        return $c;
    }
    $rows = [
        ['cgv-q1', 'cgv', 'CGV Vincom Đà Nẵng', 'Đà Nẵng', 'Sơn Trà', '910A Ngô Quyền, Sơn Trà', 5],
        ['cgv-cg', 'cgv', 'CGV Vĩnh Trung Plaza', 'Đà Nẵng', 'Thanh Khê', '255–257 Hùng Vương, Thanh Khê', 6],
        ['lotte-q7', 'lotte', 'Lotte Cinema Đà Nẵng', 'Đà Nẵng', 'Hải Châu', 'Tầng 5 Lotte Mart, 6 Nại Nam, Hải Châu', 6],
        ['lotte-hk', 'lotte', 'Lotte Cinema Sơn Trà', 'Đà Nẵng', 'Sơn Trà', '128 Ngô Quyền, Sơn Trà', 4],
        ['galaxy-tb', 'galaxy', 'Galaxy Nguyễn Tri Phương', 'Đà Nẵng', 'Thanh Khê', '86 Nguyễn Tri Phương, Thanh Khê', 5],
        ['galaxy-td', 'galaxy', 'Galaxy Liên Chiểu', 'Đà Nẵng', 'Liên Chiểu', '12 Nguyễn Sinh Sắc, Liên Chiểu', 4],
        ['bhd-q3', 'bhd', 'BHD Star Helio', 'Đà Nẵng', 'Hải Châu', 'Đường 2 Tháng 9, Hải Châu', 5],
        ['bhd-hd', 'bhd', 'BHD Star Ngũ Hành Sơn', 'Đà Nẵng', 'Ngũ Hành Sơn', '120 Trần Đại Nghĩa, Ngũ Hành Sơn', 4],
        ['beta-cg', 'beta', 'Beta Thanh Khê', 'Đà Nẵng', 'Thanh Khê', '210 Điện Biên Phủ, Thanh Khê', 3],
        ['cinestar-hc', 'cinestar', 'Cinestar Hải Châu', 'Đà Nẵng', 'Hải Châu', '55 Bạch Đằng, Hải Châu', 4],
        ['premium-q1', 'premium', 'CineGo Premium Mỹ Khê', 'Đà Nẵng', 'Sơn Trà', '35 Võ Nguyên Giáp, Sơn Trà', 3],
        ['dancing-nk', 'dancing', 'Rạp Cộng Đồng Cẩm Lệ', 'Đà Nẵng', 'Cẩm Lệ', '15 Ông Ích Đường, Cẩm Lệ', 3],
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
        ['id' => 'popcorn', 'name' => 'Bắp rang bơ', 'desc' => 'Bắp rang bơ size vừa, giòn và thơm.', 'price' => 45000],
        ['id' => 'drink', 'name' => 'Nước ngọt', 'desc' => 'Ly nước ngọt 22oz, tuỳ chọn vị.', 'price' => 35000],
        ['id' => 'combo1', 'name' => 'Combo 1 người', 'desc' => '1 bắp rang bơ + 1 nước ngọt.', 'price' => 69000],
        ['id' => 'combo2', 'name' => 'Combo 2 người', 'desc' => '1 bắp lớn + 2 nước ngọt.', 'price' => 99000],
    ];
}

/**
 * Quy tắc mã ưu đãi (DEMO).
 * type: pct (giảm %, tối đa max) | fixed (giảm số tiền cố định). min: tổng tạm tính tối thiểu.
 */
function promos(): array
{
    return [
        ['code' => 'CINEGO10', 'title' => 'Giảm 10% cho đơn từ 150.000đ', 'desc' => 'Giảm 10% tổng đơn, tối đa 40.000đ.', 'cond' => 'Đơn tối thiểu 150.000đ. Mỗi đơn dùng một mã.', 'expires' => addDays(today(), 30), 'type' => 'pct', 'value' => 10, 'max' => 40000, 'min' => 150000, 'tag' => '-10%', 'colors' => ['#4f46e5', '#c026d3']],
        ['code' => 'WELCOME30', 'title' => 'Chào bạn mới – giảm 30.000đ', 'desc' => 'Giảm thẳng 30.000đ cho lượt đặt vé đầu tiên trên CineGo.', 'cond' => 'Đơn tối thiểu 120.000đ. Chỉ áp dụng ghế thường và VIP.', 'expires' => addDays(today(), 60), 'type' => 'fixed', 'value' => 30000, 'max' => 30000, 'min' => 120000, 'tag' => '-30K', 'colors' => ['#0f766e', '#22d3ee']],
        ['code' => 'BAPNUOC20', 'title' => 'Mua combo giảm 20.000đ', 'desc' => 'Giảm 20.000đ khi đơn có ít nhất một combo bắp nước.', 'cond' => 'Đơn tối thiểu 200.000đ và có combo bắp nước.', 'expires' => addDays(today(), 14), 'type' => 'fixed', 'value' => 20000, 'max' => 20000, 'min' => 200000, 'tag' => '-20K', 'colors' => ['#b45309', '#f59e0b'], 'needCombo' => true],
    ];
}
