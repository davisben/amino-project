# Amino Project

Template for creating a Drupal project with the [Amino distribution](http://www.drupal.org/project/amino).

## Installation
To create a new project, run this command, replacing PROJECT_DIR with the directory you would like the 
project to be installed in.
```
composer create-project davisben/amino-project PROJECT_DIR --no-dev --no-interaction
```

## Drupal Paranoia
This template uses the [Drupal Paranoia](https://github.com/drupal-composer/drupal-paranoia) composer plugin 
to increase security by moving all PHP files out of the web root. This is accomplished by symlinking public
assets from the app directory to the web directory. To ensure all files are properly symlinked, ensure that
the public files directory exists at `app/sites/<site>/files`. The web directory can be rebuilt with the
following command.
```
composer drupal:paranoia
```
