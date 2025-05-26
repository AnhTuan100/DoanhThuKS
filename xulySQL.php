<?php
// Bắt đầu session nếu chưa bắt đầu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Xử lý lưu bộ lọc vào session khi submit form
if (isset($_GET['time-filter'])) {
    $_SESSION['selectedThoiGian'] = $_GET['time-filter'];
}
if (isset($_GET['room-filter'])) {
    $_SESSION['selectedLoaiPhong'] = $_GET['room-filter'];
}

// Lấy giá trị bộ lọc từ GET hoặc session, mặc định 'monthly' và 'all'
$time_filter = $_GET['time-filter'] ?? ($_SESSION['selectedThoiGian'] ?? 'monthly');
$room_filter = $_GET['room-filter'] ?? ($_SESSION['selectedLoaiPhong'] ?? 'all');

// Xóa bộ lọc khi nhấn nút "Xóa bộ lọc"
if (isset($_GET['clear_filter'])) {
    unset($_SESSION['selectedThoiGian'], $_SESSION['selectedLoaiPhong']);
    header("Location: DoanhThu.php");
    exit;
}

// Xác định cột thời gian theo bộ lọc
switch ($time_filter) {
    case 'daily':
        $time_column = "DATE(NgayXuatHoaDon)";
        break;
    case 'weekly':
        $time_column = "CONCAT(YEAR(NgayXuatHoaDon), '-Tuần ', LPAD(WEEK(NgayXuatHoaDon), 2, '0'))";
        break;
    default:
        $time_column = "DATE_FORMAT(NgayXuatHoaDon, '%Y-%m')";
}

// Câu truy vấn doanh thu dịch vụ
$sql_dichvu = "SELECT 
    dv.MaDichVu,
    dv.TenDichVu,
    COUNT(hdv.MaDV) AS SoLanSuDung,
    COUNT(hdv.MaDV) * dv.GiaDichVu AS DoanhThu
FROM dichvu dv
LEFT JOIN (
    SELECT 
        MaHoaDon,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(DichVuKemTheo, ',', numbers.n), ',', -1)) AS MaDV
    FROM hoadon
    CROSS JOIN (
        SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
    ) numbers
    WHERE DichVuKemTheo IS NOT NULL
      AND LENGTH(DichVuKemTheo) - LENGTH(REPLACE(DichVuKemTheo, ',', '')) >= numbers.n - 1
) AS hdv ON dv.MaDichVu = hdv.MaDV
GROUP BY dv.MaDichVu, dv.TenDichVu
ORDER BY DoanhThu DESC";
$result_dichvu = $conn->query($sql_dichvu);

// Câu truy vấn lấy doanh thu theo phòng
$sql = "SELECT 
            $time_column AS ThoiGian, 
            phong.LoaiPhong,
            SUM(hoadon.TongTien) AS DoanhThu 
        FROM hoadon
        INNER JOIN datphong ON hoadon.MaDatPhong = datphong.MaDatPhong
        INNER JOIN phong ON datphong.MaPhong = phong.MaPhong";
if ($room_filter != 'all') {
    $sql .= " WHERE phong.LoaiPhong = :room_filter";
}
$sql .= " GROUP BY ThoiGian, phong.LoaiPhong ORDER BY ThoiGian, phong.LoaiPhong";
$stmt = $conn->prepare($sql);
if ($room_filter != 'all') {
    $stmt->bindParam(':room_filter', $room_filter);
}
$stmt->execute();

?>
