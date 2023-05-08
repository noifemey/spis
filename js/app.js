const express = require('express')
const app = express();
app.use('/lada', express.static(__dirname + '/node_modules/ladda/js/ladda.js'));
