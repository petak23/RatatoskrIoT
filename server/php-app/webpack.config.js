const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');
const AssetsWebpackPlugin = require('assets-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
//const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

const isProduction = process.env.NODE_ENV === 'production';

// Config
const ROOT_PATH = __dirname;
const options = { // config for WebpackManifestPlugin
	//path: path.join(ROOT_PATH, 'www/dist'),
	filename : 'webpack-assets.json',
  update: true,
  prettyPrint: true,
  assetsRegex: /\.(jpe?g|png|gif|svg)\?./i,
  processOutput: function (assets) {
    return `export default ${JSON.stringify(assets)};`;
  }
};


module.exports = {
	entry: {
		front: path.join(ROOT_PATH, "app/FrontModule/assets/main.js"),
	},
	output: {
		filename: isProduction ? '[name].[contenthash:5].js' : '[name].js',
		publicPath: "",
		clean: true,
		path: path.resolve(__dirname, 'www/dist')
	},
	module: {
		rules: [
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			},
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader'
				}
			},
			{
				test: /\.scss$/,
				use: [
					isProduction ? MiniCssExtractPlugin.loader : 'vue-style-loader',
					'css-loader',
					'sass-loader'
				]
			},
			{
				test: /\.(png|jpg|gif)$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: isProduction ? '[name].[hash:5].[ext]' : '[name].[ext]',
							outputPath: 'images'
						}
					}
				]
			}
		]
	},
	plugins: [
		new VueLoaderPlugin(),
		new AssetsWebpackPlugin(options),
		//new WebpackManifestPlugin(options),
		new MiniCssExtractPlugin({
			filename: isProduction ? '[name].[contenthash:5].css' : '[name].css'
		})
	],
	optimization: {
		minimizer: [
			new TerserPlugin({
				terserOptions: {
					ecma: 5,
					compress: true,
					output: {
						comments: false
					}
				}
			})
		]
	}
};
