<?php
/*
Plugin Name: Custom Gravity Admin
Plugin URI: http://www.pursuitdesign.com
Description: This removes input types, field groups and even more settings from the admin form editor in Gravity Forms
Version: 1.3
Author: Pursuit Design
Author URI: http://www.pursuitdesign.com
*/

//Form Settings customizations
add_filter('gform_form_settings', 'pd_custom_form_settings', 10, 2);
function pd_custom_form_settings($settings) {

	//rewrite the Label Placement, allowing only Right Aligned, and hide the field
	$settings['Form Layout']['form_label_placement'] = '
        <tr style="visibility:hidden;height:1px;">
            <th style="height:1px;padding:0;">Label Placement</th>
            <td style="height:1px;padding:0;"><select id="form_label_placement" onchange="UpdateLabelPlacement();" name="form_label_placement">
<option selected="selected" value="right_label">Right Aligned Only</option>
</select></td>
        </tr>';

	//remove certain setting fields and field groups
    unset($settings['Form Layout']['css_class_name']);
    unset($settings['Form Options']);
    unset($settings['Restrictions']);
    unset($settings['Save and Continue']);
    unset($settings['Form Button']['form_button_conditional']);

	//store, unset, then reset to move field group to the bottom, then hide it
	$setting_form_layout_block = $settings['Form Layout'];
	unset($settings['Form Layout']);
	$settings['Form Layout'] = $setting_form_layout_block;

    return $settings;
}

//hide all group headers to clean it up, plus remove some other inputs
add_action('admin_head','pd_edit_group_header');
function pd_edit_group_header() {
  global $pagenow;
  if (in_array($pagenow,array('admin.php'))) {
    $newgfcss=<<<STYLE
<style type="text/css">
<!--
.gf_settings_subgroup_title {
  display:none;
}
input#form_button_image {
	display:none;
}
label[for='form_button_image'] {
	display:none !important;
}
#sub_label_placement_setting {
	display:none !important;
}
-->
</style>
STYLE;
    echo $newgfcss;
  }
}
//end Form Settings customizations

//Form Editor customizations
add_filter("gform_add_field_buttons", "pd_remove_fields", 10, 2);
function pd_remove_fields($field_groups){

    $index = 0;
    $standard_field_index = -1;
    $post_field_index = -1;
    $advanced_field_index = -1;
    $pricing_field_index = -1;

    //Finding group indexes
    foreach($field_groups as $group){
        if($group["name"] == "standard_fields")
            $standard_field_index = $index;
		else if($group["name"] == "post_fields")
            $post_field_index = $index;
		else if($group["name"] == "advanced_fields")
            $advanced_field_index = $index;
		else if($group["name"] == "pricing_fields")
            $pricing_field_index = $index;

        $index ++;
    }

    //remove fields from Standard group
    if($standard_field_index >=0){
        $page_break_index = -1;
        $page_insert_index = -1;
        $number_index = -1;
        $multi_select_index = -1;
        $index = 0;
        foreach($field_groups[$standard_field_index]["fields"] as $standard_field){
            if($standard_field["value"] == "Page Break")
                $page_break_index = $index;
			else if($standard_field["value"] == "Page")
				$page_insert_index = $index;
			else if($standard_field["value"] == "Number")
				$number_index = $index;
			else if($standard_field["value"] == "Multi Select")
				$multi_select_index = $index;
            $index++;
        }

        unset($field_groups[$standard_field_index]["fields"][$page_break_index]);
        unset($field_groups[$standard_field_index]["fields"][$page_insert_index]);
        unset($field_groups[$standard_field_index]["fields"][$number_index]);
        unset($field_groups[$standard_field_index]["fields"][$multi_select_index]);
    }

    //remove fields from Advanced group
    if($advanced_field_index >=0){
        $name_index = -1;
        $date_index = -1;
        $time_index = -1;
        $phone_index = -1;
        $address_index = -1;
        $file_upload_index = -1;
        $list_index = -1;
        $captcha_index = -1;
        $index = 0;
        foreach($field_groups[$advanced_field_index]["fields"] as $advanced_field){
            if($advanced_field["value"] == "Name")
                $name_index = $index;
			else if($advanced_field["value"] == "Date")
				$date_index = $index;
			else if($advanced_field["value"] == "Time")
				$time_index = $index;
			else if($advanced_field["value"] == "Phone")
				$phone_index = $index;
			else if($advanced_field["value"] == "Address")
				$address_index = $index;
			else if($advanced_field["value"] == "File Upload")
				$file_upload_index = $index;
			else if($advanced_field["value"] == "List")
				$list_index = $index;
			else if($advanced_field["value"] == "CAPTCHA")
				$captcha_index = $index;
            $index++;
        }

        unset($field_groups[$advanced_field_index]["fields"][$name_index]);
        unset($field_groups[$advanced_field_index]["fields"][$date_index]);
        unset($field_groups[$advanced_field_index]["fields"][$time_index]);
        unset($field_groups[$advanced_field_index]["fields"][$phone_index]);
        unset($field_groups[$advanced_field_index]["fields"][$address_index]);
        unset($field_groups[$advanced_field_index]["fields"][$file_upload_index]);
        unset($field_groups[$advanced_field_index]["fields"][$list_index]);
        unset($field_groups[$advanced_field_index]["fields"][$captcha_index]);
    }

    //removing entire Post field group
    if($post_field_index >= 0)
        unset($field_groups[$post_field_index]);

	//removing entire Pricing field group
    if($pricing_field_index >= 0)
        unset($field_groups[$pricing_field_index]);

    return $field_groups;
}
//end Form Editor customizations
?>
