<?php
function riksbank_soap($datefrom, $dateto) {
    $client = new SoapClient('https://swea.riksbank.se/sweaWS/wsdl/sweaWS_ssl.wsdl', array('soap_version' => SOAP_1_2));

    $searchGroupSeries = array(
        'groupid'  => '130',
        'seriesid' => 'SEKEURPMI'
    );

    $parameters = array(
        'searchRequestParameters' => array(
            'aggregateMethod'   => 'D',
            'datefrom'          => $datefrom,
            'dateto'            => $dateto,
            'languageid'        => 'en',
            'min'               => false,
            'avg'               => false,
            'max'               => false,
            'ultimo'            => false,
            'searchGroupSeries' => $searchGroupSeries
        )
    );

    try {
        $response = $client->getInterestAndExchangeRates($parameters);
    } catch (SoapFault $exception) {

        echo $exception;
    }

    $riksbankArray = array();

    foreach ($response->return->groups->series->resultrows as $resultrow) {
        $date = $resultrow->date;
        $value = $resultrow->value;
        $riksbankArray[$date] = $value;
    }
    return $riksbankArray;
}

function string_between_two_strings($str, $starting_word, $ending_word) {
    $arr = explode($starting_word, $str);
    if (isset($arr[1])) {
        $arr = explode($ending_word, $arr[1]);
        return $arr[0];
    }
    return '';
}

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}


function e2s_logic() {
    // https://wordpress.stackexchange.com/posts/384532/
    // nonce check
    // if (!wp_verify_nonce($_POST['nonce'], 'e2s-nonce')) {
    //     exit; // Get out of here, the nonce is rotten!
    // } else {
    $e2sinput = $_POST['e2s_input'][0]['value'];

    //Map lines of the string returned by file function to $rows array.
    $rows   = str_getcsv($e2sinput, "\n");

    //parse the items in rows 
    foreach ($rows as &$Row) $Row = str_getcsv($Row, ";");

    //Get the first row that is the HEADER row.
    $header_row = array_shift($rows);
    //This array holds the final response.
    $e2sinput_array    = array();
    foreach ($rows as $row) {
        if (!empty($row)) {
            $e2sinput_array[] = array_combine($header_row, $row);
        }
    }

    // Figure out the timespan we need data for
    $dateArray = array();
    foreach (array_keys($e2sinput_array) as $key) {
        $date = strtotime($e2sinput_array[$key]['Value date']);
        $date = date('Y-m-d', $date);
        $e2sinput_array[$key]['Value date'] = $date;
        $dateArray[] = $e2sinput_array[$key]['Value date'];
    }
    sort($dateArray);
    $datefrom = $dateArray[0];
    $dateto = $dateArray[array_key_last($dateArray)];

    // Get the exchange rates for the timespan we are interested in
    $riksbankArray = riksbank_soap($datefrom, $dateto);

    foreach (array_keys($e2sinput_array) as $key) {
        $e2s_purpose  = (!empty($e2sinput_array[$key]['Purpose'])  ? ' | ' . $e2sinput_array[$key]['Purpose']  : '');
        $e2s_account  = (!empty($e2sinput_array[$key]['Account'])  ? ' | IBAN: ' . $e2sinput_array[$key]['Account']     : '');
        $e2s_name     = (!empty($e2sinput_array[$key]['Name'])     ? $e2sinput_array[$key]['Name'] : '');

        // If "Originalbetrag" and "Wechselkurs" are present, N26 has already converted from SEK to EUR
        if (strpos($e2sinput_array[$key]['Purpose'], "Originalbetrag") !== false) {
            $amount_sek = string_between_two_strings($e2sinput_array[$key]['Purpose'], 'Originalbetrag: ', ' SEK');
            $exchangeRate = string_between_two_strings($e2sinput_array[$key]['Purpose'], 'Wechselkurs: ', ', ');
            $e2s_exchange_info = ' | ' . $e2sinput_array[$key]['Amount'] . ' EUR x ' . $exchangeRate . ' | ';
            // Remove N26 currency info
            $e2s_purpose = substr($e2s_purpose, strpos($e2s_purpose, "Transaction type") + 18);
        } else {
            // Otherwise we need to calculate it ourselves
            $date                 = $e2sinput_array[$key]['Value date'];
            $amount             = $e2sinput_array[$key]['Amount'];
            $exchangeRate        = $riksbankArray[$date];
            $amount_sek            = round(($amount * $exchangeRate), 2);
            $e2s_exchange_info    = ' | ' . $amount . ' EUR x ' . $exchangeRate;
            $e2s_purpose        = str_replace(', Transaction type: ', ' | ', $e2s_purpose);
        }
        $e2sinput_array[$key]['Amount']      = $amount_sek;
        $e2sinput_array[$key]['Currency'] = "SEK";

        $e2sinput_array[$key]['Purpose'] = $e2s_name . $e2s_exchange_info . $e2s_purpose . $e2s_account;

        unset($e2sinput_array[$key]['Date']);
        unset($e2sinput_array[$key]['Currency']);
        unset($e2sinput_array[$key]['Bank']);
        unset($e2sinput_array[$key]['Category']);
        unset($e2sinput_array[$key]['Name']);
        unset($e2sinput_array[$key]['Account']);
        unset($e2sinput_array[$key]['Bank']);
    }

    $buffer = fopen('php://temp', 'r+');
    $csv_header = array_keys($e2sinput_array[0]);

    fputs($buffer, implode(';', $csv_header) . "\n");
    foreach ($e2sinput_array as $line) {
        fputs($buffer, implode(';', $line) . "\n");
    }
    rewind($buffer);
    $csv = stream_get_contents($buffer);
    fclose($buffer);
    wp_send_json($csv);
}
// }


// }