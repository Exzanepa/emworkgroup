const mongoose = require('mongoose');

const registrationSchema = new mongoose.Schema({
  name: String,
  email: String,
  phone: String,
  checkInDate: Date,
  checkOutDate: Date,
  roomType: String,
  specialRequests: String,
  additionalField: String
});

const Registration = mongoose.model('Registration', registrationSchema);

module.exports = Registration;
