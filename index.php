<?php
  require __DIR__ . "/includes/header.php";
  require __DIR__ . "/includes/navbar.php";
  require __DIR__ . "/login.php";
?>
<div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="img/Banner1.png" class="d-block w-100" alt="โรงเรียนสุรศักดิ์มนตรี">
    </div>
  </div>
</div>

<div class="container">
    <div class="row">
        <?php
          if (isset($_SESSION["login"]) || $_SESSION["login"] === true) {
        ?>
        <div class="col-sm-12 d-flex justify-content-center py-3">
            <span class="fs-3">สวัสดี, <?= $_SESSION["name"] ?></span>
        </div>
        <div class="col-sm-12 d-flex justify-content-center py-3">
            <?php
            
            ?>
            <a href="time.php" class="w-50 btn btn-success fs-2">เช็คชื่อนักเรียน</a>
        </div>
        <div class="col-sm-12 d-flex justify-content-center py-3">
            <a href="classes.php" class="w-50 btn btn-info fs-3">เช็คข้อมูลห้องเรียน</a>
        </div>
        <?php
          }
          else {
        ?>
        <div class="col-sm-12 d-flex justify-content-center py-3">
            <a href="login.php" class="w-50 btn btn-primary fs-3">ลงชื่อเข้าใช้</a>
        </div>
        <?php
          }
        ?>
    </div>
</div>

<?php
    require __DIR__ . "/includes/scripts.php";
    require __DIR__ . "/includes/footer.php";
?>