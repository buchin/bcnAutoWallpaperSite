<?php
	if (isset($_POST['rs_keywords'])) {
		$keywords = array();
		foreach ($_POST['rs_keywords'] as $id => $keyword)
			if (!empty($keyword) && !empty($_POST['rs_urls'][$id])) {
				$keywords[] = array(str_replace('"', '', stripslashes($keyword)), stripslashes($_POST['rs_urls'][$id]));
			}
				
		update_option('rs_keywords', $keywords);
	}

	$keywords = get_option('rs_keywords');
	$css = get_option('rs_css');
?>

<script type="text/javascript">

	function removeElement(id) {
		var pd = document.getElementById('keywords_td');
		var old = document.getElementById('rs_keywords_' + id);
		pd.removeChild(old);
		var old = document.getElementById('rs_urls_' + id);
		pd.removeChild(old);
		var old = document.getElementById('rs_remove_' + id);
		pd.removeChild(old);
	}

</script>

<div class="wrap">
<h2>WP Review Site Affiliate Link Settings</h2>
<form method="post" action="" id="rs_aff_conf">

<p>WP Review Site can automatically turn keywords in your post text into affiliate links. Specify keywords on the left,
and the URL those keywords should be linked to on the right.</p>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="keywords">Edit Keywords</label></th>
<td id="keywords_td">
	<?php
		if (!empty($keywords)) {
			foreach ($keywords as $id => $arr) {
				$keyword = $arr[0];
				$url = $arr[1];
				
				echo 'Keyword: <input type="text" id="rs_keywords_' . $id . '" name="rs_keywords[' . $id . ']" value="' . $keyword . '" /> ';
				echo 'URL: <input type="text" id="rs_urls_' . $id . '" name="rs_urls[' . $id . ']" value="' . $url . '" /> ';
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
<th scope="row"><label for="addkeyword">Add a Keyword</label></th>
<td>
	<?php
		echo 'Keyword: <input type="text" name="rs_keywords[' . ($id + 1) . ']" value="" /> ';
		echo 'URL: <input type="text" name="rs_urls[' . ($id + 1) . ']" value="" /> ';
	?>
</td>
</tr>
</table>

<br />

<input type="submit" value="Save Settings" class="button" />

</form>
</div>