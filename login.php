<?php
    // * * ///////////// * * //  
    // * * START SESSION * * //
    // * * ///////////// * * //
    session_start();
    date_default_timezone_set('Asia/Bangkok');

    require __DIR__ . '/vendor/autoload.php';

    $client = new Google_Client();
    $client->setApplicationName('SSM Student Attendance Project');
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);
    $client->addScope("profile");
    $client->addScope("email");
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    if (isset($_SESSION["token"])) {
        $accessToken = json_decode($_SESSION["token"], true);
        $client->setAccessToken($accessToken);

        // echo "Login";
        $_SESSION["login"] = true;
    }
    else if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        }
        else {
            if (isset($_GET["code"])) {
                if ($_SERVER["PHP_SELF"] == "/login.php") {
                    $authCode = $_GET["code"];

                    echo $authCode;

                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    if (array_key_exists('error', $accessToken)) {
                        throw new Exception(join(', ', $accessToken));
                    }

                    $_SESSION["token"] = json_encode($client->getAccessToken());
                    $_SESSION["login"] = true;
                    header("Location: index.php");
                }
            }
            else {
                if ($_SERVER["PHP_SELF"] !== "/index.php") {
                    header("Location: " . $client->createAuthUrl());
                }
            }
        }
    }

    // * * //////////////////////// * * //
    // * * Login Essential Variable * * //
    // * * //////////////////////// * * //

    if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
        // * * GOOGLE OAUTH2 SERVICE * * //
        $gauth = new Google_Service_Oauth2($client);
        $google_info = $gauth->userinfo->get();
        $_SESSION["name"] = $google_info->name;

        // * * GOOGLE SHEETS SERVICE * * //
        $service = new Google_Service_Sheets($client);

        // * * SET Spreadsheet ID To Session * * //
        $spreadsheetId = "12ZCA9q7XsazlcMDxCwMIgTQTAN8VKeGXyoYVayDFlLQ";
        $_SESSION["file"] = "12ZCA9q7XsazlcMDxCwMIgTQTAN8VKeGXyoYVayDFlLQ";
        $spreadsheetId_data = "1nOHOIh6Ypmlbuw43dxSslGJwyQGRLqehgw936nCvElg";
        $_SESSION["file_data"] = "1nOHOIh6Ypmlbuw43dxSslGJwyQGRLqehgw936nCvElg";

        // * * DATE DATA * * //
        $_SESSION["A_DATE"] = date("a");

        // * * DATE Add-Ons Function * * //
        function getAmPm($am_message, $pm_message) {
            $return_message = "";
            switch ($_SESSION["A_DATE"]) {
                case "pm":
                    $return_message = $pm_message;
                    break;
                case "am":
                    $return_message = $am_message;
                    break;
                default:
                    $return_message = "";
            }
            
            return $return_message;
        }
    }