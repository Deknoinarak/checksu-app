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
                        <span>วันที่ข้อมูลอัพเดท: <?= date("d\/m\/Y G:i:s") ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <!-- START Form -->
            <form action="run.php" method="post">
                <?php
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

                    $ranges = [
                        $_GET["select_classes"] . '!A4:E'
                    ];
                    $params = array(
                        'ranges' => $ranges,
                        "majorDimension" => "COLUMNS",
                    );
                    $response = $service->spreadsheets_values->batchGet($spreadsheetId, $params);
                    $values_stuno = $response->getValueRanges();

                    if (empty($values[0]["values"])) {
                    ?>
                        <h3 class="text-center pt-3">ขออภัย, ไม่พบข้อมูล</h3>
                    <?php
                    } else {
                        $count;
                        // print_r($values[0]);
                        // echo "<br>";
                        // echo "<br>";
                        // print_r($values_stuno[0]);
                        // echo "<br>";
                        // echo "<br>";

                        foreach ($values[0]["values"] as $col) {
                            $count = array(
                                'come' => [
                                    'values' => 0,
                                    'stuno' => [
                                        'name' => [],
                                        'no' => []
                                    ],
                                ],
                                'not' => [
                                    'values' => 0,
                                    'stuno' => [
                                        'name' => [],
                                        'no' => []
                                    ],
                                ],
                                'late' => [
                                    'values' => 0,
                                    'stuno' => [
                                        'name' => [],
                                        'no' => []
                                    ],
                                ],
                                'absent' => [
                                    'values' => 0,
                                    'stuno' => [
                                        'name' => [],
                                        'no' => []
                                    ],
                                ],
                            );
                            
                            // print_r($count["come"]["stuno"]);

                            // echo $values_stuno[0]["values"][0][33] . $values_stuno[0]["values"][1][33] .
                            // $values_stuno[0]["values"][2][33] . "\n" . $col[33];

                            foreach ($col as $x => $value_count) {
                                switch ($col[$x]) {
                                    case " ":
                                        $count['come']["values"]++;

                                        array_push($count['come']["stuno"]["name"],
                                        $values_stuno[0]["values"][2][$x] . $values_stuno[0]["values"][3][$x]
                                        . "\n" . $values_stuno[0]["values"][4][$x]);

                                        array_push($count['come']["stuno"]["no"], $values_stuno[0]["values"][0][$x]);
                                        break;
                                    case "ส":
                                        $count['late']["values"]++;

                                        array_push($count['late']["stuno"]["name"],
                                        $values_stuno[0]["values"][2][$x] . $values_stuno[0]["values"][3][$x]
                                        . "\n" . $values_stuno[0]["values"][4][$x]);

                                        array_push($count['late']["stuno"]["no"], $values_stuno[0]["values"][0][$x]);
                                        break;
                                    case "x":
                                        $count['not']["values"]++;

                                        array_push($count['not']["stuno"]["name"],
                                        $values_stuno[0]["values"][2][$x] . $values_stuno[0]["values"][3][$x]
                                        . "\n" . $values_stuno[0]["values"][4][$x]);

                                        array_push($count['not']["stuno"]["no"], $values_stuno[0]["values"][0][$x]);
                                        break;
                                    case "ล":
                                        $count['absent']["values"]++;

                                        array_push($count['absent']["stuno"]["name"],
                                        $values_stuno[0]["values"][2][$x] . $values_stuno[0]["values"][3][$x]
                                        . "\n" . $values_stuno[0]["values"][4][$x]);

                                        array_push($count['absent']["stuno"]["no"], $values_stuno[0]["values"][0][$x]);
                                        break;
                                }
                            }

                            // print_r($count['come_']["stuno"]);
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
                        <button type="button" data-bs-target="#come_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" data-bs-toggle="modal" class="card text-light text-start w-100" style="background-color: #25a244;">
                            <div class="card-body">
                                <h4 class="card-title">มา</h4>
                                <?= $count['come']["values"] ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-2 col-sm-3">
                        <button type="button" data-bs-target="#late_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" data-bs-toggle="modal" class="card text-dark text-start w-100" style="background-color: #ffb700;">
                            <div class="card-body">
                                <h4 class="card-title">สาย</h4>
                                <?= $count['late']["values"] ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-2 col-sm-3">
                        <button type="button" data-bs-target="#not_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" data-bs-toggle="modal" class="card text-light text-start w-100" style="background-color: #d90429;">
                            <div class="card-body">
                                <h4 class="card-title">ขาด</h4>
                                <?= $count['not']["values"] ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-2 col-sm-3">
                        <button type="button" data-bs-target="#absent_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" data-bs-toggle="modal" class="card text-light text-start w-100" style="background-color: #0191B4;">
                            <div class="card-body">
                                <h4 class="card-title">ลา</h4>
                                <?= $count['absent']["values"] ?>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="modal fade" id="come_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header text-light" style="background-color: #25a244;">
                                <h5 class="modal-title" id="exampleModalLabel">นักเรียนที่มา</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">เลขที่</th>
                                            <th scope="col">ชื่อ-นามสกุล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($count["come"]["stuno"]["name"] as $num => $row) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?= $count["come"]["stuno"]["no"][$num] ?></th>
                                            <td><?= $row ?></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex modal-footer justify-content-between">
                                <span class="text-secondary fs-6">นักเรียนมาทั้งหมด <?= $count["come"]["values"] ?> คน</span>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="late_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #ffb700;">
                                <h5 class="modal-title" id="exampleModalLabel">นักเรียนที่สาย</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">เลขที่</th>
                                            <th scope="col">ชื่อ-นามสกุล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($count["late"]["stuno"]["name"] as $num => $row) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?= $count["late"]["stuno"]["no"][$num] ?></th>
                                            <td><?= $row ?></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex modal-footer justify-content-between">
                                <span class="text-secondary fs-6">นักเรียนสายทั้งหมด <?= $count["late"]["values"] ?> คน</span>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="not_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header text-light" style="background-color: #d90429;">
                                <h5 class="modal-title" id="exampleModalLabel">นักเรียนที่ขาด</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">เลขที่</th>
                                            <th scope="col">ชื่อ-นามสกุล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($count["not"]["stuno"]["name"] as $num => $row) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?= $count["not"]["stuno"]["no"][$num] ?></th>
                                            <td><?= $row ?></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex modal-footer justify-content-between">
                                <span class="text-secondary fs-6">นักเรียนขาดทั้งหมด <?= $count["not"]["values"] ?> คน</span>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="absent_<?= str_replace("คาบ ", "", str_replace("/", "_", $col[count($col) - 1] . "_" .  $col[count($col) - 1 - 1])) ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header text-light" style="background-color: #0191B4;">
                                <h5 class="modal-title" id="exampleModalLabel">นักเรียนที่ลา</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">เลขที่</th>
                                            <th scope="col">ชื่อ-นามสกุล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($count["absent"]["stuno"]["name"] as $num => $row) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?= $count["absent"]["stuno"]["no"][$num] ?></th>
                                            <td><?= $row ?></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex modal-footer justify-content-between">
                                <span class="text-secondary fs-6">นักเรียนลาทั้งหมด <?= $count["absent"]["values"] ?> คน</span>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ปิด</button>
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