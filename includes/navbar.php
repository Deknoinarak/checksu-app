<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center justify-content-center" href="#">
            <img src="../img/logo.png" alt="Logo" class="d-inline-block align-text-top me-3" width="30">
            ระบบทดสอบเช็คชื่อนักเรียน
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <?php
            if (isset($_SESSION["login"])) {
        ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="d-flex nav-item align-items-center justify-content-center">
                    <a class="nav-link" href="index.php">
                        หน้าแรก
                    </a>
                </li>
                <li class="d-flex nav-item align-items-center justify-content-center">
                    <a class="nav-link disabled" href="students.php">จัดการนักเรียน</a>
                </li>
                <li class="nav-item p-2">
                    <a class="w-100 btn btn-info" href="classes.php">เช็คข้อมูลห้องเรียน</a>
                </li>
                <li class="nav-item p-2">
                    <a class="w-100 btn btn-success" href="time.php">เริ่มเช็คชื่อ</a>
                </li>
            </ul>
            
            <a class="d-flex justify-content-center btn btn-outline-success" href="run.php?logout">ออกจากระบบ</a>
            <?php
                }
                else {
            ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    
                </ul>

                <a class="d-flex justify-content-center btn btn-outline-success" href="login.php">
                    ลงชื่อเข้าใช้
                </a>
            <?php
                }
            ?>
        </div>
    </div>
</nav>
<section id="content">

<div class="modal fade" id="alert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">แจ้งเตือน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-md-center">
                        <div class="col-sm-12 d-flex justify-content-center fa-5x alert-icon py-3">
                            <i class="fa-solid fa-circle-check fa-bounce text-success"></i>
                        </div>
                        <div class="col-sm-12 d-flex justify-content-center py-3">
                            <h1 class="text-success">
                                <?php
                                    echo $_SESSION["success"];
                                ?>
                            </h1>
                        </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <a class="btn btn-success" data-bs-dismiss="modal">เสร็จสิ้น</a>
            </div>
        </div>
    </div>
</div>
