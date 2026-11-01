<?php
declare(strict_types=1);

/**
 * CineGo Đà Nẵng – Giao diện đặt vé phong cách "Vé Giấy" (Paper Ticket)
 * Thực hiện trong một file PHP duy nhất: Logic, Dữ liệu, CSS Variables, Typography & Giao diện.
 */

// Dữ liệu 5 phim tiêu biểu với gradient riêng biệt, không dùng ảnh ngoài
$featuredMovie = [
    'id' => 'm01',
    'title' => 'Trại Buôn Người',
    'subtitle' => 'The Journey of Heroes',
    'genres' => ['Hành động', 'Kịch tính'],
    'duration' => 120,
    'age' => 'T18',
    'rating' => 8.3,
    'release' => '25/09/2026',
    'director' => 'Toni Dương Bảo Anh',
    'cast' => 'Steven Nguyễn, Huỳnh Minh Kiên, Quách Ngọc Ngoan',
    'cinema' => 'CGV Vincom Đà Nẵng',
    'room' => 'Screen 03 (Starium)',
    'time' => '19:45',
    'date' => 'Hôm nay, ' . date('d/m/Y'),
    'seat' => 'H12, H13 (VIP)',
    'price' => '220.000đ',
    'ticket_no' => 'CG-DN-889240',
    'synopsis' => 'Cuộc giải cứu sinh tử nghẹt thở nơi hang ổ tội phạm xuyên biên giới. Hành trình báo thù và bảo vệ gia đình của người anh trai dũng cảm.',
    'gradient' => 'linear-gradient(135deg, #1f0808 0%, #681515 50%, #b45309 100%)',
    'accent_art' => '#f59e0b'
];

$moviesList = [
    [
        'id' => 'm01',
        'title' => 'Trại Buôn Người',
        'genres' => ['Hành động'],
        'duration' => 120,
        'age' => 'T18',
        'rating' => 8.3,
        'cinema' => 'CGV Vincom',
        'gradient' => 'linear-gradient(140deg, #1f0707 0%, #701313 55%, #c2410c 100%)',
        'accent_art' => '#fb923c',
        'ticket_code' => 'TKT-01-ACT',
        'tagline' => 'Cuộc đào tẩu sinh tử nơi vùng biên'
    ],
    [
        'id' => 'm02',
        'title' => 'Chị Chị Em Em 3',
        'genres' => ['Tâm lý', 'Tình cảm'],
        'duration' => 118,
        'age' => 'T18',
        'rating' => 7.2,
        'cinema' => 'Lotte Hải Châu',
        'gradient' => 'linear-gradient(140deg, #1b0e1a 0%, #6b1756 50%, #e11d48 100%)',
        'accent_art' => '#f472b6',
        'ticket_code' => 'TKT-02-DRA',
        'tagline' => 'Ân oán tình thù chốn Kinh Bắc hoa lệ'
    ],
    [
        'id' => 'm04',
        'title' => 'Clayface',
        'genres' => ['Kinh dị', 'Hành động'],
        'duration' => 108,
        'age' => 'T18',
        'rating' => 7.5,
        'cinema' => 'Galaxy Nguyễn Tri Phương',
        'gradient' => 'linear-gradient(140deg, #070e1b 0%, #173252 50%, #0d9488 100%)',
        'accent_art' => '#2dd4bf',
        'ticket_code' => 'TKT-04-HOR',
        'tagline' => 'Bóng ma quái nhân trỗi dậy tại Gotham'
    ],
    [
        'id' => 'm05',
        'title' => 'Án Mạng Karaoke',
        'genres' => ['Hài', 'Trinh thám'],
        'duration' => 105,
        'age' => 'T13',
        'rating' => 7.6,
        'cinema' => 'BHD Star Helio',
        'gradient' => 'linear-gradient(140deg, #051a11 0%, #065f46 50%, #84cc16 100%)',
        'accent_art' => '#a3e635',
        'ticket_code' => 'TKT-05-COM',
        'tagline' => 'Vụ án ly kỳ bên micro tử thần'
    ],
    [
        'id' => 'm07',
        'title' => 'Chàng Mèo Mang Mũ',
        'genres' => ['Hoạt hình'],
        'duration' => 95,
        'age' => 'P',
        'rating' => 8.1,
        'cinema' => 'Cinestar Bạch Đằng',
        'gradient' => 'linear-gradient(140deg, #2b0c0c 0%, #b91c1c 50%, #f59e0b 100%)',
        'accent_art' => '#fde047',
        'ticket_code' => 'TKT-07-ANI',
        'tagline' => 'Chuyến phiêu lưu kỳ diệu cho gia đình'
    ],
];

