import { getAllCars } from './script.js';

const carListEl = document.getElementById('carList');
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const clearBtn = document.getElementById("clearBtn");

let allCars = [];  // Populated once on load, then filtered in memory

function renderCarItem(car) {
    const status = car.status ?? 'Not Specified';
    const bgColor = status === 'Approved' ? 'green' : 'yellow';
    const textColor = status === 'Approved' ? 'white' : 'black';

    return `
        <div class="car-item">
            <div class="flex-crud">
                <div style="width: 200px;">
                    <h3>${car.nama_mobil}</h3>
                    <p><b>Brand: </b>${car.namaMerek ?? 'Not Specified'}</p>
                    <p><b>Type: </b>${car.namaJenis ?? 'Not Specified'}</p>
                    <p><b>Horse Power: </b>${car.horse_power ?? 'N/A'}</p>
                    <p><b>Status: </b><br>
                        <span style="background-color: ${bgColor}; color: ${textColor}; padding: 5px; border-radius: 5px;">
                            ${status}
                        </span>
                    </p>
                    <div class="flex-crud-button">
                        <button class="edit-btn" data-id="${car.idcars}">Edit</button>
                        <button class="delete-btn" data-id="${car.idcars}">Delete</button>
                    </div>
                </div>
                <div>
                    <img src="../public/img/${car.imageCar ?? 'default.jpg'}"
                        style="width: 75%; max-width: 800px; height: auto; margin-left: 150px;">
                </div>
            </div>
        </div>
    `;
}

function renderTable(data) {
    if (!data || data.length === 0) {
        carListEl.innerHTML = '<p>No cars found.</p>';
        return;
    }
    carListEl.innerHTML = data.map(renderCarItem).join('');
}

function filterData() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const statusValue = statusFilter.value;

    if(searchTerm === '' && (statusValue === '' || statusValue === 'all')) {
        renderTable(allCars);
        return;
    }

    const filtered = allCars.filter((car) => {
        const nameLower = car.nama_mobil.toLowerCase();
        const statusLower = (car.status ?? '').toLowerCase();

        const matchesSearch = searchTerm === '' ||
            nameLower.includes(searchTerm) ||
            statusLower.includes(searchTerm);

        const matchesStatus = statusValue === '' || statusValue === 'all' ||
            statusLower === statusValue.toLowerCase();

        return matchesSearch && matchesStatus;
    });

    renderTable(filtered);
}

// Event Delegation for edit / delete, since rows are re-rendered dynamically
carListEl.addEventListener('click', (e) => {
    const editBtn = e.target.closest('.edit-btn');
    const delBtn = e.target.closest('.delete-btn');
    if (editBtn) editCar(editBtn.dataset.id);
    if (delBtn) deleteCar(delBtn.dataset.id);
});

searchInput.addEventListener('input', filterData);
statusFilter.addEventListener('change', filterData);
clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    statusFilter.value = '';
    renderTable(allCars);
});

// Load once - PHP already rendered the first view, this just primes
// 'allCars' in memory so filtering doesn't need a new fetch every time
(async function init() {
    allCars = await getAllCars();
    console.log('allCars: ', allCars); // cek bentuknya di sini dulu
})();