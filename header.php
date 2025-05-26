<header>
  <div class="header-container">
    <div class="header-left">
    <a class="logo"><img src="../image/Logo.png" alt="Logo Công ty" /></a>
    <h1>Báo Cáo Doanh Thu</h1>
    </div>
    <nav>
      <ul>
        <li><a href="/bao-cao-dich-vu" class="active">Báo cáo doanh thu</a></li>
        <li><a href="/xuat-bao-cao">Xuất báo cáo</a></li>
      </ul>
    </nav>
    <div class="header-user">
      <?php
      if (isset($_SESSION['username'])) {
            echo "<a href=''><button class='admin-btn'>👤 Xin chào, <b>" . htmlspecialchars($_SESSION['username']) . "</b></button></a>";
            echo '<a href="dangxuat.php"><button class="logout-btn">Đăng Xuất</button></a>';
        }
        ?>
    </div>
  </div>
</header>