$cinemas = [
    [
        'id' => 'cgv-vincom',
        'name' => 'CGV Vincom Đà Nẵng',
        'address' => 'Tầng 4 Vincom Plaza, 910A Ngô Quyền, Sơn Trà',
        'badge' => 'Gold Class & Starium',
        'times' => ['09:45', '11:30', '14:15', '16:45', '19:45', '21:15', '22:45']
    ],
    [
        'id' => 'lotte-danang',
        'name' => 'Lotte Cinema Đà Nẵng',
        'address' => 'Tầng 5 Lotte Mart, 6 Nại Nam, Hải Châu',
        'badge' => 'Super Plex Laser',
        'times' => ['10:00', '12:15', '14:30', '17:00', '19:15', '21:30']
    ],
    [
        'id' => 'galaxy-ntp',
        'name' => 'Galaxy Nguyễn Tri Phương',
        'address' => '86 Nguyễn Tri Phương, Thanh Khê',
        'badge' => 'Dolby Atmos 7.1',
        'times' => ['09:15', '11:45', '14:00', '16:30', '18:45', '21:00', '22:15']
    ],
    [
        'id' => 'bhd-helio',
        'name' => 'BHD Star Helio Center',
        'address' => 'Khu giải trí Helio, Đường 2 Tháng 9, Hải Châu',
        'badge' => 'First Class Lounges',
        'times' => ['10:30', '13:00', '15:15', '18:00', '20:15', '22:30']
    ],
    [
        'id' => 'cinestar-hc',
        'name' => 'Cinestar Hải Châu',
        'address' => '55 Bạch Đằng, Hải Châu',
        'badge' => 'Rạp bờ sông Hàn',
        'times' => ['09:30', '11:15', '13:45', '16:00', '18:15', '20:30']
    ],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineGo Đà Nẵng – Đặt Vé Xem Phim Phong Cách Vé Giấy</title>
    <meta name="description" content="Hệ thống đặt vé xem phim CineGo tại các cụm rạp Đà Nẵng (CGV, Lotte, Galaxy, BHD, Cinestar) theo phong cách vé giấy cổ điển độc đáo.">

    <!-- Google Fonts: Bricolage Grotesque & Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&display=swap" rel="stylesheet">

    <style>
        /* ==========================================================================
           BẢNG MÀU THEO YÊU CẦU & THIẾT LẬP HỆ THỐNG
           ========================================================================== */
        :root {
            /* Light Mode (Mặc định) */
            --bg-page: #fbf4e8;
            --bg-card: #fffaf1;
            --text-primary: #1b1f3b;
            --text-secondary: #6b6f8a;
            --border-color: #e6d9c3;
            --accent: #ff5a36;
            --accent-hover: #e54320;
            --accent-text: #ffffff;
            --tag-bg: #ffe3d6;
            --navbar-bg: #1b1f3b;
            --navbar-text: #fbf4e8;

            /* Tấm vé & Hiệu ứng giấy */
            --ticket-notch-color: var(--bg-page);
            --ticket-dash: #d6c5ad;
            --ticket-barcode: #1b1f3b;
            --shadow-ticket: 0 16px 36px rgba(27, 31, 59, 0.09), 0 4px 12px rgba(27, 31, 59, 0.04);
            --shadow-hover: 0 24px 44px rgba(27, 31, 59, 0.15), 0 8px 18px rgba(27, 31, 59, 0.06);

            /* Bo góc chuẩn */
            --radius-lg: 24px;
            --radius-md: 18px;
            --radius-sm: 14px;
            --transition-smooth: all 0.32s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Dark Mode: Tự bật theo prefers-color-scheme hoặc toggle */
        @media (prefers-color-scheme: dark) {
            :root:not([data-theme="light"]) {
                --bg-page: #12142a;
                --bg-card: #1b1e3a;
                --text-primary: #f5eddc;
                --text-secondary: #a3a6c4;
                --border-color: #2f335a;
                --accent: #ff7a59;
                --accent-hover: #ff9175;
                --accent-text: #1b1f3b;
                --tag-bg: #3a2a35;
                --navbar-bg: #0c0e20;
                --navbar-text: #f5eddc;

                --ticket-notch-color: var(--bg-page);
                --ticket-dash: #3d4370;
                --ticket-barcode: #f5eddc;
                --shadow-ticket: 0 16px 36px rgba(0, 0, 0, 0.35);
                --shadow-hover: 0 24px 48px rgba(0, 0, 0, 0.5);
            }
        }

        /* Can thiệp thủ công bằng nút Theme Toggle nếu người dùng muốn */
        :root[data-theme="dark"],
        html[data-theme="dark"],
        body.theme-dark {
            --bg-page: #12142a;
            --bg-card: #1b1e3a;
            --text-primary: #f5eddc;
            --text-secondary: #a3a6c4;
            --border-color: #2f335a;
            --accent: #ff7a59;
            --accent-hover: #ff9175;
            --accent-text: #1b1f3b;
            --tag-bg: #3a2a35;
            --navbar-bg: #0c0e20;
            --navbar-text: #f5eddc;

            --ticket-notch-color: var(--bg-page);
            --ticket-dash: #3d4370;
            --ticket-barcode: #f5eddc;
            --shadow-ticket: 0 16px 36px rgba(0, 0, 0, 0.35);
            --shadow-hover: 0 24px 48px rgba(0, 0, 0, 0.5);
        }

        :root[data-theme="light"],
        html[data-theme="light"],
        body.theme-light {
            --bg-page: #fbf4e8;
            --bg-card: #fffaf1;
            --text-primary: #1b1f3b;
            --text-secondary: #6b6f8a;
            --border-color: #e6d9c3;
            --accent: #ff5a36;
            --accent-hover: #e54320;
            --accent-text: #ffffff;
            --tag-bg: #ffe3d6;
            --navbar-bg: #1b1f3b;
            --navbar-text: #fbf4e8;

            --ticket-notch-color: var(--bg-page);
            --ticket-dash: #d6c5ad;
            --ticket-barcode: #1b1f3b;
            --shadow-ticket: 0 16px 36px rgba(27, 31, 59, 0.09);
            --shadow-hover: 0 24px 44px rgba(27, 31, 59, 0.15);
        }

        /* Reset & Base Typography */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Be Vietnam Pro", system-ui, -apple-system, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            background-color: var(--bg-page);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, .brand-logo, .ticket-num, .stat-value {
            font-family: "Bricolage Grotesque", system-ui, sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.15;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font-family: inherit;
            cursor: pointer;
            border: none;
            outline: none;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ==========================================================================
           NAVBAR DÍNH TRÊN CÙNG (STICKY NAVBAR)
           ========================================================================== */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background-color: var(--navbar-bg);
            color: var(--navbar-text);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(8px);
        }

        .navbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--navbar-text);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-logo .dot {
            color: var(--accent);
        }

        .city-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.12);
            color: #ffd8cc;
            letter-spacing: 0.02em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-link {
            font-size: 0.95rem;
            font-weight: 500;
            color: rgba(251, 244, 232, 0.82);
            transition: color 0.2s ease;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            color: var(--navbar-text);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1.1rem;
            transition: var(--transition-smooth);
        }

        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.06);
        }

        .btn-quick-book {
            background-color: var(--accent);
            color: var(--accent-text);
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.92rem;
            box-shadow: 0 4px 14px rgba(255, 90, 54, 0.35);
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-quick-book:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(255, 90, 54, 0.45);
        }

        /* Mobile Hamburger */
        .mobile-toggle {
            display: none;
            background: transparent;
            color: var(--navbar-text);
            font-size: 1.5rem;
        }

        /* ==========================================================================
           HERO SECTION: TIÊU ĐỀ LỚN & THẺ VÉ NỔI BẬT (PAPER TICKET STYLE)
           ========================================================================== */
        .hero-section {
            padding: 60px 0 70px;
            position: relative;
            background: radial-gradient(circle at 10% 20%, rgba(255, 90, 54, 0.05) 0%, transparent 45%);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            gap: 48px;
            align-items: center;
        }

        .hero-intro .tagline-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: var(--tag-bg);
            color: var(--accent);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 90, 54, 0.25);
        }

        .hero-title {
            font-size: clamp(2.4rem, 4.2vw, 3.8rem);
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.08;
            margin-bottom: 22px;
        }

        .hero-title .highlight {
            color: var(--accent);
            position: relative;
            display: inline-block;
        }

        .hero-desc {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 32px;
            max-width: 480px;
        }

        .hero-stats {
            display: flex;
            gap: 28px;
            padding-top: 20px;
            border-top: 1px dashed var(--border-color);
        }

        .stat-item .stat-value {
            font-size: 1.85rem;
            color: var(--accent);
            font-weight: 800;
        }

        .stat-item .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ==========================================================================
           THẺ VÉ GIẤY NỔI BẬT (FEATURED PAPER TICKET)
           - Hai nửa vòng tròn khuyết (cutout notches)
           - Đường xé răng cưa nét đứt (perforated line)
           - Mã vạch bên phải
           - Xoay nhẹ 1.5 độ
           ========================================================================== */
        .ticket-wrapper {
            perspective: 1000px;
            padding: 10px;
        }

        .paper-ticket {
            position: relative;
            background-color: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-ticket);
            border: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 1.8fr 1fr;
            transform: rotate(1.5deg);
            transition: var(--transition-smooth);
            overflow: visible;
        }

        .paper-ticket:hover {
            transform: rotate(0deg) translateY(-6px);
            box-shadow: var(--shadow-hover);
        }

        /* Hai nửa vòng tròn khuyết ở mép giao cắt thân vé và cuống vé */
        .ticket-notch-top, .ticket-notch-bottom {
            position: absolute;
            left: 64.28%; /* Vị trí ranh giới 1.8fr : 1fr */
            width: 32px;
            height: 32px;
            background-color: var(--ticket-notch-color);
            border-radius: 50%;
            z-index: 10;
            transform: translateX(-50%);
            border: 1px solid var(--border-color);
            transition: background-color 0.3s ease;
        }

        .ticket-notch-top {
            top: -16px;
            clip-path: polygon(0 50%, 100% 50%, 100% 100%, 0 100%);
        }

        .ticket-notch-bottom {
            bottom: -16px;
            clip-path: polygon(0 0, 100% 0, 100% 50%, 0 50%);
        }

        /* Thân vé (Ticket Body) */
        .ticket-body {
            padding: 28px 26px 28px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .ticket-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .ticket-badge-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge-age {
            font-size: 0.76rem;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 6px;
            background-color: #ef4444;
            color: #ffffff;
            letter-spacing: 0.05em;
        }

        .badge-genre {
            font-size: 0.76rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
            background-color: var(--tag-bg);
            color: var(--accent);
        }

        .ticket-score {
            display: flex;
            align-items: center;
            gap: 4px;
            font-family: "Bricolage Grotesque", sans-serif;
            font-weight: 800;
            color: #f59e0b;
            font-size: 0.95rem;
        }

        /* Poster Gradient riêng cho phim */
        .ticket-gradient-art {
            width: 100%;
            height: 160px;
            border-radius: var(--radius-sm);
            background: <?= $featuredMovie['gradient'] ?>;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.45);
            margin-bottom: 20px;
        }

        .gradient-decor-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.18;
            background-image: radial-gradient(#fff 1.5px, transparent 1.5px);
            background-size: 14px 14px;
        }

        .art-title-overlay {
            color: #ffffff;
            font-family: "Bricolage Grotesque", sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            text-shadow: 0 4px 16px rgba(0,0,0,0.6);
            text-align: center;
            padding: 0 20px;
            z-index: 2;
        }

        .art-film-tag {
            position: absolute;
            bottom: 12px;
            right: 14px;
            font-size: 0.72rem;
            letter-spacing: 0.15em;
            color: <?= $featuredMovie['accent_art'] ?>;
            text-transform: uppercase;
            font-weight: 700;
            z-index: 2;
        }

        .ticket-movie-title {
            font-size: 1.6rem;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .ticket-movie-sub {
            font-size: 0.88rem;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .ticket-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            background: rgba(230, 217, 195, 0.22);
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
        }

        .meta-col .meta-label {
            font-size: 0.72rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .meta-col .meta-value {
            font-size: 0.88rem;
            color: var(--text-primary);
            font-weight: 700;
        }

        /* Cuống vé (Ticket Stub) */
        .ticket-stub {
            position: relative;
            padding: 26px 22px;
            border-left: 2px dashed var(--ticket-dash);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: rgba(255, 90, 54, 0.02);
            border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
        }

        .stub-top .stub-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-secondary);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stub-top .ticket-num {
            font-size: 1.15rem;
            color: var(--accent);
            margin-bottom: 16px;
        }

        .stub-row {
            margin-bottom: 10px;
        }

        .stub-row-label {
            font-size: 0.7rem;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .stub-row-val {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Mã vạch bên phải */
        .barcode-box {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px dashed var(--ticket-dash);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .barcode-lines {
            display: flex;
            align-items: flex-end;
            height: 48px;
            gap: 2.5px;
            width: 100%;
            justify-content: center;
        }

        .barcode-lines span {
            background-color: var(--ticket-barcode);
            display: inline-block;
            height: 100%;
            border-radius: 1px;
        }

        .barcode-num {
            font-family: monospace;
            font-size: 0.72rem;
            color: var(--text-secondary);
            letter-spacing: 0.22em;
        }

        .btn-stub-action {
            margin-top: 12px;
            width: 100%;
            background-color: var(--text-primary);
            color: #ffffff;
            border: 2px solid var(--text-primary);
            padding: 10px;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.85rem;
            text-align: center;
            transition: var(--transition-smooth);
        }

        .btn-stub-action:hover {
            background-color: var(--accent);
            border-color: var(--accent);
            color: var(--accent-text);
        }

        /* ==========================================================================
           LƯỚI 5 PHIM CÓ LỌC THỂ LOẠI (MOVIES GRID + GENRE FILTER)
           ========================================================================== */
        .movies-section {
            padding: 80px 0 70px;
            border-top: 1px dashed var(--border-color);
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 36px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .section-title {
            font-size: 2.2rem;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .section-sub {
            color: var(--text-secondary);
            font-size: 0.98rem;
        }

        /* Bộ lọc thể loại */
        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background-color: var(--bg-card);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.88rem;
            font-weight: 600;
            transition: var(--transition-smooth);
        }

        .filter-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .filter-btn.active {
            background-color: var(--text-primary);
            color: #ffffff;
            border-color: var(--text-primary);
        }

        /* Lưới 5 card vé xem phim */
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(215px, 1fr));
            gap: 24px;
        }

        .mini-ticket-card {
            position: relative;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 16px;
            box-shadow: var(--shadow-ticket);
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            cursor: pointer;
        }

        /* Hover card: nâng lên 6px và xoay nhẹ */
        .mini-ticket-card:hover {
            transform: translateY(-6px) rotate(-1deg);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent);
        }

        /* Nửa vòng tròn khuyết mini của thẻ phim */
        .mini-card-notch-l, .mini-card-notch-r {
            position: absolute;
            top: 170px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: var(--ticket-notch-color);
            border: 1px solid var(--border-color);
            z-index: 5;
            transition: background-color 0.3s ease;
        }
        .mini-card-notch-l { left: -10px; }
        .mini-card-notch-r { right: -10px; }

        .mini-poster-gradient {
            width: 100%;
            height: 180px;
            border-radius: var(--radius-sm);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            text-align: center;
            box-shadow: inset 0 0 30px rgba(0,0,0,0.5);
            margin-bottom: 14px;
        }

        .mini-poster-text {
            color: #ffffff;
            font-family: "Bricolage Grotesque", sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0,0,0,0.7);
        }

        .mini-poster-badges {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            gap: 6px;
        }

        .mini-ticket-code {
            position: absolute;
            bottom: 8px;
            right: 10px;
            font-family: monospace;
            font-size: 0.68rem;
            color: rgba(255,255,255,0.7);
            letter-spacing: 0.1em;
        }

        /* Nét đứt phân tách cuống vé */
        .mini-tear-line {
            border-bottom: 2px dashed var(--ticket-dash);
            margin: 6px -16px 14px;
        }

        .mini-movie-title {
            font-size: 1.12rem;
            color: var(--text-primary);
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mini-movie-tagline {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 14px;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 34px;
        }

        .mini-card-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
        }

        .mini-info-time {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .btn-card-book {
            background-color: var(--accent);
            color: var(--accent-text);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 700;
            transition: var(--transition-smooth);
        }

        .btn-card-book:hover {
            background-color: var(--accent-hover);
            transform: scale(1.05);
        }

        /* ==========================================================================
           SUẤT CHIẾU THEO RẠP TẠI ĐÀ NẴNG (SHOWTIMES BY CINEMA)
           ========================================================================== */
        .showtimes-section {
            padding: 70px 0 80px;
            background: rgba(230, 217, 195, 0.15);
            border-top: 1px dashed var(--border-color);
            border-bottom: 1px dashed var(--border-color);
        }

        .cinema-tabs-nav {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 28px;
            scrollbar-width: thin;
        }

        .cinema-tab-btn {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.92rem;
            font-weight: 700;
            white-space: nowrap;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cinema-tab-btn:hover {
            border-color: var(--accent);
        }

        .cinema-tab-btn.active {
            background-color: var(--text-primary);
            color: #ffffff;
            border-color: var(--text-primary);
            box-shadow: 0 4px 14px rgba(27, 31, 59, 0.2);
        }

        .cinema-tab-btn .badge-star {
            font-size: 0.72rem;
            padding: 2px 7px;
            border-radius: 10px;
            background: var(--tag-bg);
            color: var(--accent);
        }

        .cinema-tab-btn.active .badge-star {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* Bảng rạp đang chọn & Chip giờ chiếu */
        .cinema-panel {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-ticket);
        }

        .cinema-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px dashed var(--border-color);
            flex-wrap: wrap;
            gap: 14px;
        }

        .cinema-title-box h3 {
            font-size: 1.45rem;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .cinema-title-box p {
            font-size: 0.88rem;
            color: var(--text-secondary);
        }

        .date-picker-pills {
            display: flex;
            gap: 8px;
        }

        .date-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.84rem;
            font-weight: 600;
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            transition: var(--transition-smooth);
        }

        .date-pill.active, .date-pill:hover {
            background: var(--tag-bg);
            color: var(--accent);
            border-color: var(--accent);
        }

        /* Movie Row with Chips */
        .cinema-movie-row {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 28px;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .cinema-movie-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .cinema-movie-info h4 {
            font-size: 1.15rem;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .cinema-movie-meta {
            font-size: 0.82rem;
            color: var(--text-secondary);
            display: flex;
            gap: 10px;
        }

        /* Chip giờ chiếu bấm được */
        .time-chips-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .time-chip {
            background-color: var(--bg-page);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-family: "Bricolage Grotesque", sans-serif;
            font-size: 0.98rem;
            font-weight: 700;
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .time-chip span.format {
            font-family: "Be Vietnam Pro", sans-serif;
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .time-chip:hover {
            background-color: var(--tag-bg);
            border-color: var(--accent);
            color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(255, 90, 54, 0.2);
        }

        .time-chip.selected {
            background-color: var(--accent);
            border-color: var(--accent);
            color: var(--accent-text);
            box-shadow: 0 4px 14px rgba(255, 90, 54, 0.4);
        }

        .time-chip.selected span.format {
            color: rgba(255, 255, 255, 0.85);
        }

        /* ==========================================================================
           3 BƯỚC ĐẶT VÉ (3 BOOKING STEPS)
           ========================================================================== */
        .steps-section {
            padding: 80px 0;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .step-paper-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 32px 28px;
            box-shadow: var(--shadow-ticket);
            position: relative;
            transition: var(--transition-smooth);
            overflow: hidden;
        }

        .step-paper-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent);
        }

        /* Tem số thứ tự lớn */
        .step-tag-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--tag-bg);
            color: var(--accent);
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            font-family: "Bricolage Grotesque", sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 90, 54, 0.2);
        }

        .step-paper-card h3 {
            font-size: 1.25rem;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .step-paper-card p {
            font-size: 0.94rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .step-decor-dash {
            border-bottom: 2px dashed var(--ticket-dash);
            margin: 22px 0 16px;
        }

        .step-note {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ==========================================================================
           FOOTER
           ========================================================================== */
        .site-footer {
            background-color: var(--navbar-bg);
            color: var(--navbar-text);
            padding: 64px 0 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 48px;
        }

        .footer-col h4 {
            font-size: 1.15rem;
            color: #ffffff;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .footer-col p {
            color: rgba(251, 244, 232, 0.72);
            font-size: 0.92rem;
            line-height: 1.7;
            margin-bottom: 18px;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-links a {
            color: rgba(251, 244, 232, 0.75);
            font-size: 0.92rem;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        .footer-bottom {
            padding-top: 28px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 0.85rem;
            color: rgba(251, 244, 232, 0.55);
        }

        /* ==========================================================================
           MODAL ĐẶT VÉ VÉ GIẤY TƯƠNG TÁC (INTERACTIVE TICKET MODAL)
           ========================================================================== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(18, 20, 42, 0.75);
            backdrop-filter: blur(6px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-paper-ticket {
            background-color: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.45);
            max-width: 540px;
            width: 100%;
            position: relative;
            padding: 32px 30px;
            transform: scale(0.92);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: visible;
        }

        .modal-overlay.active .modal-paper-ticket {
            transform: scale(1);
        }

        .modal-close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(107, 111, 138, 0.15);
            color: var(--text-primary);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            transition: var(--transition-smooth);
        }

        .modal-close-btn:hover {
            background: var(--accent);
            color: #ffffff;
        }

        .modal-ticket-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .modal-ticket-title {
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .modal-ticket-cinema {
            color: var(--accent);
            font-size: 0.92rem;
            font-weight: 700;
        }

        .modal-dash {
            border-bottom: 2px dashed var(--ticket-dash);
            margin: 18px -30px;
            position: relative;
        }

        .modal-dash::before, .modal-dash::after {
            content: "";
            position: absolute;
            top: -12px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--bg-page);
            border: 1px solid var(--border-color);
        }

        .modal-dash::before { left: -12px; }
        .modal-dash::after { right: -12px; }

        .modal-seat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .modal-seat-item {
            background: rgba(230, 217, 195, 0.2);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
        }

        .modal-seat-item .label {
            font-size: 0.72rem;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .modal-seat-item .val {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .modal-total-row .total-label {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-total-row .total-val {
            font-family: "Bricolage Grotesque", sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--accent);
        }

        .btn-confirm-ticket {
            width: 100%;
            background-color: var(--accent);
            color: var(--accent-text);
            padding: 14px;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(255, 90, 54, 0.35);
            transition: var(--transition-smooth);
        }

        .btn-confirm-ticket:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
        }

        /* ==========================================================================
           RESPONSIVE MOBILE BREAKPOINTS
           ========================================================================== */
        @media (max-width: 992px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .paper-ticket {
                transform: rotate(0deg);
                max-width: 600px;
                margin: 0 auto;
            }
            .cinema-movie-row {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .steps-grid {
                grid-template-columns: 1fr;
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 680px) {
            .nav-links {
                display: none;
            }
            .mobile-toggle {
                display: block;
            }
            .paper-ticket {
                grid-template-columns: 1fr;
            }
            .ticket-stub {
                border-left: none;
                border-top: 2px dashed var(--ticket-dash);
                border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            }
            .ticket-notch-top, .ticket-notch-bottom {
                display: none;
            }
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 16px;
            }
            .hero-title {
                font-size: 2.2rem;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- ==========================================================================
         NAVBAR DÍNH TRÊN CÙNG
         ========================================================================== -->
    <header class="navbar">
        <div class="container navbar-content">
            <div class="brand-box">
                <a href="#hero" class="brand-logo">
                    CineGo<span class="dot">.</span>
                </a>
                <span class="city-badge">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    Đà Nẵng
                </span>
            </div>

            <nav>
                <ul class="nav-links">
                    <li><a href="#movies" class="nav-link active">Phim Đang Chiếu</a></li>
                    <li><a href="#showtimes" class="nav-link">Lịch Chiếu Rạp</a></li>
                    <li><a href="#steps" class="nav-link">Quy Trình Vé Giấy</a></li>
                    <li><a href="#cinemas-info" class="nav-link">Cụm Rạp</a></li>
                </ul>
            </nav>

            <div class="nav-actions">
                <!-- Theme Toggle Button -->
                <button class="theme-toggle-btn" id="themeToggleBtn" aria-label="Đổi giao diện Sáng / Tối" title="Chuyển chế độ Sáng / Tối">
                    🌓
                </button>
                <a href="#showtimes" class="btn-quick-book">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M22 10V6a2 2 0 0 0-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2zm-9 7.5h-2v-2h2v2zm0-4.5h-2v-2h2v2zm0-4.5h-2v-2h2v2z"/></svg>
                    Đặt vé ngay
                </a>
            </div>
        </div>
    </header>

    <!-- ==========================================================================
         HERO SECTION: TIÊU ĐỀ LỚN & THẺ VÉ NỔI BẬT
         ========================================================================== -->
    <section class="hero-section" id="hero">
        <div class="container hero-grid">
            <!-- Cột trái: Giới thiệu -->
            <div class="hero-intro">
                <div class="tagline-pill">
                    <span>🎟️ PHONG CÁCH VÉ GIẤY ĐIỆN TỬ</span>
                </div>
                <h1 class="hero-title">
                    Cầm tấm vé,<br>
                    thưởng thức điện ảnh tại <span class="highlight">Đà Nẵng</span>.
                </h1>
                <p class="hero-desc">
                    Trải nghiệm đặt vé xem phim đậm chất hoài niệm với phong cách vé giấy xé cuống độc đáo. Kết nối trực tiếp 5 cụm rạp hàng đầu thành phố bên sông Hàn.
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-value">5+</div>
                        <div class="stat-label">Cụm rạp Đà Nẵng</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">60+</div>
                        <div class="stat-label">Suất chiếu mỗi ngày</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">100%</div>
                        <div class="stat-label">Vé giấy mã vạch</div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Thẻ vé nổi bật (Featured Paper Ticket) -->
            <div class="ticket-wrapper">
                <div class="paper-ticket" id="featuredTicket">
                    <!-- Hai nửa vòng tròn khuyết -->
                    <div class="ticket-notch-top"></div>
                    <div class="ticket-notch-bottom"></div>

                    <!-- Thân vé chính (Ticket Body) -->
                    <div class="ticket-body">
                        <div>
                            <div class="ticket-header-row">
                                <div class="ticket-badge-group">
                                    <span class="badge-age"><?= htmlspecialchars($featuredMovie['age']) ?></span>
                                    <?php foreach ($featuredMovie['genres'] as $g): ?>
                                        <span class="badge-genre"><?= htmlspecialchars($g) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="ticket-score">
                                    ★ <?= number_format($featuredMovie['rating'], 1) ?>
                                </div>
                            </div>

                            <!-- Poster Gradient Nghệ Thuật (Không dùng ảnh ngoài) -->
                            <div class="ticket-gradient-art">
                                <div class="gradient-decor-pattern"></div>
                                <h3 class="art-title-overlay"><?= htmlspecialchars($featuredMovie['title']) ?></h3>
                                <div class="art-film-tag">CINEMA TICKET</div>
                            </div>

                            <h2 class="ticket-movie-title"><?= htmlspecialchars($featuredMovie['title']) ?></h2>
                            <p class="ticket-movie-sub"><?= htmlspecialchars($featuredMovie['subtitle']) ?> • <?= $featuredMovie['duration'] ?> phút</p>
                        </div>

                        <!-- Thông tin vé chi tiết -->
                        <div class="ticket-meta-grid">
                            <div class="meta-col">
                                <div class="meta-label">Rạp chiếu</div>
                                <div class="meta-value"><?= htmlspecialchars($featuredMovie['cinema']) ?></div>
                            </div>
                            <div class="meta-col">
                                <div class="meta-label">Phòng chiếu</div>
                                <div class="meta-value"><?= htmlspecialchars($featuredMovie['room']) ?></div>
                            </div>
                            <div class="meta-col">
                                <div class="meta-label">Suất chiếu</div>
                                <div class="meta-value"><?= htmlspecialchars($featuredMovie['time']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Cuống vé xé bên phải (Ticket Stub) -->
                    <div class="ticket-stub">
                        <div class="stub-top">
                            <div class="stub-label">Cuống vé kiểm soát</div>
                            <div class="ticket-num"><?= htmlspecialchars($featuredMovie['ticket_no']) ?></div>

                            <div class="stub-row">
                                <div class="stub-row-label">Ngày chiếu</div>
                                <div class="stub-row-val"><?= htmlspecialchars($featuredMovie['date']) ?></div>
                            </div>
                            <div class="stub-row">
                                <div class="stub-row-label">Vị trí ghế</div>
                                <div class="stub-row-val" style="color: var(--accent);"><?= htmlspecialchars($featuredMovie['seat']) ?></div>
                            </div>
                            <div class="stub-row">
                                <div class="stub-row-label">Giá vé</div>
                                <div class="stub-row-val"><?= htmlspecialchars($featuredMovie['price']) ?></div>
                            </div>
                        </div>

                        <!-- Mã vạch bên phải -->
                        <div class="barcode-box">
                            <div class="barcode-lines">
                                <span style="width: 3px;"></span>
                                <span style="width: 1px;"></span>
                                <span style="width: 4px;"></span>
                                <span style="width: 2px;"></span>
                                <span style="width: 1px;"></span>
                                <span style="width: 3px;"></span>
                                <span style="width: 2px;"></span>
                                <span style="width: 5px;"></span>
                                <span style="width: 1px;"></span>
                                <span style="width: 3px;"></span>
                                <span style="width: 2px;"></span>
                                <span style="width: 4px;"></span>
                                <span style="width: 1px;"></span>
                                <span style="width: 2px;"></span>
                                <span style="width: 3px;"></span>
                            </div>
                            <span class="barcode-num">8892-0491-DN</span>
                            <button class="btn-stub-action" onclick="openTicketModal('<?= $featuredMovie['title'] ?>', '<?= $featuredMovie['cinema'] ?>', '<?= $featuredMovie['time'] ?>', '<?= $featuredMovie['price'] ?>')">
                                Xé vé & Đặt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================================================
         LƯỚI 5 PHIM CÓ LỌC THỂ LOẠI
         ========================================================================== -->
    <section class="movies-section" id="movies">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Phim Đang Chiếu Tại Rạp</h2>
                    <p class="section-sub">Tuyển chọn 5 tác phẩm điện ảnh xuất sắc nhất tại các rạp Đà Nẵng tuần này</p>
                </div>

                <!-- Bộ lọc thể loại bằng thẻ bấm -->
                <div class="filter-bar" id="genreFilterBar">
                    <button class="filter-btn active" data-genre="all">Tất cả (5)</button>
                    <button class="filter-btn" data-genre="Hành động">Hành động</button>
                    <button class="filter-btn" data-genre="Tâm lý">Tâm lý</button>
                    <button class="filter-btn" data-genre="Kinh dị">Kinh dị</button>
                    <button class="filter-btn" data-genre="Hài">Hài</button>
                    <button class="filter-btn" data-genre="Hoạt hình">Hoạt hình</button>
                </div>
            </div>

            <!-- Lưới 5 thẻ phim phong cách vé giấy mini -->
            <div class="movies-grid" id="moviesGrid">
                <?php foreach ($moviesList as $m): ?>
                    <article class="mini-ticket-card" data-genres="<?= implode(',', $m['genres']) ?>" onclick="openTicketModal('<?= htmlspecialchars($m['title']) ?>', '<?= htmlspecialchars($m['cinema']) ?>', '19:30', '110.000đ')">
                        <!-- Nửa vòng tròn khuyết mini 2 mép -->
                        <div class="mini-card-notch-l"></div>
                        <div class="mini-card-notch-r"></div>

                        <!-- Poster Gradient riêng cho phim -->
                        <div class="mini-poster-gradient" style="background: <?= $m['gradient'] ?>;">
                            <div class="mini-poster-badges">
                                <span class="badge-age"><?= htmlspecialchars($m['age']) ?></span>
                            </div>
                            <div class="mini-poster-text"><?= htmlspecialchars($m['title']) ?></div>
                            <span class="mini-ticket-code"><?= $m['ticket_code'] ?></span>
                        </div>

                        <!-- Nét đứt phân tách cuống vé -->
                        <div class="mini-tear-line"></div>

                        <h3 class="mini-movie-title"><?= htmlspecialchars($m['title']) ?></h3>
                        <p class="mini-movie-tagline"><?= htmlspecialchars($m['tagline']) ?></p>

                        <div class="mini-card-footer">
                            <span class="mini-info-time">⏱️ <?= $m['duration'] ?>p</span>
                            <button class="btn-card-book" aria-label="Đặt vé <?= htmlspecialchars($m['title']) ?>">
                                Đặt vé
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ==========================================================================
         SUẤT CHIẾU THEO RẠP TẠI ĐÀ NẴNG (CHIP GIỜ CHIẾU BẤM ĐƯỢC)
         ========================================================================== -->
    <section class="showtimes-section" id="showtimes">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Lịch Chiếu Rạp Đà Nẵng</h2>
                    <p class="section-sub">Chọn rạp, ngày chiếu và bấm vào giờ chiếu để xé vé trực tiếp</p>
                </div>
            </div>

            <!-- Tab chọn cụm rạp -->
            <div class="cinema-tabs-nav" id="cinemaTabs">
                <?php foreach ($cinemas as $idx => $c): ?>
                    <button class="cinema-tab-btn <?= $idx === 0 ? 'active' : '' ?>" data-cinema-id="<?= $c['id'] ?>">
                        🏢 <?= htmlspecialchars($c['name']) ?>
                        <span class="badge-star"><?= $c['badge'] ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Bảng hiển thị suất chiếu theo rạp -->
            <div class="cinema-panel">
                <div class="cinema-panel-header">
                    <div class="cinema-title-box">
                        <h3 id="panelCinemaName">CGV Vincom Đà Nẵng</h3>
                        <p id="panelCinemaAddress">Tầng 4 Vincom Plaza, 910A Ngô Quyền, Q. Sơn Trà, TP. Đà Nẵng</p>
                    </div>
                    <div class="date-picker-pills">
                        <button class="date-pill active" onclick="switchDate(this, 'Hôm nay')">Hôm nay (<?= date('d/m') ?>)</button>
                        <button class="date-pill" onclick="switchDate(this, 'Ngày mai')">Ngày mai (<?= date('d/m', strtotime('+1 day')) ?>)</button>
                        <button class="date-pill" onclick="switchDate(this, 'Cuối tuần')">Cuối tuần</button>
                    </div>
                </div>

                <!-- Danh sách phim & chip giờ chiếu -->
                <div id="cinemaShowtimesList">
                    <?php foreach (array_slice($moviesList, 0, 3) as $m): ?>
                        <div class="cinema-movie-row">
                            <div class="cinema-movie-info">
                                <h4><?= htmlspecialchars($m['title']) ?></h4>
                                <div class="cinema-movie-meta">
                                    <span><?= $m['age'] ?></span>
                                    <span>•</span>
                                    <span><?= $m['duration'] ?> phút</span>
                                    <span>•</span>
                                    <span>2D Phụ đề</span>
                                </div>
                            </div>
                            <div class="time-chips-group">
                                <button class="time-chip" onclick="selectTimeChip(this, '<?= htmlspecialchars($m['title']) ?>', '10:15', 'CGV Vincom')">
                                    10:15
                                    <span class="format">Phòng 01</span>
                                </button>
                                <button class="time-chip" onclick="selectTimeChip(this, '<?= htmlspecialchars($m['title']) ?>', '13:30', 'CGV Vincom')">
                                    13:30
                                    <span class="format">Phòng 02</span>
                                </button>
                                <button class="time-chip" onclick="selectTimeChip(this, '<?= htmlspecialchars($m['title']) ?>', '16:45', 'CGV Vincom')">
                                    16:45
                                    <span class="format">Gold Class</span>
                                </button>
                                <button class="time-chip" onclick="selectTimeChip(this, '<?= htmlspecialchars($m['title']) ?>', '19:45', 'CGV Vincom')">
                                    19:45
                                    <span class="format">Starium VIP</span>
                                </button>
                                <button class="time-chip" onclick="selectTimeChip(this, '<?= htmlspecialchars($m['title']) ?>', '22:15', 'CGV Vincom')">
                                    22:15
                                    <span class="format">Phòng 03</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================================================
         3 BƯỚC ĐẶT VÉ PHONG CÁCH VÉ GIẤY
         ========================================================================== -->
    <section class="steps-section" id="steps">
        <div class="container">
            <div class="section-header" style="text-align: center; justify-content: center; margin-bottom: 48px;">
                <div style="max-width: 600px;">
                    <div class="tagline-pill" style="margin-bottom: 12px;">ĐƠN GIẢN & TIỆN LỢI</div>
                    <h2 class="section-title">3 Bước Sở Hữu Tấm Vé</h2>
                    <p class="section-sub">Không cần xếp hàng dài tại quầy. Mọi thông tin đều gói gọn trong một tấm vé giấy điện tử chuẩn chỉ.</p>
                </div>
            </div>

            <div class="steps-grid">
                <!-- Bước 1 -->
                <div class="step-paper-card">
                    <div class="step-tag-pill">01</div>
                    <h3>Chọn Phim & Cụm Rạp</h3>
                    <p>Khám phá danh sách phim mới nhất tại 5 cụm rạp Đà Nẵng: CGV Vincom, Lotte Hải Châu, Galaxy, BHD Star hoặc Cinestar.</p>
                    <div class="step-decor-dash"></div>
                    <div class="step-note">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Lọc thể loại theo sở thích
                    </div>
                </div>

                <!-- Bước 2 -->
                <div class="step-paper-card">
                    <div class="step-tag-pill">02</div>
                    <h3>Chọn Ghế & Suất Chiếu</h3>
                    <p>Bấm trực tiếp vào các chip giờ chiếu trên lịch. Chọn chỗ ngồi ưng ý với phân hạng ghế Thường, VIP hay Ghế Đôi Sweetbox.</p>
                    <div class="step-decor-dash"></div>
                    <div class="step-note">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Xem trước vị trí phòng chiếu
                    </div>
                </div>

                <!-- Bước 3 -->
                <div class="step-paper-card">
                    <div class="step-tag-pill">03</div>
                    <h3>Nhận Vé Giấy Mã Vạch</h3>
                    <p>Tấm vé giấy điện tử lập tức phát hành với mã vạch bảo mật và mã ghế rõ ràng. Quét mã tại cửa kiểm soát vào thẳng phòng chiếu.</p>
                    <div class="step-decor-dash"></div>
                    <div class="step-note">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Hỗ trợ lưu về điện thoại
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================================================
         FOOTER
         ========================================================================== -->
    <footer class="site-footer" id="cinemas-info">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="brand-logo" style="margin-bottom: 14px;">
                        CineGo<span class="dot">.</span>
                    </div>
                    <p>
                        Nền tảng đặt vé xem phim phong cách vé giấy tại thành phố Đà Nẵng. Đưa trải nghiệm điện ảnh trở về với sự chỉn chu, tinh tế và đáng nhớ.
                    </p>
                    <span class="city-badge" style="background: rgba(255,255,255,0.08);">
                        📍 Trụ sở: Hải Châu, Đà Nẵng • Hotline: 1900 8892
                    </span>
                </div>

                <div class="footer-col">
                    <h4>Cụm Rạp Đà Nẵng</h4>
                    <ul class="footer-links">
                        <li><a href="#showtimes">CGV Vincom Đà Nẵng</a></li>
                        <li><a href="#showtimes">Lotte Cinema Nại Nam</a></li>
                        <li><a href="#showtimes">Galaxy Nguyễn Tri Phương</a></li>
                        <li><a href="#showtimes">BHD Star Helio Center</a></li>
                        <li><a href="#showtimes">Cinestar Bạch Đằng</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Phim Nổi Bật</h4>
                    <ul class="footer-links">
                        <li><a href="#movies">Trại Buôn Người</a></li>
                        <li><a href="#movies">Chị Chị Em Em 3</a></li>
                        <li><a href="#movies">Clayface (DCU)</a></li>
                        <li><a href="#movies">Án Mạng Karaoke</a></li>
                        <li><a href="#movies">Chàng Mèo Mang Mũ</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Chính Sách & Hỗ Trợ</h4>
                    <ul class="footer-links">
                        <li><a href="#">Quy định đặt & đổi vé</a></li>
                        <li><a href="#">Bảo mật vé điện tử</a></li>
                        <li><a href="#">Hỏi đáp thường gặp</a></li>
                        <li><a href="#">Liên hệ quảng cáo rạp</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>© <?= date('Y') ?> CineGo Vietnam. Phong cách vé giấy (Paper Ticket) cho cụm rạp Đà Nẵng.</div>
                <div>Thiết kế tối ưu theo chuẩn Bricolage Grotesque & Be Vietnam Pro.</div>
            </div>
        </div>
    </footer>

    <!-- ==========================================================================
         MODAL ĐẶT VÉ VÉ GIẤY TƯƠNG TÁC (INTERACTIVE PAPER TICKET MODAL)
         ========================================================================== -->
    <div class="modal-overlay" id="ticketModal" onclick="closeModalOnBackdrop(event)">
        <div class="modal-paper-ticket">
            <button class="modal-close-btn" onclick="closeTicketModal()" aria-label="Đóng">✕</button>

            <div class="modal-ticket-top">
                <div>
                    <span class="tagline-pill" style="margin-bottom: 6px; font-size: 0.72rem;">XÁC NHẬN XÉ VÉ</span>
                    <h3 class="modal-ticket-title" id="modalMovieTitle">Trại Buôn Người</h3>
                </div>
                <div class="modal-ticket-cinema" id="modalCinemaName">CGV Vincom</div>
            </div>

            <div class="modal-dash"></div>

            <div class="modal-seat-grid">
                <div class="modal-seat-item">
                    <div class="label">Suất chiếu</div>
                    <div class="val" id="modalShowtime">19:45 (Hôm nay)</div>
                </div>
                <div class="modal-seat-item">
                    <div class="label">Phòng chiếu</div>
                    <div class="val">Screen 02 (VIP Laser)</div>
                </div>
                <div class="modal-seat-item">
                    <div class="label">Số ghế đã chọn</div>
                    <div class="val" style="color: var(--accent);">G08, G09 (Đôi)</div>
                </div>
                <div class="modal-seat-item">
                    <div class="label">Mã vé điện tử</div>
                    <div class="val" style="font-family: monospace;">CG-DN-<?= rand(1000, 9999) ?></div>
                </div>
            </div>

            <div class="modal-total-row">
                <span class="total-label">Tổng thanh toán:</span>
                <span class="total-val" id="modalPrice">220.000đ</span>
            </div>

            <button class="btn-confirm-ticket" onclick="handleTicketCheckout()">
                🎟️ In & Xác Nhận Lấy Vé
            </button>
        </div>
    </div>

    <!-- ==========================================================================
         JAVASCRIPT TƯƠNG TÁC
         ========================================================================== -->
    <script>
        // 1. Quản lý Dark / Light Theme
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const htmlElement = document.documentElement;

        function applyTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            document.body.classList.toggle('theme-dark', theme === 'dark');
            document.body.classList.toggle('theme-light', theme === 'light');
            if (themeToggleBtn) {
                themeToggleBtn.textContent = theme === 'dark' ? '☀️' : '🌓';
                themeToggleBtn.setAttribute('title', theme === 'dark' ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối');
            }
        }

        // Đọc tùy chọn đã lưu hoặc theo hệ thống
        const savedTheme = localStorage.getItem('cinego_theme');
        if (savedTheme) {
            applyTheme(savedTheme);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark');
        }

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
                localStorage.setItem('cinego_theme', newTheme);
            });
        }

        // 2. Lọc phim theo thể loại (Genre Filter)
        const filterButtons = document.querySelectorAll('#genreFilterBar .filter-btn');
        const movieCards = document.querySelectorAll('#moviesGrid .mini-ticket-card');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const selectedGenre = btn.getAttribute('data-genre');
                movieCards.forEach(card => {
                    const cardGenres = card.getAttribute('data-genres').split(',');
                    if (selectedGenre === 'all' || cardGenres.includes(selectedGenre)) {
                        card.style.display = 'flex';
                        setTimeout(() => { card.style.opacity = '1'; card.style.transform = ''; }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => { card.style.display = 'none'; }, 200);
                    }
                });
            });
        });

        // 3. Chuyển đổi rạp chiếu tại Đà Nẵng (Cinema Tabs)
        const cinemaData = {
            'cgv-vincom': {
                name: 'CGV Vincom Đà Nẵng',
                address: 'Tầng 4 Vincom Plaza, 910A Ngô Quyền, Q. Sơn Trà, TP. Đà Nẵng',
                movies: [
                    { title: 'Trại Buôn Người', age: 'T18', dur: 120, times: ['10:15', '13:30', '16:45', '19:45', '22:15'] },
                    { title: 'Chị Chị Em Em 3', age: 'T18', dur: 118, times: ['11:00', '14:20', '17:30', '20:45'] },
                    { title: 'Clayface', age: 'T18', dur: 108, times: ['12:30', '15:45', '18:50', '21:30'] }
                ]
            },
            'lotte-danang': {
                name: 'Lotte Cinema Đà Nẵng',
                address: 'Tầng 5 Lotte Mart, 6 Nại Nam, P. Hòa Cường Bắc, Q. Hải Châu',
                movies: [
                    { title: 'Chị Chị Em Em 3', age: 'T18', dur: 118, times: ['10:00', '12:45', '16:15', '19:15', '22:00'] },
                    { title: 'Án Mạng Karaoke', age: 'T13', dur: 105, times: ['11:15', '14:00', '17:45', '20:30'] },
                    { title: 'Chàng Mèo Mang Mũ', age: 'P', dur: 95, times: ['09:45', '13:15', '15:30', '18:15'] }
                ]
            },
            'galaxy-ntp': {
                name: 'Galaxy Nguyễn Tri Phương',
                address: '86 Nguyễn Tri Phương, P. Chính Gián, Q. Thanh Khê',
                movies: [
                    { title: 'Clayface', age: 'T18', dur: 108, times: ['09:30', '12:00', '15:00', '18:30', '21:15'] },
                    { title: 'Trại Buôn Người', age: 'T18', dur: 120, times: ['10:45', '14:15', '17:15', '20:15'] },
                    { title: 'Án Mạng Karaoke', age: 'T13', dur: 105, times: ['13:00', '16:00', '19:00', '21:45'] }
                ]
            },
            'bhd-helio': {
                name: 'BHD Star Helio Center',
                address: 'Khu giải trí Helio, Đường 2 Tháng 9, Q. Hải Châu',
                movies: [
                    { title: 'Án Mạng Karaoke', age: 'T13', dur: 105, times: ['10:30', '13:45', '17:00', '20:15', '22:30'] },
                    { title: 'Trại Buôn Người', age: 'T18', dur: 120, times: ['11:30', '15:15', '18:45', '21:45'] },
                    { title: 'Chàng Mèo Mang Mũ', age: 'P', dur: 95, times: ['09:15', '12:30', '14:45', '17:15'] }
                ]
            },
            'cinestar-hc': {
                name: 'Cinestar Hải Châu',
                address: '55 Bạch Đằng, P. Hải Châu 1, Q. Hải Châu (Bờ sông Hàn)',
                movies: [
                    { title: 'Chàng Mèo Mang Mũ', age: 'P', dur: 95, times: ['09:30', '11:45', '14:00', '16:30', '18:45'] },
                    { title: 'Chị Chị Em Em 3', age: 'T18', dur: 118, times: ['10:45', '13:30', '17:15', '20:00'] },
                    { title: 'Clayface', age: 'T18', dur: 108, times: ['12:15', '15:45', '19:15', '21:45'] }
                ]
            }
        };

        const cinemaTabButtons = document.querySelectorAll('#cinemaTabs .cinema-tab-btn');
        const panelCinemaName = document.getElementById('panelCinemaName');
        const panelCinemaAddress = document.getElementById('panelCinemaAddress');
        const cinemaShowtimesList = document.getElementById('cinemaShowtimesList');

        cinemaTabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                cinemaTabButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const cid = btn.getAttribute('data-cinema-id');
                const info = cinemaData[cid];
                if (info) {
                    panelCinemaName.textContent = info.name;
                    panelCinemaAddress.textContent = info.address;

                    // Render lại hàng phim & chip giờ chiếu
                    let html = '';
                    info.movies.forEach(m => {
                        html += `
                            <div class="cinema-movie-row">
                                <div class="cinema-movie-info">
                                    <h4>${m.title}</h4>
                                    <div class="cinema-movie-meta">
                                        <span>${m.age}</span>
                                        <span>•</span>
                                        <span>${m.dur} phút</span>
                                        <span>•</span>
                                        <span>2D Phụ đề</span>
                                    </div>
                                </div>
                                <div class="time-chips-group">
                                    ${m.times.map(t => `
                                        <button class="time-chip" onclick="selectTimeChip(this, '${m.title}', '${t}', '${info.name}')">
                                            ${t}
                                            <span class="format">Phòng chiếu</span>
                                        </button>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    });
                    cinemaShowtimesList.innerHTML = html;
                }
            });
        });

        // 4. Bấm Chip giờ chiếu -> Mở Modal Đặt Vé
        function selectTimeChip(btn, movieTitle, time, cinemaName) {
            document.querySelectorAll('.time-chip').forEach(c => c.classList.remove('selected'));
            btn.classList.add('selected');
            openTicketModal(movieTitle, cinemaName, time, '110.000đ');
        }

        function switchDate(btn, label) {
            document.querySelectorAll('.date-picker-pills .date-pill').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
        }

        // 5. Quản lý Modal Vé Giấy
        const ticketModal = document.getElementById('ticketModal');
        const modalMovieTitle = document.getElementById('modalMovieTitle');
        const modalCinemaName = document.getElementById('modalCinemaName');
        const modalShowtime = document.getElementById('modalShowtime');
        const modalPrice = document.getElementById('modalPrice');

        function openTicketModal(title, cinema, time, price) {
            modalMovieTitle.textContent = title;
            modalCinemaName.textContent = cinema;
            modalShowtime.textContent = time + ' (Hôm nay)';
            modalPrice.textContent = price || '110.000đ';
            ticketModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeTicketModal() {
            ticketModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function closeModalOnBackdrop(e) {
            if (e.target === ticketModal) {
                closeTicketModal();
            }
        }

        function handleTicketCheckout() {
            alert('🎉 Chúc mừng! Tấm vé giấy điện tử của bạn đã được in và sẵn sàng!\nMã vé đã được đồng bộ vào hệ thống cụm rạp Đà Nẵng.');
            closeTicketModal();
        }

        // Đóng modal bằng phím ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && ticketModal.classList.contains('active')) {
                closeTicketModal();
            }
        });
    </script>
</body>
</html>
