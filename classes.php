<?php
    require __DIR__ . "/includes/header.php";
    require __DIR__ . "/includes/navbar.php";
    require __DIR__ . "/security.php";
    require __DIR__ . "/login.php";
?>
<div class="container">
    <div class="row">
        <div class="col-sm-12 header p-3">
            <h1> <?php
                if(isset($_GET["select_classes"]))
                    echo "สรุปข้อมูลห้องเรียน";
                else
                    echo "โปรดเลือกห้องเรียน ที่ต้องการเช็คข้อมูลห้องเรียน";
            ?> </h1>
        </div>
        
        <!-- START Header Choose Classes -->
        <?php
        // ** START Check if there was no GET "sclect_classes"
        if (!isset($_GET["select_classes"])) {
        ?>
        <div class="col-sm-12">
            <h3>เลือกห้องเรียน</h3>
            <form action="classes.php" method="GET">
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
            <div class="col-sm-12 d-flex justify-content-end p-3">
                <button type="submit" class="btn btn-primary">
                    เช็คข้อมูลห้องเรียน
                </button>
            </form>
        </div>
        <!-- END Header Choose Classes -->
        <?php
        }
        // ** END Check if there was no GET "sclect_classes"
        /////////////////////////////////////////////////////
        // ** START if there were GET "sclect_classes"
        else {
            $service = new Google_Service_Sheets($client);

            $result = $service->spreadsheets_values->get($_SESSION["file"], $_GET["select_classes"] . "!A4:A");
            $numRows = $result->getValues() != null ? count($result->getValues()) : 0;
            $numRows -= 4;
        ?>
        <!-- START Content Section -->
        <div class="col-sm-12 pb-3">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col-sm-12 col-md-2">
                    <a class="btn btn-link w-100" href="?">
                        < ย้อนกลับ
                    </a>
                </div>

                <div class="col-sm-12 col-md-8">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>ห้องเรียน: มัธยมศึกษาปีที่ <?= str_replace("_", "/", $_GET["select_classes"]) ?></span>
                        <span class="px-2 fs-4">|</span>
                        <span>จำนวนนักเรียน: <?= str_replace("_", "/", $numRows) ?> คน</span>
                        <span class="px-2 fs-4">|</span>
                        <span>วันที่ข้อมูลอัพเตท: <?= date("d\/m\/Y G:i:s") ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <!-- START Form -->
            <form action="run.php" method="post">
                <?php
                    // $col_num = array("F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S",
                    // "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH",
                    // "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV",
                    // "AW", "AX", "AY", "AZ", "BA", "BC", "BC");

                    $service = new Google_Service_Sheets($client);
                    $range = $_GET["select_classes"] . '!A4:BC';
                    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                    $values = $response->getValues();

                    $ranges = [
                        $_GET["select_classes"] . '!F4:BC'
                    ];
                    $params = array(
                        'ranges' => $ranges,
                        "majorDimension" => "COLUMNS",
                    );
                    $response = $service->spreadsheets_values->batchGet($spreadsheetId, $params);
                    $values = $response->getValueRanges();

                    if (empty($values[0]["values"])) {
                    ?>
                        <h3 class="text-center pt-3">ขออภัย, ไม่พบข้อมูล</h3>
                    <?php
                    } else {
                        // print_r($values[0]["values"][0]);
                        // echo "<br>";

                        foreach ($values[0]["values"] as $col) {
                            $count_come = 0;
                            $count_not = 0;
                            $count_late = 0;
                            $count_absent = 0;
                            foreach ($col as $x => $value_count) {
                                switch ($col[$x]) {
                                    case " ":
                                        $count_come++;
                                        break;
                                    case "ส":
                                        $count_late++;
                                        break;
                                    case "x":
                                        $count_not++;
                                        break;
                                    case "ล":
                                        $count_absent++;
                                        break;
                                }
                            }
                ?>
                <div class="d-flex row justify-content-center align-items-center py-3">
                    <div class="col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">วันที่ / คาบเรียน / ครูผู้สอน</h4>
                                <?= $col[count($col) - 1] ?> / <?= $col[count($col) - 1 - 1] ?> / <?= $col[count($col) - 1 - 3] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-3">
                        <div class="card text-light" style="background-color: #25a244;">
                            <div class="card-body">
                                <h4 class="card-title">มา</h4>
                                <?= $count_come ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-3">
                        <div class="card" style="background-color: #ffb700;">
                            <div class="card-body">
                                <h4 class="card-title">สาย</h4>
                                <?= $count_late ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-3">
                        <div class="card text-light" style="background-color: #d90429;">
                            <div class="card-body">
                                <h4 class="card-title">ขาด</h4>
                                <?= $count_not ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-3">
                        <div class="card text-light" style="background-color: #0191B4;">
                            <div class="card-body">
                                <h4 class="card-title">ลา</h4>
                                <?= $count_absent ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                        }
                    }
                ?>
            </form>
            <!-- END Form -->
            <!-- END Content Section -->
        </div>
        <?php
        }
        // ** END if there were GET "sclect_classes"
        ?>

    </div>
</div>

<?php
    require __DIR__ . "/includes/scripts.php";
    require __DIR__ . "/includes/footer.php";
?>