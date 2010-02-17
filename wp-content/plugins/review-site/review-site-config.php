<?php
	if (isset($_POST['rs_categories'])) {
		foreach ($_POST['rs_categories'] as $id => $category)
			if (empty($category))
				unset($_POST['rs_categories'][$id]);
			else
				$_POST['rs_categories'][$id] = str_replace('"', '', stripslashes($category));
				
		update_option('rs_categories', $_POST['rs_categories']);
	}
	if (isset($_POST['rs_reorder']))
        $reorder = ($_POST['rs_reorder'] == 'true') ? true : false;
        update_option('rs_reorder', $reorder);
	if (isset($_POST['rs_css']))
		update_option('rs_css', $_POST['rs_css']);
	
	$reorder = get_option('rs_reorder');
	$categories = get_option('rs_categories');
	$css = get_option('rs_css');
?>

<script type="text/javascript">

	function removeElement(id) {
		var pd = document.getElementById('categories_td');
		var old = document.getElementById('rs_categories_' + id);
		pd.removeChild(old);
		var old = document.getElementById('rs_remove_' + id);
		pd.removeChild(old);
	}

</script>

<div class="wrap">
<h2>WP Review Site Settings</h2>
<form method="post" action="" id="rs_conf">

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="blogdescription">Reorder Posts by Weighted Rating</label></th>
<td>
	<input type="radio" name="rs_reorder" value="true" <?php if ($reorder) echo 'checked="checked"'; ?> /> Yes<br />
	<input type="radio" name="rs_reorder" value="false" <?php if (!$reorder) echo 'checked="checked"'; ?> /> No
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="css">Embedded Ratings CSS</label></th>
<td id="css">
	<textarea style="width: 400px; height: 120px" name="rs_css" id="rs_css"><?php echo $css; ?></textarea>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="categories">Edit Rating Categories</label></th>
<td id="categories_td">
	<?php
		if (!empty($categories)) {
			foreach ($categories as $id => $category) {
				echo '<input type="text" id="rs_categories_' . $id . '" name="rs_categories[' . $id . ']" value="' . $category . '" /> ';
				?>
				<a href="" id="rs_remove_<?php echo $id; ?>" onclick="removeElement('<?php echo $id; ?>'); return false;" style="color: #c00">remove</a>
				<br />
				<?php
			}
		}
	?>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="blogdescription">Add a Category</label></th>
<td><input type="text" name="rs_categories[]" value="" /></td>
</tr>
</table>

<br />

<input type="submit" value="Save Settings" class="button" />

</form>
</div>