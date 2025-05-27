<?php
session_start();
include 'kiemtra.php';
include 'ketnoi.php';
include 'xulySQL.php';

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Lấy bộ lọc từ GET
$time_filter = $_GET['time-filter'] ?? 'daily';
$room_filter = $_GET['room-filter'] ?? 'all';

// Lấy dữ liệu doanh thu phòng (sử dụng lại truy vấn trong xulySQL.php)
$time_column = 'DATE(hoadon.NgayXuatHoaDon)';
if ($time_filter == 'weekly') {
    $time_column = 'YEARWEEK(hoadon.NgayXuatHoaDon, 1)';
} elseif ($time_filter == 'monthly') {
    $time_column = 'DATE_FORMAT(hoadon.NgayXuatHoaDon, "%Y-%m")';
}

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
$data_phong = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy dữ liệu doanh thu dịch vụ
$sql_dichvu = "SELECT dv.TenDichVu, COUNT(hdv.MaHoaDon) AS SoLanSuDung, SUM(dv.GiaDichVu) AS DoanhThu
FROM hoadon hdv
JOIN dichvu dv ON FIND_IN_SET(dv.MaDichVu, hdv.DichVuKemTheo)
GROUP BY dv.MaDichVu, dv.TenDichVu
ORDER BY DoanhThu DESC";
$result_dichvu = $conn->query($sql_dichvu);
$data_dichvu = $result_dichvu->fetchAll(PDO::FETCH_ASSOC);

// Tạo file Excel
$spreadsheet = new Spreadsheet();

// Sheet 1: Doanh thu phòng
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Doanh Thu Phòng');
$sheet->setCellValue('A1', 'Loại Phòng');
$sheet->setCellValue('B1', 'Thời Gian');
$sheet->setCellValue('C1', 'Doanh Thu (VND)');
$rowNum = 2;
foreach ($data_phong as $row) {
    $sheet->setCellValue('A' . $rowNum, $row['LoaiPhong']);
    $sheet->setCellValue('B' . $rowNum, $row['ThoiGian']);
    $sheet->setCellValue('C' . $rowNum, $row['DoanhThu']);
    $rowNum++;
}

// Sheet 2: Doanh thu dịch vụ
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Doanh Thu Dịch Vụ');
$sheet2->setCellValue('A1', 'Tên Dịch Vụ');
$sheet2->setCellValue('B1', 'Số Lần Sử Dụng');
$sheet2->setCellValue('C1', 'Doanh Thu (VND)');
$rowNum = 2;
foreach ($data_dichvu as $row) {
    $sheet2->setCellValue('A' . $rowNum, $row['TenDichVu']);
    $sheet2->setCellValue('B' . $rowNum, $row['SoLanSuDung']);
    $sheet2->setCellValue('C' . $rowNum, $row['DoanhThu']);
    $rowNum++;
}

// Xuất file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="BaoCaoDoanhThu.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
