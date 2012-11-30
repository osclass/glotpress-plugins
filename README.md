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

Clone an existing project (and subprojects) into a new one, very useful since GlotPress doesn't merge new and known strings. This plugins HAVE TO be used with this version of GlotPress [conejoninja/glotpress](https://github.com/conejoninja/glotpress) since it requires a hook on a template (to add the option).