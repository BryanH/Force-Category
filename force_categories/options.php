<script type="text/javascript">
/*<![CDATA[*/
function add_must_tax() {
	var mustHave = jQuery('#categorylist :selected').text();
	alert("Selected "+mustHave);
}
/*]]>*/
</script>

<h3><div id="icon-users" class="icon32"></div> Force Categories settings</h3>

<p><span class="dropcap">M</span><strong>ust have</strong> categories will be assigned to every post by this user, whereas <strong>can&#8217;t have</strong> categories can never be assigned to any posts by this user.</p>
<div id="catres"><!--Spacer needed to anchor the parent-->
<div class="spacer"></div>
<div id="musthave" class="catpick">
<h2>Must have</h2>
<ul class="catselect">
<?php $musthaves = get_the_author_meta( 'musthave_categories', $user->ID );
foreach( $musthave as $musthave ): ?>
<li id="<?= $musthave ?>"><?= $musthave ?> ***X***</li>
<?php endforeach; ?>
</ul>
<input type="hidden" id="musthaveval" name="musthaveval" value="<?= implode(',', $musthave ); ?>" />
</div>
<div id="mustactions" class="catactions"><input type="button" name="add_must" value="&#171;" onclick="add_must_tax()" /></div>
<div id="categories" class="catpick">
<h2>Categories</h2>

<!-- ?php wp_list_categories('orderby=name&include=' . implode(',', $musthave ) ); ? -->
<select id="categorylist" size="10" name="event-dropdown" style="height:100px!important" multiple="multiple">
<?php
$categories = get_terms('subsite', 'fields=names');
foreach ($categories as $category) {
	$option = '<option value="' . $category . '">';
	$option .= $category;
	$option .= '</option>';
	echo $option;
}
?>
</select>
</div>
<div id="cantactions" class="catactions"><input type="button" name="add_cant" value="&#171;" /></div>
<div id="canthave" class="catpick">
<h2>Can't have</h2>
<ul class="catselect">
<?php $canthaves = get_the_author_meta( 'canthave_categories', $user->ID );
foreach( $canthave as $canthave ): ?>
<li id="<?= $canthave ?>"><?= $canthave ?> ***X***</li>
<?php endforeach; ?>
</ul>
<input type="hidden" id="canthaveval" name="canthaveval" value="<?= implode(',', $canthave ); ?>" />
</div>
<!--Spacer needed to anchor the parent-->
<div class="spacer"></div>
</div>
