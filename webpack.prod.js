const merge = require('webpack-merge');
const path = require('path');
const WebpackFtpUpload = require('webpack-ftp-upload-plugin');
const common = require('./webpack.common.js');

module.exports = merge(common, 
  {
    mode: 'production',
    plugins: [
      new WebpackFtpUpload({
        host: 'ftp.jenspeter.net',
        port: '22',
        username: 'jenspeter.net',
        password: 'Data1969d',
        local: path.join(__dirname, 'dist'),
        path: '/customers/5/c/8/jenspeter.net/httpd.www/some2'
      })
    ]
  });