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

        <button id="__paymentButton" form="ECPayForm" value="<?php echo esc_html($buttonText);?>"><?php echo $buttonText;?></button>

        <p style="font-size: 0.8em;margin: 6px 0px; width: 84%;">
            <?php echo __( '門市名稱', 'purchaserStore' ) . ':'; ?><label id="purchaserStoreLabel"><?php echo $cvsInfo['CVSStoreName'];?></label>
        </p>
        <p style="font-size: 0.8em;margin: 6px 0px; width: 84%;">
            <?php echo __( '門市地址', 'purchaserAddress' ) . ':'; ?><label id="purchaserAddressLabel"><?php echo $cvsInfo['CVSAddress'];?></label>
        </p>
        <p style="font-size: 0.8em;margin: 6px 0px; width: 84%;">
            <?php echo __( '門市電話', 'purchaserPhone' ) . ':'; ?><label id="purchaserPhoneLabel"><?php echo $cvsInfo['CVSTelephone'];?></label>
        </p>
        <p style="font-size: 0.8em;color: #c9302c; width: 84%;">
            <?php echo '使用綠界科技超商取貨，連絡電話請填寫手機號碼。'; ?>
        </p>
    </td>
</tr>
<div id="ECPayCvsForm"></div>

<script>
    jQuery(document).ready(function($) {
        var form_ECPayForm = "<?php echo $html; ?>"
        var content = $('<div>').append( form_ECPayForm ).html();
        $("#ECPayCvsForm").prepend(content);
    });
</script>