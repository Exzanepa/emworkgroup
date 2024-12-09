const multer = require('multer');
const path = require('path');
const Item = require('../models/item');

// Fetch all items
exports.getItems = async (req, res) => {
    try {
        const items = await Item.find();
        res.status(200).json(items);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

// Add a new item
exports.addItem = async (req, res) => {
    try {
        const { prefix, firstName, lastName, dateOfBirth, age } = req.body;
        const profileImage = req.file ? req.file.path : null;

        if (!prefix || !firstName || !lastName || !dateOfBirth || !age || !profileImage) {
            return res.status(400).json({ message: 'ข้อมูลไม่ครบถ้วน' });
        }

        const newItem = await Item.create({
            prefix,
            firstName,
            lastName,
            dateOfBirth,
            age,
            profileImage,
        });

        res.status(201).json(newItem);
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ error: error.message });
    }
};


// Update an item
exports.updateItem = async (req, res) => {
    try {
        const { id } = req.params;
        const { prefix, firstName, lastName, dateOfBirth } = req.body;

        
        const profileImage = req.file ? req.file.path : null;

        const updatedFields = { prefix, firstName, lastName, dateOfBirth };
        if (profileImage) {
            updatedFields.profileImage = profileImage;
        }

        
        if (dateOfBirth) {
            const dob = new Date(dateOfBirth);
            updatedFields.age = new Date().getFullYear() - dob.getFullYear();
        }

        const updatedItem = await Item.findByIdAndUpdate(
            id,
            updatedFields,
            { new: true }
        );

        if (!updatedItem) {
            return res.status(404).json({ message: 'ไม่พบรายการที่ต้องการอัปเดต' });
        }

        res.status(200).json(updatedItem);
    } catch (error) {
        console.error(error.message);
        res.status(500).json({ error: error.message });
    }
};

// Delete an item
exports.deleteItem = async (req, res) => {
    try {
        const { id } = req.params;
        const deletedItem = await Item.findByIdAndDelete(id);

        if (!deletedItem) {
            return res.status(404).json({ message: 'ไม่พบรายการที่ต้องการลบ' });
        }

        res.status(200).json({ message: 'ลบรายการสำเร็จ', data: deletedItem });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};





const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'uploads/');
    },
    filename: (req, file, cb) => {
        cb(null, `${Date.now()}-${file.originalname}`);
    },
});

const fileFilter = (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png/;
    const isValid = allowedTypes.test(path.extname(file.originalname).toLowerCase()) &&
        allowedTypes.test(file.mimetype);
    if (isValid) {
        cb(null, true);
    } else {
        cb(new Error('รองรับเฉพาะไฟล์ภาพ (jpeg, jpg, png) เท่านั้น'));
    }
};

const upload = multer({ storage, fileFilter });
