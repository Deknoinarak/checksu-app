<?php
    require __DIR__ . "/login.php";

    if (isset($_GET["logout"])) {
        if (file_exists(__DIR__ . "/token.json")) {
            unlink(__DIR__ . "/token.json");
        }
        unset($_SESSION["login"]);
        unset($_SESSION["token"]);

        header("Location: index.php");
    }

    else if (isset($_POST["submit_time"])) {
        $col_num = array("F", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S",
        "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH",
        "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV",
        "AW", "AX", "AY", "AZ", "BA", "BC", "BC");

        $range_before = $_POST["select_classes"] . "!F4:BC4";
        $range_stuno = $_POST["select_classes"] . "!B4:B1000";

        $service = new Google_Service_Sheets($client);
        
        $result = $service->spreadsheets_values->get($spreadsheetId, $range_before);
        $numRows = $result->getValues() != null ? count($result->getValues()[0]) : 0;

        $student_num_q = $service->spreadsheets_values->get($spreadsheetId, $range_stuno);
        $student_num = $student_num_q->getValues();

        $values = [
            
        ];

        $date_sep = explode("-", $_POST["date_date"]);
        $date = $date_sep[2] . "/" . $date_sep[1] . "/" . $date_sep[0] + 543;
        
        foreach ($student_num as $row) {
            array_push($values, [$_POST["radio_".$row[0]]]);
        }

        array_push($values, [$_POST["select_tc"]]);
        array_push($values, []);
        array_push($values, [$_POST["select_section"]]);
        array_push($values, [$date]);

        $range = $_POST["select_classes"] . "!" . $col_num[$numRows + 1] . "4:" . $col_num[$numRows + 1];

        $data = [];
        $data[] = new Google_Service_Sheets_ValueRange([
            'range' => $range,
            'values' => $values
        ]);
        // Additional ranges to update ...
        $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
            'valueInputOption' => "RAW",
            'data' => $data
        ]);
        $result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);

        $_SESSION["success"] = "บันทึกข้อมูลสำเร็จ";
        header("Location: time.php?success");
    }

    else {
        header("Location: index.php");
    }