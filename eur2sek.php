<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       EUR2SEK
 * Plugin URI:        https://digitalerdurchbruch.de
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1.1
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
define('EUR2SEK_VERSION', '1.1.1');

/**
 * [e2s] returns html
 * @return string EUR2SEK HTML
 */

// *****************************************************

require_once plugin_dir_path(__FILE__) . 'e2s_logic.php';

add_action('wp_ajax_e2s_logic',        'e2s_logic');
add_action('wp_ajax_nopriv_e2s_logic', 'e2s_logic');
add_action('wp_enqueue_scripts', 'e2s_enqueue_jquery');

function e2s_enqueue_jquery() {
	wp_enqueue_script('jquery');
}
add_action('wp_footer', 'e2s_ajax', 0);

function e2s_ajax() {
?>
	<script>
		jQuery(function($) {
			var sendFeedBack = function(response) {
				console.log('response' + JSON.stringify(response));
				$('#e2s-input-textarea').val(response);

				var resultTable = document.getElementById('result-table');
				var responseRows = response.trim().split('\n');
				var responseCells = '';
				var resultTableHeader = '';
				var resultTableRows = '';
				var table ='';

				responseRows.forEach(function(row, row_index) {
					var resultTableColumns = '';
					var columns = row.split(';');
					columns.forEach(function(column, column_index) {
						resultTableColumns += row_index == 0 ? '<th>' + column + '</th>' : '<td>' + column + '</td>';
					});
					if (row_index == 0) {
        				resultTableHeader += '<tr>' + resultTableColumns + '</tr>';
    			    } else {
      			      resultTableRows += '<tr>' + resultTableColumns + '</tr>';
    			    }
				});

				table += '<table>';
		        	table += '<thead>';
       		    		table += resultTableHeader;
        			table += '</thead>';
        			table += '<tbody>';
            			table += resultTableRows;
        			table += '</tbody>';
   		 		table += '</table>';

				resultTable.innerHTML = table;
			};
			$("#e2s-convert").on("click", function() {
				// remove quotes from text
				$('#e2s-input-textarea').val($('#e2s-input-textarea').val().replace(/\"/g, ''));
				// console.log($('#e2s-input-textarea').val());
				let formData = $('form#e2s-input').serializeArray();
				$.post(
					"<?php echo admin_url('admin-ajax.php'); ?>", {
						action: "e2s_logic",
						e2s_input: formData
					},
					sendFeedBack,
				);
			});
		});
	</script>
	<?php
}

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

	function e2s_html() {
		ob_start();
	?>
		<div id="e2s">
			<form name="e2s-input" id="e2s-input" method="post" action="" autocomplete="off">
				<textarea style="width: 100%; height: 400px;" name="e2s-input-textarea" id="e2s-input-textarea" placeholder="Drop your CSV here"></textarea>
				<?php
				if (isset($_POST['e2s-convert'])) {
				?><input type="button" value="Clear" onclick="javascript:eraseText();">
				<?php
				}
				?>
				<input type="button" name="e2s-convert" id="e2s-convert" value="Convert">
			</form>

			<div id="result-table"></div>

			<?php // wp_nonce_field('test_nonce_action_e2s', 'test_nonce_field_e2s'); 
			?>
		</div>

		<script>
			function eraseText() {
				document.getElementById('e2s-input-textarea').value = '';
			}
		</script>
		<br />

<?php
		return ob_get_clean();
	}
}

add_shortcode('e2s', 'e2s_html');
add_action('init', 'e2s_init');

/** Always end your PHP files with this closing tag */
?>