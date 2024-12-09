const mongoose = require('mongoose');

const itemSchema = new mongoose.Schema({
    prefix: { type: String, required: true },
    firstName: { type: String, required: true },
    lastName: { type: String, required: true },
    dateOfBirth: { type: Date, required: true },
    age: { type: Number, required: true },
    profileImage: { type: String, required: true },
}, { timestamps: true });

module.exports = mongoose.model('Item', itemSchema);
