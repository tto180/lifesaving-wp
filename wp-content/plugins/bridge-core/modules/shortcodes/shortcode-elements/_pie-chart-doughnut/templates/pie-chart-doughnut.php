<?php
	
	$pattern = "/\\d+(,?#?\\w*\\s?;?)*/";
	
	// Match the pattern against the content
	preg_match_all($pattern, $content, $matches);
	
	if ( ! empty( $matches ) && ! empty( $matches[0] ) ) {
		$match = $matches[0][0];
	} else {
		return $html = '<p>' . esc_html__('Insert valid Pie Chart data', 'bridge-core') . '</p>';
	}
	
	$id = mt_rand(1000, 9999);
	$datasets = array();
	$values = array();
	$colors = array();
	
	// Explode the matched string into chart elements
	$pie_chart_array = explode(";", $match);
	
	// Process each pie chart element
	for ($i = 0; $i < count($pie_chart_array); $i++) {
		$pie_chart_el = explode(",", $pie_chart_array[$i]);
		
		// Ensure values are integers, and colors are trimmed
		$values[] = intval( trim( $pie_chart_el[0] ) );
		$colors[] = trim( $pie_chart_el[1] );
	}
	
	$data = array(
		'data-values' => json_encode($values),
		'data-colors' => json_encode($colors),
	);

?>

<div class="q_pie_graf_holder q-pie-chart-doughnut">
	<div class="q_pie_graf" <?php echo bridge_qode_get_inline_attrs($data); ?>>
		<canvas id="<?php echo 'pie' . esc_attr($id); ?>" height="<?php echo esc_attr($height); ?>" width="<?php echo esc_attr($width); ?>"></canvas>
	</div>
	<div class="q_pie_graf_legend">
		<ul>
			<?php
				// Reset the pie chart array to use for the legend
				$pie_chart_array = explode(";", $match);
				for ($i = 0; $i < count($pie_chart_array); $i++) {
					$pie_chart_el = explode(",", $pie_chart_array[$i]);
					?>
					<li>
						<div class="color_holder" style="background-color:<?php echo esc_attr(trim($pie_chart_el[1])); ?>"></div>
						<p style="color: <?php echo esc_attr($color); ?>">
							<?php echo esc_html(trim($pie_chart_el[2])); ?>
						</p>
					</li>
				<?php } ?>
		</ul>
	</div>
</div>
