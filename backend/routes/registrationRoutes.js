const express = require('express');
const { createRegistration, getRegistrations } = require('../controllers/registrationController');

const router = express.Router();

router.post('/register', createRegistration);
router.get('/register', getRegistrations);

module.exports = router;
