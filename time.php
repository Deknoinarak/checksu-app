<?php
    require __DIR__ . "/includes/header.php";
    require __DIR__ . "/includes/navbar.php";
    require __DIR__ . "/security.php";
    require __DIR__ . "/login.php";
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12 header p-3">
            <h1>โปรดเลือกห้องเรียน และวันที่ ที่ต้องการเช็คชื่อ</h1>
        </div>
        <?php
        if (!isset($_GET["select_classes"]) && !isset($_GET["date_date"])) {
        ?>
        <div class="col-md-6 col-sm-12">
            <h3>เลือกห้องเรียน</h3>
            <form action="time.php" method="GET">
                <select name="select_classes" id="select_classes" class="form-select">
                    <?php
                    $spreadsheet = new Google_Service_Sheets($client);
                    $spreadsheetId = $_SESSION["file"];
                    $response = $service->spreadsheets->get($spreadsheetId);

                    foreach ($response["sheets"] as $key => $value) {
                    ?>
                        <option value="<?= $response["sheets"][$key]["properties"]["title"] ?>">
                            มัธยมศึกษาปีที่ <?= str_replace("_", "/", $response["sheets"][$key]["properties"]["title"]) ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 col-sm-12">
                    <h3>เลือกวันที่</h3>
                    <input type="date" name="date_date" id="date_date" class="form-control" value="<?= date("Y\-m\-d") ?>">
            </div>
            <div class="col-sm-12 d-flex justify-content-end p-3">
                <button type="submit" class="btn btn-primary">
                    ดึงข้อมูลเพื่อเช็คชื่อ
                </button>
            </form>
        </div>
        <?php
        } else {
        ?>
        <div class="d-flex col-sm-12 justify-content-between align-items-center pb-3">
            <a class="btn btn-warning" href="?">
                < ย้อนกลับ
            </a>
            <div>
                <span>ห้องเรียน: มัธยมศึกษาปีที่ <?= str_replace("_", "/", $_GET["select_classes"]) ?></span>
                <span>วันที่เช็คชื่อ: <?= date("d\/m\/Y", strtotime($_GET["date_date"])) ?></span>
            </div>
        </div>
        <?php
        }
        if (isset($_GET["select_classes"]) && isset($_GET["date_date"])) {
        ?>
        <form action="run.php" method="post">
            <input type="hidden" name="select_classes" value="<?= $_GET["select_classes"] ?>">
            <input type="hidden" name="date_date" value="<?= $_GET["date_date"] ?>">
        <div class="d-flex justify-content-center row">
            <?php
                $service = new Google_Service_Sheets($client);
                $range = $_GET["select_classes"] . '!A4:E1000';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $values = $response->getValues();

                $values = array_slice($values, 0, -4);

                if (empty($values)) {
                    print "ไม่พบข้อมูล\n";
                } else {
                    foreach ($values as $row) {
            ?>
            <div class="col-sm-12 py-3">
                <div class="d-flex row justify-content-center align-items-center">
                    <div class="col-sm-4">
                        <h5><?= $row[0] ?>. <?= $row[2] ?><?= $row[3] ?> <?= $row[4] ?></h5>
                    </div>
                    <input type="hidden" value="<?= $row[1] ?>" name="<?= $row[1] ?>">

                    <div class="d-flex col-sm-12 col-md-8 justify-content-center align-items-center">
                        <div class="d-flex select-absent-status w-100">
                                <div>
                                    <input type="radio" class="form-check-input" name="radio_<?= $row[1] ?>" id="come_<?= $row[1] ?>" value=" " checked>
                                    <label class="form-check-label" for="come_<?= $row[1] ?>">
                                        มาเรียน
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" class="form-check-input" name="radio_<?= $row[1] ?>" id="late_<?= $row[1] ?>" value="ส">
                                    <label class="form-check-label" for="late_<?= $row[1] ?>">
                                        สาย
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" class="form-check-input" name="radio_<?= $row[1] ?>" id="not_<?= $row[1] ?>" value="x">
                                    <label class="form-check-label" for="not_<?= $row[1] ?>">
                                        ขาดเรียน
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" class="form-check-input" name="radio_<?= $row[1] ?>" id="absent_<?= $row[1] ?>" value="ล">
                                    <label class="form-check-label" for="absent_<?= $row[1] ?>">
                                        ลา
                                    </label>
                                </div>
                            </tbody>
                        </div>
                    </div>   
                </div>
            </div>
            <?php
                    }
                }
            ?>

            <div class="col-sm-12">
                <div class="row d-flex justify-content-between align-items-center">
                    <div class="col-sm-12 col-md-4">
                        <h3>ลงชื่อครูผู้สอน</h3>
                        <select name="select_tc" id="select_tc" class="form-select">
                        <?php
                            $service = new Google_Service_Sheets($client);

                            $range = 'TC!A2:C';
                            $spreadsheetId = $_SESSION["file_data"];
                            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                            $values = $response->getValues();

                            foreach ($values as $row) {
                        ?>
                            <option value="<?= $row[0] . $row[1] ?>">
                                <?= $row[0] . $row[1] . " " . $row[2] ?>
                            </option>
                        <?php
                            }
                        ?>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <h3>คาบเรียน</h3>
                        <select name="select_section" id="select_section" class="form-select">
                        <?php
                            $service = new Google_Service_Sheets($client);

                            $range = 'Section!A2:A';
                            $spreadsheetId = $_SESSION["file_data"];
                            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                            $values = $response->getValues();

                            foreach ($values as $row) {
                        ?>
                            <option value="<?= $row[0] . $row[1] ?>">
                                <?= $row[0] . $row[1] . " " . $row[2] ?>
                            </option>
                        <?php
                            }
                        ?>
                        </select>
                    </div>
                    <div class="d-flex col-sm-12 col-md-2 py-3 justify-content-end">
                        <button type="submit" name="submit_time" class="btn btn-success">
                            ยืนยันการเช็คชื่อ
                        </button>
                    </div>
                </div>
            </div>

        </form>
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