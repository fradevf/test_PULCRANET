<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $number = $_POST["number"];
    $rate = $_POST["rate"];

    $message_error = [];
    if (!preg_match('/^(\d*\.\d{0,2}|\d+)$/', $number, $matches_number))
        $message_error[] = "ERRORE: Numero non corretto!";
    if (!preg_match('/^([A-Za-z]{3})$/', $rate, $matches_rate))
        $message_error[] = "ERRORE: Valuta non corretta!";
    if ($message_error && count($message_error) > 0) {
        echo json_encode($message_error);
        exit();
    }

    $changes = readXML();

    $all_currencies = [];
    foreach ($changes->Cube as $c) {
        $all_currencies[] = $c['currency'];
        if ($c['currency'] == $matches_rate[1]) {
            echo json_encode([
                'value' => number_format($matches_number[1] * floatval($c['rate']), 2),
                'rate' => $matches_rate[1],
                'date' => $changes['time']
            ]);
            exit();
        }
    }

    echo json_encode([
        'message' => "La valuta " . $matches_rate[1]
            . " non è disponibile.\nLe valute disponibili sono:\n"
            . implode("\n", $all_currencies)
            . "\n"
    ]);
    exit();
}

function readXML()
{
    $url = "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";
    $xml = simplexml_load_file($url);

    return $xml->Cube->Cube;
}

?>
