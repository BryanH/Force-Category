<h3>Force Categories settings</h3>

<p><span class="dropcap">M</span><strong>ust have</strong> categories will be assigned to every post by this user, whereas <strong>can&#8217;t have</strong> categories can never be assigned to any posts by this user.</p>
<div id="catres"><!--Spacer needed to anchor the parent-->
<div class="spacer"></div>
<div id="musthave" class="catpick">
<h2>Must have</h2>
<select class="catselect" name="musthave" size="6" multiple="multiple" style="height:100px!important">
<option>123</option>
<option>34242</option>
<option>123</option>
<option>34242</option>
</select></div>
<div id="mustactions" class="catactions"><input type="button" name="add_must" value="&#171;" /><br />
<input type="button" name="remove_must" value="&#187;" /></div>
<div id="categories" class="catpick">
<h2>Categories</h2>
<select id="categorylist" size="10" name="event-dropdown" style="height:100px!important" multiple="multiple">
 <?php
  $categories=  get_categories(array('orderby' => 'name'));
  foreach ($categories as $category) {
  	$option = '<option value="' . $category->category_nicename . '">';
	$option .= $category->cat_name;
	//$option .= ' ('.$category->category_count.')';
	$option .= '</option>';
	echo $option;
  }
 ?>
</select>

</div>
<div id="cantactions" class="catactions"><input type="button" name="add_cant" value="&#171;" /><br />
<input type="button" name="remove_cant" value="&#187;" /></div>
<div id="canthave" class="catpick">
<h2>Can't have</h2>
<select class="catselect" name="canthave" size="6" multiple="multiple" style="height:100px!important">
<option>123</option>
<option>34242</option>
<option>123</option>
<option>34242</option>
</select></div>
<!--Spacer needed to anchor the parent-->
<div class="spacer"></div>
</div>