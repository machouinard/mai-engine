# Mai Engine

The required plugin to power Mai themes.

## Development

Mai Engine makes use of two package managers, NPM for JavaScript and Composer for PHP packages.

### Setup the development environment

1. Clone this repository into your WordPress site's `plugins` directory.

    ```shell
    git clone https://github.com/maithemewp/mai-engine.git
    ```

2. Change directories into the plugin folder from the command line:

    ```shell
    cd mai-engine
    ```

3. Install Composer and any PHP dependencies with the following command:

    ```shell
    composer install
    ```

    *Please note that this step requires that you have Composer installed globally on your machine. We recommend using Homebrew to install Composer: `brew install composer`*
    
    ```shell
    export PATH="$HOME/.composer/vendor/bin:$PATH"
    ```

4. Install Node packages:

    ```shell
    npm install
    ```

    *Please note that this step requires that you have Node installed globally on your machine. We recommend using Homebrew to install Node: `brew install node`*
    
    
### Composer scripts

Mai Engine uses PHP Code Sniffer for linting and fixing coding standards. To lint all PHP files against WordPress coding standards run the following command:

```shell
composer phpcs
```

To have PHP Code Sniffer attempt to automatically fix any warnings run the following:

```shell
composer phpcbf
```

### NPM scripts

Mai Engine utilizes Gulp and Sass to automate tedious tasks, such as automatically generating the many stylesheets required by the child themes.

First you will need to install NPM on your machine:

```shell
brew install npm
```

It is also recommended to install NVM (Node Version Manager) to allow easy switching of Node versions:

```shell
brew install nvm
```

Next, install the Gulp CLI globally on your machine. To install Gulp CLI run the following command from the terminal:

```shell
 sudo npm install gulp-cli -g
```

Now that all of the global packages are installed, navigate to the root directory of this plugin, e.g:

```shell
cd Sites/my-project/wp-content/plugins/mai-engine
```

From there, make sure that Node is running the correct version (11.15.0). To do this, you will first need to run the following commands to configure nvm correctly:

```shell
export NVM_DIR="$HOME/.nvm" # Sets the path to NVM 
[ -s "/usr/local/opt/nvm/nvm.sh" ] && . "/usr/local/opt/nvm/nvm.sh"  # This loads nvm
[ -s "/usr/local/opt/nvm/etc/bash_completion.d/nvm" ] && . "/usr/local/opt/nvm/etc/bash_completion.d/nvm"  # This loads nvm bash_completion
```

Then you will be able to use NVM commands. Run the following to switch to the correct version:

```shell
nvm install 11.15.0
nvm use 11.15.0
```

You should see the following message in the terminal:

```shell
Now using node v11.15.0 (npm v6.7.0)
```

#### Gulp
 
Once the Gulp CLI and Node packages have been installed, you are ready to begin using the following Gulp tasks to automate development:

**Default**

Running the default gulp task will kick of development and Gulp will watch files for changes. When a change to a file is detected Gulp will run the build tasks and recompile assets. 

```shell
gulp
```

**CSS**

```shell
gulp build:css
```

**JS**

```shell
gulp build:js
```

### Using the CSS system

Goals of the CSS system: Keep it DRY. Prioritize performance.

Please note: files in the `assets/css/min/` directory should never be edited directly as any changes will be overridden when running the gulp build task. All changes should be made to the SCSS files in the `assets/scss/` directory and then compiled using the `gulp build:css` command.

**Organization**

This project follows the ITCSS principal to organize the CSS files in such a way that they can better deal with CSS specificity. One of the key principles of ITCSS is that it separates your CSS codebase to several sections (called layers), which take the form of the inverted triangle. The structured is also loosely based on the [Sass Guidelines](https://sass-guidelin.es/). More information about ITCSS can be found [here](https://www.xfive.co/blog/itcss-scalable-maintainable-css-architecture/).

- **Abstracts** – used with preprocessors and contain font, colors definitions, globally used mixins and functions. It’s important not to output any CSS in this layer.
- **Base** – reset and/or normalize styles, box-sizing definition, styling for bare HTML elements (like H1, A, etc.). These come with default styling from the browser so we can redefine them here. This is the first layer which generates actual CSS.
- **Layout** – the layout/ folder contains everything that takes part in laying out the site or application. These elements are usually only in one place and contain multiple components.
- **Components** – specific UI components. This is where the majority of our work takes place and our UI components are often composed of Objects and Components
- **Utilities** – utilities and helper classes with ability to override anything which goes before in the triangle, eg. hide helper class
- **Plugins** - styling for third party plugins. Not imported in the main stylesheet.

### Variables

Mai Engine has 3 types of variables, all of which have different purposes:

#### JSON variables

JSON files can be read by both Gulp and PHP, which means they can be written in one place but accessible in all areas of the project.

Theme JSON variables are defined in the `config/theme-name/config.json` file.

##### JSON variables in SCSS

Any variables defined in the JSON file are usable in SCSS files and will be processed by Gulp during compilation. For example, define some colors:

```json
{
  "breakpoint": 1200,
  "colors": {
    "primary": "#fb2056",
    "heading": "#232c39"
  }
}
```

These can be used in SCSS files in the following way:

```scss
body {
    color: map_get($colors, primary);
}

h1 {
    color: map_get($colors, heading);
}

.wrap {
    max-width: $breakpoint;
}
```

##### JSON variables in PHP

To access the JSON variables with PHP, use the following helper function:

```php
mai_get_color( 'primary' );
```

This will check the currently active child themes config for the variable, and if it's not found will use the default config variable.

#### SCSS variables

While JSON variables can be used in SCSS, not all SCSS variables need to be defined in the JSON config. The majority of variables used throughout the SCSS framework are defined in the `assets/scss/settings` directory.

#### CSS Custom Properties

CSS Custom Properties are different to SCSS variables in that they can be changed at runtime, unlike SCSS variables which are compiled prior to deployment. That being said, Custom Properties work with SCSS, as seen in this project.

Mai Engine makes extensive use of CSS Custom Properties to reduce overall file size and allow for easier child theme customizations. Global CSS Custom Properties are defined in one place in the plugin, the `assets/scss/base/_globals.scss` file. 
