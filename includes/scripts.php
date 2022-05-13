<script>
    var apiKey = 'AIzaSyClKbHkE-5Ujcl8uKRmdr18fkGw9xGN1ao';
 
    var clientId = "939744552379-c71er7mhjph9ua4svmikvo8d51fagvtj.apps.googleusercontent.com"

    var appId = "939744552379";

    const scope = 'https://www.googleapis.com/auth/drive.file';

    const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/drive/v3/rest';

    var pickerApiLoaded = false;
    var oauthToken = "<?= $apiKey['access_token'] ?>";

    let tokenClient;
    let gapiInited = false;
    let gisInited = false;

    console.log("LoadApiPicker " + pickerApiLoaded);
    console.log(oauthToken);

    function loadPicker() {
        // gapi.load('client', {'callback': intializeGapiClient});
        gapi.load('picker', {'callback': onPickerApiLoad});
        console.log("End loadPicker");
    }

    async function intializeGapiClient() {
        console.log('Loaded GAPI');
        await gapi.client.init({
            apiKey: apiKey,
            discoveryDocs: [DISCOVERY_DOC],
        });
        gapiInited = true;
        handleAuthClick();
    }

    function gisLoaded() {
        console.log('Loaded GIS');
        tokenClient = google.accounts.oauth2.initTokenClient({
            client_id: clientId,
            scope: scope,
            callback: '', // defined later
        });
        gisInited = true;
    }

    function handleAuthClick() {
        tokenClient.callback = async (resp) => {
            if (resp.error !== undefined) {
                throw (resp);
            }
        };

        if (gapi.client.getToken() === null) {
            // Prompt the user to select an Google Account and asked for consent to share their data
            // when establishing a new session.
            tokenClient.requestAccessToken({prompt: 'consent'});
        }
        else {
            // Skip display of account chooser and consent dialog for an existing session.
            tokenClient.requestAccessToken({prompt: ''});
        }
    }

    function onPickerApiLoad() {
        pickerApiLoaded = true;
        console.log("PickerAPI " + pickerApiLoaded);
    }

    function createPicker() {
        console.log("First createPicker");
        // if (pickerApiLoaded && gapi.client.getToken() !== null) {
        if (pickerApiLoaded && oauthToken) {
            console.log("Running createPicker");
            var view = new google.picker.View(google.picker.ViewId.DOCS);
            view.setMimeTypes("application/vnd.google-apps.folder,application/vnd.google-apps.spreadsheet");
            var picker = new google.picker.PickerBuilder()
                .enableFeature(google.picker.Feature.NAV_HIDDEN)
                // .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
                .setAppId(appId)
                // .setOAuthToken(gapi.client.getToken().access_token)
                .setOAuthToken(oauthToken)
                .addView(view)
                // .addView(new google.picker.DocsUploadView())
                .setDeveloperKey(apiKey)
                .setCallback(pickerCallback)
                .build();
            picker.setVisible(true);
        }
    }

    function pickerCallback(data) {
        if (data.action == google.picker.Action.PICKED) {
            var fileId = data.docs[0].id;
            window.location.replace("https://ssm.checksu.cf:8080/run.php?file=" + fileId);
        }
    }
</script>

<script type="text/javascript" src="https://apis.google.com/js/api.js" onload="loadPicker()"></script>
<!-- <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script> -->
<?php
    if (isset($_GET["success"])) {
        if (isset($_SESSION["success"])) {
?>
<script>
    var alertModalToggle = document.querySelector('#alert')
    var alertModal = bootstrap.Modal.getOrCreateInstance(alertModalToggle)
    alertModal.toggle()
</script>
<?php
            unset($_SESSION["success"]);
        }
    }
?>