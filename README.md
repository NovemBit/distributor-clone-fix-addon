# distributor-clone-fix-addon

Distributor Clone Fix add-on is for extending the [Distributor Plugin](https://distributorplugin.com/) functionality to fix subscriptions if spoke was cloned

## Requirements

* PHP 5.6+
* [WordPress](http://wordpress.org) 4.7+
* Distributor plug-in


## Plugin installation

- You can build plugin yourself (see [development](#development-and-manual-builds)) or [download latest stable build](https://github.com/NovemBit/distributor-clone-fix-addon/releases/download/1.0/distributor-clone-fix-addon.zip)

## Usage example

Lets assume you've created external connection with distributor plugin from `Source` site to `Destination`. Then you've distributed some posts to `Destination` and in some reason you needed `Destination` to be cloned. So, you cloned it, we'll name it `Clone`. Now you created external connection for `Clone` from `Destination`. Now, the problem is, that all your posts in `Clone` will have `Subscription` created for `Destination`. This add-on will resolve that case. Let's do it step by step  
- Install `Distributor Clone Fix` add-on on both sides  ( `Source` & `Clone` ).
- After You've installed add-on, `Fix Connection` action will appear in `wp-admin` post listing pages bulk actions. 
![screenshot](https://i.snag.gy/lr6Ca9.jpg)
- Please note that post type must be distributable. see `distributable_post_types` function in `utils.php` [distributor plugin](https://github.com/NovemBit/distributor) 
- Select Posts that you want to be fixed, then select `Fix Connection` from bulk actions and press `apply`. 
- You will be prompted to select connection, for our example it will be connection created for `Clone`. Select it and press `apply` again. 
- That's all! After action completed add-on will show you notification about success or failure.
- Please note that this add-on was tested for single-site installation with external connections.

## Development and manual builds
 - We are using [Webpack](https://webpack.js.org/) for assets compiling and minify and [Babel](https://babeljs.io/) for transpiling JavaScript code.
 - So, you need [npm](https://nodejs.org/en/) and [composer](https://getcomposer.org/) installed.
 - After you cloned repository, make sure to run `npm i && composer install` in repository root. 
 - Then you must build assets. Simply run `npm run build`.
