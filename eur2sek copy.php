<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       EUR2SEK
 * Plugin URI:        https://digitalerdurchbruch.de
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Karl Friedrich
 * Author URI:        https://digitalerdurchbruch.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       eur2sek
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('EUR2SEK_VERSION', '1.0.0');

/**
 * [e2s] returns html
 * @return string EUR2SEK HTML
 */

add_shortcode('e2s', 'e2s_html');

function e2s_init() {

	// function readCSV($csvFile) {
	// 	$aryData = [];
	// 	$output = [];
	// 	$header = NULL;
	// 	if ($csvFile) {
	// 		$header = str_getcsv($csvFile, ";");
	// 		while str_getcsv($csvFile, ";");
	// 		foreach ($header as $key => $label) {
	// 			$output[$label] = array_column($aryData, $key);
	// 		}
	// 	}
	// 	return $output;
	// }
	function string_between_two_strings($str, $starting_word, $ending_word) {
		$arr = explode($starting_word, $str);
		if (isset($arr[1])) {
			$arr = explode($ending_word, $arr[1]);
			return $arr[0];
		}
		return '';
	}

	function e2s_html() {
		ob_start();
?>
		<div id="e2s">
			<form name="e2s-input" id="e2s-input" method="post" action="" autocomplete="off">
				<textarea style="width: 100%; height: 400px;" name="e2s-textarea" placeholder="Drop your CSV here" value="<?php echo isset($_POST['e2s-input']) ? esc_attr($_POST['e2s-input']) : ''; ?>"></textarea>
				<input name="e2s-convert" type="submit" value="Convert">
				<?php wp_nonce_field('test-nonce-action', 'test_nonce_field'); ?>
			</form>
			</section>
			<br />
	<?php
		$first_step = ob_get_clean();

		if (isset($_POST['e2s-convert'])) {
			ob_start();

			// https://wordpress.stackexchange.com/posts/384532/
			// nonce check
			$nonce = $_POST["test_nonce_field"];
			if (!wp_verify_nonce($nonce, 'test-nonce-action')) {
				exit; // Get out of here, the nonce is rotten!
			} else {

				$e2sinput = $_POST['e2s-textarea'];
				// $e2sinput_csv = str_getcsv($e2sinput, ";");

				// $e2sinput_csv = array_map('str_getcsv', $e2sinput);
				// array_walk($e2sinput_csv, function (&$a) use ($e2sinput_csv) {
				// 	$a = array_combine($e2sinput_csv[0], $a);
				// });
				// array_shift($e2sinput_csv); # remove column header

				// print_r($e2sinput, true);

				// print_r(readCSV($e2sinput, ";"));

				//Map lines of the string returned by file function to $rows array.
				$rows   = str_getcsv($e2sinput, "\n");

				foreach ($rows as &$Row) $Row = str_getcsv($Row, ";"); //parse the items in rows 

				// echo '<br><br>';
				// echo 'rows';
				// echo '<pre>' . print_r($rows, true) . '</pre>';

				//Get the first row that is the HEADER row.
				$header_row = array_shift($rows);
				//This array holds the final response.
				$e2sinput_array    = array();
				foreach ($rows as $row) {
					if (!empty($row)) {
						$e2sinput_array[] = array_combine($header_row, $row);
					}
				}

				// echo '<br><br>';
				// echo 'e2sinput_array';
				// echo '<pre>' . print_r($e2sinput_array, true) . '</pre>';

				// foreach ($e2sinput_array as $e2sinput_array_row) {
				// 	unset($e2sinput_array_row['Date']);
				// }

				$dateArray = array();
				foreach (array_keys($e2sinput_array) as $key) {
					$date = strtotime($e2sinput_array[$key]['Value date']);
					$date = date('Y-m-d', $date);
					$e2sinput_array[$key]['Value date'] = $date;
					$dateArray[] = $e2sinput_array[$key]['Value date'];
				}
				sort($dateArray);
				// echo '<br><br>';
				// echo 'dateArray';
				// echo '<pre>' . print_r($dateArray, true) . '</pre>';


				$client = new SoapClient('https://swea.riksbank.se/sweaWS/wsdl/sweaWS_ssl.wsdl', array('soap_version' => SOAP_1_2));

				$searchGroupSeries = array(
					'groupid'  => '130',
					'seriesid' => 'SEKEURPMI'
				);

				$parameters = array(
					'searchRequestParameters' => array(
						'aggregateMethod'   => 'D',
						'datefrom'          => $dateArray[0],
						'dateto'            => $dateArray[array_key_last($dateArray)],
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

				// return $resultArray;
				// echo '<pre>' . print_r($riksbankArray, true) . '</pre>';
				// echo '<pre>' . print_r($parameters, true) . '</pre>';




				foreach (array_keys($e2sinput_array) as $key) {
					$e2s_purpose  = (!empty($e2sinput_array[$key]['Purpose'])  ? ' | ' . $e2sinput_array[$key]['Purpose']  : '');
					$e2s_amount   = (!empty($e2sinput_array[$key]['Amount'])   ? $e2sinput_array[$key]['Amount']   : '');

					$e2s_category = (!empty($e2sinput_array[$key]['Category']) ? ' | Cat: '  . $e2sinput_array[$key]['Category'] : '');
					$e2s_account  = (!empty($e2sinput_array[$key]['Account'])  ? ' | IBAN: ' . $e2sinput_array[$key]['Account']     : '');
					$e2s_name     = (!empty($e2sinput_array[$key]['Name'])     ? $e2sinput_array[$key]['Name'] : '');


					if (strpos($e2sinput_array[$key]['Purpose'], "Originalbetrag") !== false) {
						// If "Originalbetrag" and "Wechselkurs" are present, N26 has already converted from SEK to EUR
						$amount_sek = string_between_two_strings($e2sinput_array[$key]['Purpose'], 'Originalbetrag: ', ' SEK');
						$exchangeRate = string_between_two_strings($e2sinput_array[$key]['Purpose'], 'Wechselkurs: ', ', ');
						// echo "<br>Originalbetrag <br>";
						// echo $amount_sek;
						// echo "<br>Wechselkurs <br>";
						// echo $exchangeRate;
						$e2s_exchange_info 				  = ' | ' . $e2sinput_array[$key]['Amount'] . ' EUR x ' . $exchangeRate . ' | ';
						// Remove N26 currency info
						$e2s_purpose = substr($e2s_purpose, strpos($e2s_purpose, "Transaction type"));
					} else {
						// Otherwise we need to calculate it ourselves
						$date 							  = $e2sinput_array[$key]['Value date'];
						$amount 						  = $e2sinput_array[$key]['Amount'];
						$exchangeRate					  = $riksbankArray[$date];
						$amount_sek 					  = round(($amount * $exchangeRate), 2);
						$e2s_exchange_info 				  = ' | ' . $amount . ' EUR x ' . $exchangeRate;
						// echo "<br>value date <br>";
						// echo $e2sinput_array[$key]['Value date'];
						// echo "<br>date <br>";
						// echo $date;
						// echo "<br>amount <br>";
						// echo $amount;
						// echo "<br>exchangeRate <br>";
						// echo $exchangeRate;
						// echo "<br>amount_sek <br>";
						// echo $amount_sek;
						// echo "<br>e2s_exchange_info <br>";
						// echo $e2s_exchange_info;
					}
					$e2sinput_array[$key]['Amount']	  = $amount_sek;
					$e2sinput_array[$key]['Currency'] = "SEK";

					$e2sinput_array[$key]['Purpose'] = $e2s_name . $e2s_exchange_info . $e2s_purpose . $e2s_account . $e2s_category;

					unset($e2sinput_array[$key]['Date']);
					unset($e2sinput_array[$key]['Currency']);
					unset($e2sinput_array[$key]['Bank']);
					unset($e2sinput_array[$key]['Category']);
					unset($e2sinput_array[$key]['Name']);
					unset($e2sinput_array[$key]['Account']);
					unset($e2sinput_array[$key]['Bank']);
				}

				// echo '<br><br>';
				// echo 'e2sinput_array no date';
				// echo '<pre>' . print_r($e2sinput_array, true) . '</pre>';

				$buffer = fopen('php://temp', 'r+');
				$csv_header = array_keys($e2sinput_array[0]);
				// $csv_header = array_merge($csv_header);
				// $csv_header = implode(';', $csv_header);
				// fputcsv($buffer, $csv_header, ";", "'", "\\");
				fputs($buffer, implode(';', $csv_header) . "\n");
				foreach ($e2sinput_array as $line) {
					// fputcsv($buffer, $line, ";", "'", "\\");
					fputs($buffer, implode(';', $line) . "\n");
				}
				rewind($buffer);
				$csv = stream_get_contents($buffer);
				fclose($buffer);

				echo '<br><br>';
				echo 'csv_header';
				echo '<pre>' . print_r($csv, true) . '</pre>';

				$output = '';
				$output  = $first_step;
				$output .= '<a id="step2"></a>';
				$output .= '<div id="e2s">';
				$output .= '<textarea style="width: 100%; height: 400px;" name="e2s-textarea-result" value="">' . $csv . '</textarea>';
				$output .= '<p>Ende</p>';

				echo $output;
				return ob_get_clean();
			}
		}
		return $first_step;
	}
}
add_action('init', 'e2s_init');

/** Always end your PHP files with this closing tag */
	?>