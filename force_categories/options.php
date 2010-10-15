<script type="text/javascript">
/*<![CDATA[*/
function add_category(destinationID, sourceID, valFieldID) {
  var mustHave = [],
      i,
      targetList = jQuery("#" + destinationID),
      valueField = jQuery('#' + valFieldID);
  jQuery("#" + sourceID + " :selected").each(function (i, selected) {
    mustHave[i] = jQuery(selected).text();
    jQuery(selected).remove();
  });
//  alert("Selected: " + mustHave.join(', '));
  mustHave = mustHave.sort();
  for (i = 0; i < mustHave.length; i++) {
    jQuery("#" + destinationID + "").append('<li>' + mustHave[i] + '</li>');
  }

  if( 0 == valueField.val().length) {
	valueField.val(mustHave.join(','));
  } else {
  	valueField.val(valueField.val() + ',' + mustHave.join(','));
  }
/* TODO: click item to remove
'
   jQuery(".musthave_list").click(function(){
   var element = $(this);
   var added = false;
   var targetList = $(this).parent().siblings(".ingredientList")[0];
   $(this).fadeOut("fast", function() {
   $(".ingredient", targetList).each(function(){
   if ($(this).text() > $(element).text()) {
   $(element).insertBefore($(this)).fadeIn("fast");
   added = true;
   return false;
   }
   });
   if(!added) $(element).appendTo($(targetList)).fadeIn("fast");
   });
   });
   */
}
/*]]>*/
</script>

<h3>Force Categories settings</h3>

<p><span class="dropcap">M</span><strong>ust have</strong> categories will be assigned to every post or page created or edited by this user, whereas <strong>can&#8217;t have</strong> categories can never be assigned to any posts or pages created or edited by this user.</p>
<div id="catres"><!--Spacer needed to anchor the parent-->
<div class="spacer"></div>
<div class="catpick">
<h3>Must have</h3>
<ul class="catselect" id="musthave">
<?php
foreach( $musthaves as $musthave_value ):
	$musthave_value  = esc_attr($musthave_value);
	?>
<li class="musthave_list"><?= $musthave_value ?></li>
<?php endforeach; ?>
</ul>

<h6>(Click category to remove)</h6>
<input type="hidden" id="musthaveval" name="musthaveval" value="<?= $musthaves_flat ?>" />
</div>
<div id="mustactions" class="catactions"><input type="button" name="add_must" value="&#171;" onclick="add_category('musthave', 'categorylist', 'musthaveval')" /></div>
<div id="categories" class="catpick">
<h3>Categories</h3>
<select id="categorylist" size="10" name="event-dropdown" style="height:100px!important" multiple="multiple">
<?php
foreach ($categories as $category):
	$category = esc_attr($category);?>
<option value="<?= $category ?>"><?= $category ?></option>
<?php
endforeach;
?>
</select>
</div>
<div id="cantactions" class="catactions"><input type="button" name="add_cant" value="&#187;" onclick="add_category('canthave', 'categorylist', 'canthaveval')" /></div>
<div class="catpick">
<h3>Can't have</h3>
<ul id="canthave" class="catselect">
<?php
foreach( $canthaves as $canthave_value ):
	$canthave_value  = esc_attr($canthave_value);
?>
<li class="canthave_list"><?= $canthave_value ?></li>
<?php endforeach; ?>
</ul>
<h6>(Click category to remove)</h6>
<input type="hidden" id="canthaveval" name="canthaveval" value="<?= $canthaves_flat ?>" />
</div>
<!--Spacer needed to anchor the parent-->
<div class="spacer"></div>
</div>
