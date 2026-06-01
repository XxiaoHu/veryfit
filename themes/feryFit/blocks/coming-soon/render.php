<?php
/**
 * Coming Soon Block Renderer
 *
 * @package feryfit
 */

$wrapper_attributes = get_block_wrapper_attributes();
$image_url = get_template_directory_uri() . '/assets/images/qidai.png';

$main_text = isset( $attributes['mainText'] ) ? $attributes['mainText'] : 'The function is under development. Please stay tuned for the page!';
?>

<div <?php echo $wrapper_attributes; ?>>
	<div class="coming-soon">
		<div class="coming-soon__icon">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="Coming Soon" />
		</div>
		<p class="coming-soon__text"><?php echo esc_html( $main_text ); ?></p>
	</div>
</div>
