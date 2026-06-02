<?php
/**
 * Render the breadcrumb block
 *
 * @param array $attributes Block attributes.
 *
 * @return string
 */

if (! defined('ABSPATH')) {
	exit;
}

$wrapper_attributes = get_block_wrapper_attributes();

// 使用全局面包屑函数（在 functions.php 中定义）
if (! function_exists('feryfit_breadcrumb_render')) {
	// 如果全局函数不存在，输出错误信息
	echo '<!-- Breadcrumb function not found -->';
	return;
}

?>

<div <?php echo $wrapper_attributes; ?>>
	<?php feryfit_breadcrumb_render(); ?>
</div>
