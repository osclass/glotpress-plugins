# Glotpress Plugins

This are the plugins we use in [translate.osclass.org](http://translate.osclass.org/).

## Google Analytics

Track your GlotPress site easily. You just need to define your tracking id:

```
$this->ga_id= 'UA-XXXXXXXX-X';
```

## Getting Started

Display a notice just below the header to show your getting started guide to your users. It's only displayed to no logged users.

This is the one we use in our [glotpress site](http://translate.osclass.org/).

## Clone project

Clone an existing project (and subprojects) into a new one, very useful since GlotPress doesn't merge new and known strings. This plugin HAVE TO be used with this version of GlotPress [conejoninja/glotpress](https://github.com/conejoninja/glotpress) since it requires a hook on a template (to add the option).

## Export project

This plugin will ad a new option to export the project into a zip package ready to install it as a language in Osclass. This plugin HAVE TO be used with this version of GlotPress [conejoninja/glotpress](https://github.com/conejoninja/glotpress) since it requires a hook on a template (to add the option). The hook needed is "subprojects_menu_item", add this line into gp-templates/project.php around line 63:

	<?php do_action("subprojects_menu_item", $sub_project); ?>

## Import originals

This is a SCRIPT, place it on scripts folder, also, git clone Osclass (https://github.com/osclass/Osclass) and i18n-tools (https://github.com/osclass/i18n-tools). Set a cron to run it daily. It will checkout latest develop version of Osclass, then extract the strings with i18n-tools and import them into glotpress "osclass/dev/" project.