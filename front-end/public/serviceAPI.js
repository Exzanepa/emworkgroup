import axios from 'axios';

const BASE_URL = 'http://localhost:5000';
const apiEndpoints = {
    addItem: `${BASE_URL}/itemlist/additem`,
    getAllItems: `${BASE_URL}/itemlist/getallitem`,
    updateItem: (id) => `${BASE_URL}/itemlist/updateitem/${id}`,
    deleteItem: (id) => `${BASE_URL}/itemlist/deleteitem/${id}`,
};


export async function fetchItems() {
    try {
        const response = await axios.get(apiEndpoints.getAllItems);
        const items = response.data;

        const tableBody = document.querySelector('#data-table tbody');
        tableBody.innerHTML = ''; 

        items.forEach((item) => {
            const row = `
                <tr>
                    <td>${item.prefix}</td>
                    <td>${item.firstName}</td>
                    <td>${item.lastName}</td>
                    <td>${new Date(item.dateOfBirth).toLocaleDateString()}</td>
                    <td>${item.age}</td>
                    <td><img src="${item.profileImage}" alt="Profile" width="50"></td>
                    <td>${new Date(item.updatedAt).toLocaleString()}</td>
                    <td>
                        <button class="edit-btn" data-id="${item._id}">แก้ไข</button>
                        <button class="delete-btn" data-id="${item._id}">ลบ</button>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });

        
        document.querySelectorAll('.edit-btn').forEach((btn) => btn.addEventListener('click', handleEdit));
        document.querySelectorAll('.delete-btn').forEach((btn) => btn.addEventListener('click', handleDelete));
    } catch (error) {
        console.error('Error fetching items:', error);
    }
}


export async function addItem(formData) {
    try {
        await axios.post(apiEndpoints.addItem, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        alert('เพิ่มรายการสำเร็จ!');
        fetchItems();
    } catch (error) {
        console.error('Error adding item:', error);
        alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล');
    }
}


export async function updateItem(id, formData) {
    try {
        await axios.put(apiEndpoints.updateItem(id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        alert('แก้ไขรายการสำเร็จ!');
        fetchItems();
    } catch (error) {
        console.error('Error updating item:', error);
        alert('เกิดข้อผิดพลาดในการแก้ไขข้อมูล');
    }
}


export async function deleteItem(id) {
    try {
        await axios.delete(apiEndpoints.deleteItem(id));
        alert('ลบรายการสำเร็จ!');
        fetchItems();
    } catch (error) {
        console.error('Error deleting item:', error);
        alert('เกิดข้อผิดพลาดในการลบข้อมูล');
    }
}


async function handleDelete(event) {
    const id = event.target.getAttribute('data-id');
    if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?')) {
        await deleteItem(id);
    }
}


function handleEdit(event) {
    const id = event.target.getAttribute('data-id');
    
    const row = event.target.closest('tr');
    const prefix = row.children[0].textContent;
    const firstName = row.children[1].textContent;
    const lastName = row.children[2].textContent;
    const dateOfBirth = new Date(row.children[3].textContent).toISOString().split('T')[0];

    
    document.getElementById('prefix').value = prefix;
    document.getElementById('name').value = firstName;
    document.getElementById('last-name').value = lastName;
    document.getElementById('dob').value = dateOfBirth;

    document.getElementById('popup-form').classList.remove('hidden');

    
    const form = document.getElementById('add-form');
    form.onsubmit = async function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const dob = new Date(formData.get('dateOfBirth'));
        formData.append('age', new Date().getFullYear() - dob.getFullYear());
        await updateItem(id, formData);

        
        form.onsubmit = handleAdd;
        document.getElementById('popup-form').classList.add('hidden');
    };
}


async function handleAdd(e) {
    e.preventDefault();
    const form = document.getElementById('add-form');
    const formData = new FormData(form);

    const dob = new Date(formData.get('dateOfBirth'));
    formData.append('age', new Date().getFullYear() - dob.getFullYear());

    await addItem(formData);
    form.reset();
}
