const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
module.exports = {
	optimization: {
		minimizer: [
			new OptimizeCSSAssetsPlugin({}),
			new TerserPlugin({
				test: /\.js(\?.*)?$/i,
				sourceMap: true,
				terserOptions: {
					warnings: false,
					parse: {},
					compress: {},
					mangle: true, // Note `mangle.properties` is `false` by default.
					output: null,
					toplevel: false,
					nameCache: null,
					ie8: false,
					keep_fnames: false,
				},
			}),
		],
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: '../css/[name].min.css',
		}),
	],
	entry: {
		bulk: [
			path.join(__dirname, 'assets/js/bulk.js'),
			path.join(__dirname, 'assets/css/bulk.css'),
		],
	},
	output: {
		path: path.join(__dirname, 'build/js/'),
		filename: '[name].min.js',
	},
	module: {
		rules: [
			{
				test: /\.js/,
				exclude: /(node_modules|bower_components)/,
				use: [
					{
						loader: 'babel-loader',
					},
				],
			},
			{
				test: /\.css$/,
				use: [MiniCssExtractPlugin.loader, 'css-loader'],
			},
		],
	},
	stats: {
		colors: true,
	},
	devtool: 'source-map',
};
