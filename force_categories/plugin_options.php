<h3><?php _e( "Force Categories Options", 'force_categories' ); ?></h3>
<p><?php

	_e("Enter the page custom taxonomy name to use for managing user interactions.");
	echo ' ';
?></p>
  <ul>
    <li><label for="taxonomy_to_use">Post Taxonomy/category to use</label>
    <select name="taxonomy_to_use">
    <?php
$taxonomies=get_taxonomies('', 'objects');
foreach ($taxonomies as $taxonomy ) {
  echo "<option value='{$taxonomy->name}'";
  if( $taxonomy->name == $taxonomy_to_use){
  	echo ' selected="selected"';
  }
  echo ">{$taxonomy->labels->singular_name}</option>";
}
?>
    </select>
    <li>
  </ul>