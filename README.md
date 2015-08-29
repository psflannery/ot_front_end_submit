# ot_front_end_submit
As part of Ben Vickers' Opening Times Reading section, users will be asked to submit a link of their choice along with a short explanation as to why they have chosen it. These submission will be gathered and periodically pubished, to create an archive of the project.

Version: 1.1.0 Date: 29/8/15

# Still a work in progress
Creates a shortcode that renders a form enabling users to submit links and a description to the site. Submissions are marked as pending and published upon review.

This project is very specifically tailored to work with the Opening Times site and preobably won't work elsewhere. That said, you are more than welcome to take and resuse anything you find useful. 

The front-end submission form is very heavily based on the CMB2 front end submit form - https://github.com/WebDevStudios/CMB2-Snippet-Library/blob/master/front-end/cmb2-front-end-submit.php

The code to create a page template within a WordPress plugin, is taken with only the most minor adjstment to enable the page-template dropdown in Quick Edit view, from here - http://www.wpexplorer.com/wordpress-page-templates-plugin/ This in turn, owes a great deal of thanks to the work done by Tom McFarlin here - https://github.com/tommcfarlin/page-template-example

## What Happens...
* Submitted link is checked to see if valid.
* If so, grab the site `<title>` and use as our post title.
* The original link is marked up and added to the post content along with any description.
* Each Post is assigned a category and `link` post format.
* Category and Post-author are set in the theme customizer (this helps with the transition from dev to production environments).
* A page template is created that loops through all the submitted links.

## TODO
* Lots of tidying