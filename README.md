Force Categories
================

Force Categories is a Wordpress plugin that affects an author's posts with respect to categories.
Every post by a specific author can be:

1. Forced to belong to one or more specified categories (custom taxonomies)
1. Prevented from belonging to one or more specified categories (custom taxonomies)

Default taxonomies:
* Featured
* Voices
* Main Well

Additional taxonomies are added as custom to the particular blog (e.g., "More Energy News")

## Target environment
This plugin is designed to be used on a multi-author blog (where you allow multiple authors and/or contributors to write posts).
While a contributor's posts must be approved (and therefore could be edited by an Author or Editor),
it would become tedious and error-prone to always remember which categories to include and exclude on a particular contributor's posts.
## Installing
* Copy the directory into your `wp-content/plugin` directory.
* Go into your Wordpress admin screen and activate the plugin.

## Using
* Pull up a user's account in the admin screen and scroll to the "Force Categories" section.
* Place categories in the *force include* or *force exclude* boxes as appropriate.

## Use Case Examples
Here are some situations where you'd use this plugin.

### Force Category
Suppose that you have a set of authors (Bob, Jane and Timmy), whose posts you wish to view, excluding all other authors.

You set Bob, Jane and Timmy to always post as the category "The Three Amigos." Now you can show all their posts by going to that category's page.  Note that Bob, Jane and Timmy can assign whatever other categories they want to their posts, but they can't remove "The Three Amigos" category from their posts.

### Prevent Category
Suppose that you use a category, "Featured" to display posts that you wish to display in a special area on the home page.

You don't want the authors and contributors to assign their posts to that category without permission. So, you prevent them from using that category. Only an Editor (or an Author who has access to assign that category) can assign their posts to the "Featured" category.

## Author
Bryan Hanks bryanh41@hotmail.com
