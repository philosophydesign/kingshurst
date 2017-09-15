<div class="extrapage property-specification lello">
	<div class="container">
		<div class="row">
			<div class="stopatmax extracontent col-md-12 col-sm-12 col-xs-12">
				<a name="specifications"></a>
				<h2>Specification</h2>
				<?php
				$specbits = get_field('specification_parts');
				if (!empty($specbits)) {
					$c = 1;
					$cols = [];
					foreach ($specbits as $s) {
						$cols[$c] .= '
							<h4>'.$s['title'].'</h4>
							'.$s['body'].'
						';
						$c++;
						if ($c > 3) {
							$c = 1;
						}
					}
					echo '<div class="row">';
					foreach ($cols as $c) {
						echo '<div class="col-md-4 col-sm-12 col-xs-12">'.$c.'</div>';
					}
					echo '</div>';
				} else {
					#echo 'No specification';
				}
				?>
			</div>
		</div>
	</div>
</div>
