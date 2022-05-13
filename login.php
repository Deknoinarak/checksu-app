<?php
    session_start();

    require __DIR__ . '/vendor/autoload.php';

    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->addScope("profile");
    $client->addScope("email");
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    function getClient() {
        if (isset($_SESSION["token"]) && $_SESSION["login"] = true) {
            $accessToken = json_decode($_SESSION["token"], true);
            $client->setAccessToken($accessToken);
        }
        else {
            header("Location: login.php");
        }

        return $client;
    }
        
        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        if (isset($_GET["code"])) {

            echo $_GET["code"];

            // If there is no previous token or it's expired.
            if ($client->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                }
                else {
                    $authCode = $_GET["code"];

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
                        throw new Exception(join(', ', $accessToken));
                    }
                }

                $_SESSION["token"] = json_encode($client->getAccessToken());
            }

            echo "Passed!";

            $_SESSION["login"] = true;
            header("Location: index.php");
        }
        else {
            if (isset($_SESSION["token"])) {
                $accessToken = json_decode($_SESSION["token"], true);
                $client->setAccessToken($accessToken);

                $_SESSION["login"] = true;
                if ($_SERVER["PHP_SELF"] == "/login.php") {
                    header("Location: index.php");
                }
            }
            else {
                if ($_SERVER["PHP_SELF"] == "/login.php") {
                    header("Location: " . $client->createAuthUrl());
                }
            }
        }

        if (isset($_SESSION["login"]) || $_SESSION["login"] === true) {
            $gauth = new Google_Service_Oauth2($client);
            $google_info = $gauth->userinfo->get();
            $_SESSION["name"] = $google_info->name;
        }

    $service = new Google_Service_Sheets($client);

    $spreadsheetId = "12ZCA9q7XsazlcMDxCwMIgTQTAN8VKeGXyoYVayDFlLQ";
    $_SESSION["file"] = "12ZCA9q7XsazlcMDxCwMIgTQTAN8VKeGXyoYVayDFlLQ";
    $spreadsheetId_data = "1nOHOIh6Ypmlbuw43dxSslGJwyQGRLqehgw936nCvElg";
    $_SESSION["file_data"] = "1nOHOIh6Ypmlbuw43dxSslGJwyQGRLqehgw936nCvElg";

    foreach(json_decode($_SESSION["token"], true) as $key => $value)
    {
        $apiKey[$key] = $value;
    }