const webpack = require("webpack");
const path = require("path");

config = {
    entry: "./gp_forms.js",
    output: {
        filename: "../js/gp_forms.js"
    },
    module: {
        loaders: [{
            test: /\.js$/,
            exclude: "/node_modules/",
            use: {
                loader: "babel-loader",
                query: {
                    presets: ["es2015"]
                }
            }
        }]
    }
};

module.exports = config;
