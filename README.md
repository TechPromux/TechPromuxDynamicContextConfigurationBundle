# TechPromux Dynamic Context Configuration Bundle: for dynamic system variables

This project is a symfony based bundle with possibility to manage multiple dynamic system variables.

It provides a custom code for create records to save configurations in multiple data type values. 

You can add your own custom variables types and this bundle can manage them in a admin panel too.  

You only need download it and use it with a little effort. 

We hope that this project contribute to your work with Symfony.

# Instalation

Open a console in root project folder and execute following command:

    composer install techpromux/dynamic-configuration-bundle

# Configuration

For custom database and other options edit files:

	// TODO

Create/Update tables from entities definitions, executing following command:

    ./bin/console doctrine:schema:update --force


Force bundle to copy all assets in public folder, executing following command:

    ./bin/console assets:install web (for Symfony <= 3.3)

    ./bin/console assets:install public (for Symfony >= 3.4)
