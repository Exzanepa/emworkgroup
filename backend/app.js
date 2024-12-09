const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const registrationRoutes = require('./routes/registrationRoutes');
const itemlist = require('./routes/list');
// const list = require('./routes/list');
const app = express();

// Middleware
app.use(bodyParser.json());
app.use(cors());

// Routes
app.use('/api', registrationRoutes);
app.use('/itemlist', itemlist);

module.exports = app;
