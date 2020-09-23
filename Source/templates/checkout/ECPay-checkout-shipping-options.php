<?php
/**
 * 前台 - 購物車顯示 option
 */

defined('ECPAY_PLUGIN_PATH') || exit;

// 驗證
if (!is_array($shipping_options)) {
    return;
}

// 組合超商取貨項目
$options = '<option>------</option>';
foreach ($shipping_options as $option) {
    $selected = ($shipping_type == esc_attr($option)) ? 'selected' : '';
    $options .= '<option value="' . esc_attr($option) . '" ' . $selected . '>' . esc_html($shipping_name[$option]) . '</option>';
}

?>

<!-- template -->
<input type="hidden" id="category" name="category" value="<?php echo esc_html($category); ?>">
<tr class="shipping_option">
    <th><?php echo esc_html($method_title); ?></th>
    <td>
        <select name="shipping_option" class="input-select" id="shipping_option">
            <?php echo $options; ?>
        </select>
    </td>
</tr>