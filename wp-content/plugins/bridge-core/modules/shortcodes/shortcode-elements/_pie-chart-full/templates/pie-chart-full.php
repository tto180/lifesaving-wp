<?php
	
	// Regex pattern to match chart data (value,color,label)
	$pattern = "/\\d+(,?#?\\w*\\s?;?)*/";
	
	// Match the pattern against the content
	preg_match_all($pattern, $content, $matches);
	
	if (!empty($matches) && !empty($matches[0])) {
		$match = $matches[0][0];
	} else {
		return $html = '<p>' . esc_html__('Insert valid Pie Chart data', 'bridge-core') . '</p>';
	}
	
	$id = mt_rand(1000, 9999);
	$datasets = array();
	$values = array();
	$colors = array();
	
	// Split the matched string into chart elements
	$pie_chart_array = explode(";", $match);
	
	// Process each pie chart element (value, color)
	foreach ($pie_chart_array as $chart_data) {
		$pie_chart_el = explode(",", $chart_data);
		
		if (isset($pie_chart_el[0]) && isset($pie_chart_el[1])) {
			// Add value and color after validation
			$values[] = intval( trim( $pie_chart_el[0] ) );
			$colors[] = trim($pie_chart_el[1]);
		}
	}
	
	$data = array(
		'data-values' => json_encode($values),
		'data-colors' => json_encode($colors),
	);
	
	// Add optional appearance attribute
	if ($element_appearance != '') {
		$data['data-element-appearance'] = $element_appearance;
	}
?>

<div class="q_pie_graf_holder q-pie-chart-full">
	<div class="q_pie_graf" <?php echo bridge_qode_get_inline_attrs($data); ?>>
		<canvas id="<?php echo 'pie' . esc_attr($id); ?>" height="<?php echo esc_attr($height); ?>" width="<?php echo esc_attr($width); ?>"></canvas>
	</div>
	<div class="q_pie_graf_legend">
		<ul>
			<?php
				foreach ($pie_chart_array as $chart_data) {
					$pie_chart_el = explode(",", $chart_data);
					
					// Ensure that each element has value, color, and label
					if (isset($pie_chart_el[0], $pie_chart_el[1], $pie_chart_el[2])) {
						?>
						<li>
							<div class='color_holder' style="background-color:<?php echo esc_attr(trim($pie_chart_el[1])); ?>"></div>
							<p style="color: <?php echo esc_attr($color); ?>"><?php echo esc_html(trim($pie_chart_el[2])); ?></p>
						</li>
						<?php
					}
				}
			?>
		</ul>
	</div>
</div>
