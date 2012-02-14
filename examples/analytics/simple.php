<?php
require_once '../../src/apiClient.php';
require_once '../../src/contrib/apiAnalyticsService.php';
session_start();

$datajson = "";
$client = new apiClient();
$client->setApplicationName("Google Analytics PHP Starter Application");

// Visit https://code.google.com/apis/console?api=analytics to generate your
// client id, client secret, and to register your redirect uri.
// $client->setClientId('insert_your_oauth2_client_id');
// $client->setClientSecret('insert_your_oauth2_client_secret');
// $client->setRedirectUri('insert_your_oauth2_redirect_uri');
// $client->setDeveloperKey('insert_your_developer_key');
$service = new apiAnalyticsService($client);

if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  
  $ids = "ga:51312295";
    $start_date = "2012-01-01";
    $end_date = "2012-01-14";
    $metrics = "ga:visitors,ga:pageviews";
    // $metrics = "ga:visitors,ga:visits,ga:pageviews";
    $dimensions = "ga:date";
    $optParams = array('dimensions' => $dimensions);
$data = $service->data_ga->get($ids,$start_date,$end_date,$metrics,$optParams);
  $datajson = json_encode(($data['rows']));

echo '<pre>'; 
print_r($datajson);  
echo '</pre>';   
 }
 else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
}
?>
    <head>
      <script type="text/javascript" src="https://www.google.com/jsapi"></script>
      <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
          var data = new google.visualization.DataTable();
          data.addColumn('number', 'Pocet navstevniku');
          data.addColumn('number', 'Pocet navstevniku');
          data.addColumn('number', 'Zobrazeno strane');
          data.addRows(<?=str_replace('"','',$datajson);?>);

          var options = {
            width: 400, height: 240,
            title: 'Prehled navstevnosti'
          };

          var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
          chart.draw(data, options);
        }
      </script>
    </head>
    <body>
      <div id="chart_div"></div>
    </body>
  </html>

