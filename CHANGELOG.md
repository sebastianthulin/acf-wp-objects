ACF-WP-Objects Changelog
========================

0.5.19
------
 - Page Layout: Revise $args
 - Flexible Content: Add Accordion to field options
 - New Field Type: Select Nav Menu
 - New Field Type: Select Nav Menu Location

0.5.18
------
 - Fix: Composer

0.5.17
------
 - CSS: Introduce `accordion` class for flexibe content
 - CSS: Introduce `buttongroup` class for checkboxes

0.5.16
------
 - Fix WP Options Page location with ACF free
 - Fix JS error with ACF Free

0.5.15
------
 - Fixes: PHP Warnings

0.5.14
------
 - Feature: acf_add_page_layout() merge with existing fields

0.5.13
------
 - Fix: ACF Move fields broken with includer fields involved
 - Repeater Choice: Use row index for display and key
 - CSS: introduce .btn-Ncols classes (2-6 columns) for acf button group grid

0.5.12
------
 - Fix: PostType Object PHP warning
 - CSS: improve block editor styles

0.5.11
------
 - Fix: PHP fatal in Fields/SelectRole
 - Fix: php warning in wp-includes/formatting.php

0.5.10
------
 - Includer field: fix collapse target field inside repeater

0.5.9
-----
 - ID Field: make slugify an extra setting
 - ID Field: handle duplicated fields
 - Repeater Choices: Introduce css class `color-spots`
 - CSS: introduce `deny-sort` class for repeaters to disable sorting

0.5.8
-----
 - Styles: improve seamless style

0.5.7
-----
 - Fix: repeater choices with local json

0.5.6
-----
 - rm var_dump

0.5.5
-----
 - Fix: acf_recreate_field_keys() didn't recreate conditional logic with message fields

0.5.4
-----
 - Fix: Includer field admin local field groups
 - ID-Option: slugify value on set-once-fields

0.5.3
-----
 - Page Layout: Fix repeater collapsed fields
 - Feature: ID-Option for Text fields. Ensures uniqueness the on screen. Use with repeaters!
 - Feature: introduce api functions `acf_recreate_field_keys( $fields )` and `acf_is_fieldgroup_admin()`
 - Fix: RepeaterChoices init causing weird behaviour in field group admin


0.5.2
-----
 - CSS: fix seamless style for repeaters inside accordions
 - Page Layout: Allow `save_post_content` to be callable

0.5.1
-----
 - Feature: Location Rule Everywhere
 - Fix: LocalJSON not saving under custom paths in ACF 5.9+
 - API: introduce `acf_get_page_layouts()` and `acf_get_page_layout( $page_layout )`
 - Page-Layout: skip layouts with `row_layout` not set
 
0.5.0
-----
 - Feature: Polylang compatibiity – assign ACF fields to polylang languages

0.4.18
------
 - Fix: Post content not saved
 - Styles: fix seamless, no-label

0.4.16
------
 - CSS: Fix seamless
 - CSS: Introduce no-label
 - Repeater Choice: disable field if there are no choices
 - Repeater Choice: Choose visualize field
 - Fix: Helper\Conditionals combine function

0.4.6
-----
 - CSS: Compact styles
 - Fix: includer field bug in field group admin

0.4.5
-----
 - Repeater Choices: Fix local fields not showing in field settings
 - Fix: JSON Load Paths

0.4.4
-----
 - Compatibility: Use ACF 5.9+ native save json path

0.4.1
-----
 - Feature: Sweet Spot Auto

0.4.0
-----
 - Feature: Page Layouts
 - Feature: Localize field groups through .po
 - Feature: Customize local JSON paths

0.2.12
------
 - Fix network settings

0.2.11
------
 - Security hardening

0.2.8 - 0.2.10
--------------
 - Sweet Spot field

0.2.0
------
 - Network options pages
 - ESNext
 - Upgrade plugin Boilerplate
 - add tests skeleton
 - allow conditional logic for WP-Object-Fields
 - Several bugfixes

0.1.16
------
 - Repeater-Choice: improve color select style
 - Introduce Gulp
 - Fix Issue in Field Group admin: acf noted changes but nothing changed

0.1.15
------
 - Fix function call

0.1.14
------
 - Fix messed-up fields after save
 - Fix uninstall fatal

0.1.13
------
 - Repeater Select: style improvements

0.1.12
------
 - Introduce Repeater Select

0.1.9
-----
 - Fix PHP warning

0.1.8
-----
 - Change NS dir case

0.1.6 + 0.1.7
-------------
- fix pt location rule not matching

0.1.5
-----
 - Post type location rule broken in acf/ajax/check_screen request

0.1.4
-----
 - fix php fatal in acf-customizer compat class (again)

0.1.3
-----
 - fix php fatal in acf-customizer compat class

0.1.2
-----
 - fix php fatal in plugin component class (again)

0.1.1
-----
 - fix php fatal in plugin component class

0.1.0
-----
 - Introduce changelog
 - Introduce location rules for Post Type and Taxonomy
