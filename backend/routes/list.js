const express = require('express');
const upload = require('../config/uploadConfig'); 
const { addItem, getItems, updateItem, deleteItem } = require('../controllers/itemcontroller');

const router = express.Router();

router.post('/additem', upload.single('profileImage'), addItem);
router.get('/getallitem', getItems);
router.put('/updateitem/:id', upload.single('profileImage'), updateItem);
router.delete('/deleteitem/:id', deleteItem);

module.exports = router;
