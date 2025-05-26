<?php
session_start();
include 'kiemtra.php';
include 'ketnoi.php';
include 'xulySQL.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Báo Cáo Doanh Thu</title>
    <link rel="stylesheet" href="styleDoanhThu.css">
</head>
<body>
   <?php include 'header.php'; ?> 

    <main>
        <section class="report-section">
            <h2>Báo Cáo Doanh Thu</h2>

            <!-- Form lọc dữ liệu -->
                <form method="GET" class="filter-section">
                    <div class="filter-group">
                        <label for="time-filter">Chọn Thời Gian</label>
                        <select id="time-filter" name="time-filter">
                            <option value="daily" <?php echo $time_filter == 'daily' ? 'selected' : '' ?>>Theo Ngày</option>
                            <option value="weekly" <?php echo $time_filter == 'weekly' ? 'selected' : '' ?>>Theo Tuần</option>
                            <option value="monthly" <?php echo $time_filter == 'monthly' ? 'selected' : '' ?>>Theo Tháng</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="room-filter">Loại Phòng</label>
                        <select id="room-filter" name="room-filter">
                            <option value="all" <?php echo $room_filter == 'all' ? 'selected' : '' ?>>Tất Cả</option>
                            <option value="Đơn" <?php echo $room_filter == 'Đơn' ? 'selected' : '' ?>>Phòng Đơn</option>
                            <option value="Đôi" <?php echo $room_filter == 'Đôi' ? 'selected' : '' ?>>Phòng Đôi</option>
                            <option value="Cao cấp" <?php echo $room_filter == 'Cao cấp' ? 'selected' : '' ?>>Phòng Cao cấp</option>
                        </select>
                    </div>
                    <button type="submit">Lọc</button>
                    <button type="submit" name="clear_filter" value="1">Xóa bộ lọc</button>
                </form>

            <!-- Ô tìm kiếm nhanh trên bảng -->
            <div style="margin-bottom: 5px;">
                <input type="text" id="searchInput" placeholder="Tìm kiếm..." style="padding:6px; border-radius:3px; border:1px solid #ccc; width: 250px;">
            </div>
            <!-- Bảng doanh thu theo phòng -->
            <h3>Doanh Thu Theo Phòng</h3>
            <div class="report-data">
        <?php if ($stmt->rowCount() > 0): ?>
        <table>
                <thead>
                    <tr>
                        <th>Loại Phòng</th>
                        <th>Thời Gian</th>
                        <th>Doanh Thu (VND)</th>
                    </tr>
                </thead>
            <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['LoaiPhong']); ?></td>
                        <td><?php echo htmlspecialchars($row['ThoiGian']); ?></td>
                        <td><?php echo number_format($row['DoanhThu'], 0, ',', '.'); ?></td>
                    </tr>
        <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Không có dữ liệu doanh thu.</p>
        <?php endif; ?>
            </div>           

            <!-- Bảng doanh thu dịch vụ -->
            <div class="report-data">
            <h3 style="margin-top: 30px;">Doanh Thu Theo Dịch Vụ</h3>
            <?php if ($result_dichvu && $result_dichvu->rowCount() > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Tên Dịch Vụ</th>
                    <th>Số Lần Sử Dụng</th>
                    <th>Doanh Thu (VND)</th>
                </tr>
            </thead>
        <tbody>
            <?php while ($row = $result_dichvu->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['TenDichVu']); ?></td>
                    <td><?php echo htmlspecialchars($row['SoLanSuDung']); ?></td>
                    <td><?php echo number_format($row['DoanhThu'], 0, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        </table>
        <?php else: ?>
            <p>Không có dữ liệu doanh thu dịch vụ.</p>
        <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
    // ----------- TÌM KIẾM DỮ LIỆU TRONG BẢNG ------------

    document.getElementById('searchInput').addEventListener('keyup', function() {
        var filter = this.value.toLowerCase(); // Chuyển giá trị nhập thành chữ thường để so sánh
        var rows = document.querySelectorAll('.report-data tbody tr'); // Lấy tất cả dòng trong bảng
        rows.forEach(function(row) {
            var text = row.textContent.toLowerCase(); // Lấy toàn bộ nội dung dòng
            // Hiển thị dòng nếu có chứa từ khóa tìm kiếm, ngược lại ẩn dòng
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
