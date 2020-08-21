<?

require_once 'classes/cbrDailyApi.php';

try {
    $api = new cbrDailyApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}