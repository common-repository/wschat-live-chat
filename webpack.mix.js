const mix = require('laravel-mix');

var LiveReloadPlugin = require('webpack-livereload-plugin');

mix.webpackConfig({
    plugins: [new LiveReloadPlugin()]
});

mix.options({
    // Don't perform any css url rewriting by default
    processCssUrls: false,
    terser: {
        extractComments: false,
    }
})

if (process.env.SOURCE) {
	mix.js('./resources/js/admin_chat.js', './resources/dist/admin-chat.js')
    	.js('./resources/js/user_chat.js', './resources/dist/user-chat.js')
    	.js('./resources/js/admin-tags.js', './resources/dist/admin-tags.min.js')
    	.js('./resources/js/admin_history.js', './resources/dist/admin-history.min.js')
    	.js('./resources/js/global_alert.js', './resources/dist/global_alert.min.js')
    .js('./resources/js/plugins/prechat-form/field-settings.js', './resources/js/plugins/prechat-form/field-settings.min.js');
} else {
	mix
    	.sass('./resources/scss/bootstrap.scss', './resources/dist/base.css', [])
    	.js('./resources/js/user_chat.js', './resources/dist/user-chat.js')
    	.js('./resources/js/admin_chat.js', './resources/dist/admin-chat.js')
    	.js('./resources/js/live-visitors.js', './resources/dist/live-visitors.js')
    	.js('./resources/js/admin_history.js', './resources/dist/admin-history.min.js')
    	.js('./resources/js/admin-tags.js', './resources/dist/admin-tags.min.js')
    	.js('./resources/js/global_alert.js', './resources/dist/global_alert.min.js')
		.js('./resources/js/plugins/prechat-form/field-settings.js', './resources/js/plugins/prechat-form/field-settings.min.js');
	}
