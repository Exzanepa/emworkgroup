const Registration = require('../models/registration');

exports.createRegistration = async (req, res) => {
  const newRegistration = new Registration(req.body);
  try {
    const savedRegistration = await newRegistration.save();
    res.status(201).json(savedRegistration);
  } catch (error) {
    res.status(400).json({ message: error.message });
  }
};

exports.getRegistrations = async (req, res) => {
  try {
    const registrations = await Registration.find();
    res.status(200).json(registrations);
  } catch (error) {
    res.status(400).json({ message: error.message });
  }
};
