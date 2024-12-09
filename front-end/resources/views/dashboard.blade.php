<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard test emworkgroup</title>
    
</head>

<body>
    <div class="navbar">
        <h1>Dashboard test emworkgroup</h1>
    </div>
    <div class="container">
        <!-- Form -->
        <div class="form-container">
            <form id="add-form">
                <div class="form-group">
                    <label for="prefix">คำนำหน้า:</label>
                    <input list="prefix-list" id="prefix" name="prefix" required>
                    <datalist id="prefix-list">
                        <option value="นาย">
                        <option value="นาง">
                        <option value="นางสาว">
                    </datalist>
                </div>
                <div class="form-group">
                    <label for="name">ชื่อ:</label>
                    <input type="text" id="name" name="firstName" required>
                </div>
                <div class="form-group">
                    <label for="last-name">นามสกุล:</label>
                    <input type="text" id="last-name" name="lastName" required>
                </div>
                <div class="form-group">
                    <label for="dob">วันเกิด:</label>
                    <input type="date" id="dob" name="dateOfBirth" required>
                </div>
                <div class="form-group">
                    <label for="image">ภาพ:</label>
                    <input type="file" id="image" name="profileImage" accept="image/*">
                </div>
                <button type="submit" class="btn add">บันทึก</button>
            </form>
        </div>

       
        <div class="table-container">
            <div class="search-sort-container">
                <input type="text" id="search-input" placeholder="ค้นหาชื่อ-นามสกุล">
                <button id="search-btn" class="btn">ค้นหา</button>
                <button id="sort-btn" class="btn">เรียงลำดับตามอายุ</button>
            </div>

            <table id="data-table">
                <thead>
                    <tr>
                        <th>คำนำหน้า</th>
                        <th>ชื่อ</th>
                        <th>นามสกุล</th>
                        <th>วันเกิด</th>
                        <th>อายุ</th>
                        <th>ภาพ</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>


    <div class="container">
        <canvas id="ageChart" width="500" height="100"></canvas>
    </div>


</body>

</html>


<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const BASE_URL = 'http://localhost:5000'; //  API port 5000
    const apiEndpoints = {
        getAll: `${BASE_URL}/itemlist/getallitem`,
        add: `${BASE_URL}/itemlist/additem`,
        update: (id) => `${BASE_URL}/itemlist/updateitem/${id}`,
        delete: (id) => `${BASE_URL}/itemlist/deleteitem/${id}`
    };

    let itemsCache = []; //  API

    async function fetchItems() {
        try {
            const response = await axios.get(apiEndpoints.getAll);
            itemsCache = response.data;
            renderTable(itemsCache);
        } catch (error) {
            console.error('Error fetching items:', error);
        }
    }

    function renderTable(items) {
        const tableBody = document.querySelector('#data-table tbody');
        tableBody.innerHTML = '';
        items.forEach(item => {
            const row = `
            <tr>
                <td>${item.prefix}</td>
                <td>${item.firstName}</td>
                <td>${item.lastName}</td>
                <td>${new Date(item.dateOfBirth).toLocaleDateString()}</td>
                <td>${item.age}</td>
                <td><img src="${item.profileImage}" alt="Profile" width="50"></td>
                <td>
                    <button class="btn edit" data-id="${item._id}">แก้ไข</button>
                    <button class="btn delete" data-id="${item._id}">ลบ</button>
                </td>
            </tr>`;
            tableBody.innerHTML += row;
        });
        attachEditEvent();
        attachDeleteEvent();
    }

    async function addItem(formData) {
        try {
            await axios.post(apiEndpoints.add, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            alert('เพิ่มรายการสำเร็จ!');
            fetchItems();
        } catch (error) {
            console.error('Error adding item:', error);
        }
    }

    async function updateItem(id, formData) {
        try {
            await axios.put(apiEndpoints.update(id), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            alert('แก้ไขรายการสำเร็จ!');
            fetchItems();
        } catch (error) {
            console.error('Error updating item:', error);
        }
    }

    function attachEditEvent() {
        document.querySelectorAll('.btn.edit').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                const id = e.target.getAttribute('data-id');

              
                const prefix = row.children[0].textContent;
                const firstName = row.children[1].textContent;
                const lastName = row.children[2].textContent;
                const dateOfBirth = new Date(row.children[3].textContent).toISOString().split('T')[0];

              
                document.getElementById('prefix').value = prefix;
                document.getElementById('name').value = firstName;
                document.getElementById('last-name').value = lastName;
                document.getElementById('dob').value = dateOfBirth;

                
                const form = document.getElementById('add-form');
                form.onsubmit = async (event) => {
                    event.preventDefault();
                    const formData = new FormData(form);
                    formData.append('age', new Date().getFullYear() - new Date(formData.get(
                        'dateOfBirth')).getFullYear());
                    await updateItem(id, formData);

                    
                    form.onsubmit = handleAddItem;
                    form.reset();
                };
            });
        });
    }

    function attachDeleteEvent() {
        document.querySelectorAll('.btn.delete').forEach(button => {
            button.addEventListener('click', async (e) => {
                const id = e.target.getAttribute('data-id');
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?')) {
                    try {
                        await axios.delete(apiEndpoints.delete(id));
                        alert('ลบรายการสำเร็จ!');
                        fetchItems();
                    } catch (error) {
                        console.error('Error deleting item:', error);
                    }
                }
            });
        });
    }

    async function handleAddItem(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('age', new Date().getFullYear() - new Date(formData.get('dateOfBirth')).getFullYear());
        await addItem(formData);
        e.target.reset();
    }

 
    function handleSearch() {
        const searchQuery = document.getElementById('search-input').value.toLowerCase();
        const filteredItems = itemsCache.filter(item =>
            item.firstName.toLowerCase().includes(searchQuery) ||
            item.lastName.toLowerCase().includes(searchQuery)
        );
        renderTable(filteredItems);
    }

    
    function handleSort() {
        const sortedItems = [...itemsCache].sort((a, b) => a.age - b.age); // เรียงลำดับตามอายุ
        renderTable(sortedItems);
    }

   
    document.getElementById('add-form').onsubmit = handleAddItem;


    fetchItems();

    
    document.getElementById('search-btn').addEventListener('click', handleSearch);
    document.getElementById('sort-btn').addEventListener('click', handleSort);








    async function fetchAndRenderChart() {
            try {
                const response = await axios.get(apiEndpoints.getAll);
                const items = response.data;

                
                const ageGroups = {};
                items.forEach(item => {
                    const age = item.age;
                    ageGroups[age] = (ageGroups[age] || 0) + 1; 
                });

                
                const labels = Object.keys(ageGroups);
                const data = Object.values(ageGroups);

               
                renderChart(labels, data);
            } catch (error) {
                console.error('Error fetching items:', error);
            }
        }

     
        function renderChart(labels, data) {
            const ctx = document.getElementById('ageChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวนสมาชิกตามอายุ',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

      
        fetchAndRenderChart();
</script>












<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
    }

    .navbar {
        background-color: #007bff;
        color: white;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar h1 {
        margin: 0;
    }

    .container {
        padding: 20px;
    }

    .table-container {
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn.edit {
        background-color: #ffc107;
        color: white;
    }

    .btn.delete {
        background-color: #dc3545;
        color: white;
    }

    .btn.add {
        background-color: #28a745;
        color: white;
        margin-bottom: 20px;
    }

    .form-container {
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }


    .search-sort-container {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
    }

    .search-sort-container input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        flex: 1;
    }

    .search-sort-container button {
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>
