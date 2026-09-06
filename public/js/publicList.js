import { getAllCars, getAllBrands, getAllTypes, BASEURL } from './script.js';

const carListEl = document.getElementById('carList');
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const sortSelect = document.getElementById('sortSelect');
const brandFilter = document.getElementById('brandFilter');
const typeFilter = document.getElementById('typeFilter');

let allCars = [];  // Populated once on load, then filtered in memory

function renderCarCard(car) {
    return `
        <div class="car-card">
            <h2>${car.nama_mobil}</h2>
            <img src="${BASEURL}/img/${car.nama_foto ?? 'default.jpg'}" style="width: 100%; max-width: 800px; height: auto;">
            <div class="car-details">
                <p><b>Brand: </b>${car.merek ?? 'Not Specified'}</p>
                <p><b>Jenis: </b>${car.jenis ?? 'Not Specified'}</p>
                <p><b>Horse Power: </b>${car.horse_power ?? 'N/A'} CC</p>
            </div>
        </div>
    `;
}

function renderTable(data) {
    if (!data || data.length === 0) {
        carListEl.innerHTML = '<div class="no-cars">No approved cars found.</div>';
        return;
    }
    carListEl.innerHTML = data.map(renderCarCard).join('');
}

function filterData() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const sortValue = sortSelect.value;
    const brandValue = brandFilter.value;
    const typeValue = typeFilter.value;

    // Ini tidak perlu, karena sudah dipastikan oleh si elemen2 const searchInput diatas.
    // if(searchTerm === '') {
    //     renderTable(allCars);
    //     return;
    // }

    const filtered = allCars.filter((car) => {
        const nameLower = car.nama_mobil.toLowerCase();
        const brandLower = (car.merek ?? '').toLowerCase();
        const typeLower = (car.jenis ?? '').toLowerCase();

        // Search filterring
        const matchesSearch = searchTerm === '' ||
                nameLower.includes(searchTerm) ||
                brandLower.includes(searchTerm) ||
                typeLower.includes(searchTerm);

        // Brand Filterring
        const matchesBrand = brandValue === '' || brandValue === 'all' ||
            brandLower === brandValue.toLowerCase();
        // Type Filterring
        const matchesType = typeValue === '' || typeValue === 'all' ||
            typeLower === typeValue.toLowerCase();

        return matchesSearch && matchesBrand && matchesType;
    });

    // Sort the filtered car data
    console.log('sebelum sort, filtered[0]: ', filtered[0]?.nama_mobil);

    if(sortValue === 'ascending') {
        console.log('menjalankan sort ascending');
        filtered.sort((a,b) => a.nama_mobil.localeCompare(b.nama_mobil));
    } else if(sortValue === 'descending') {
        console.log('menjalankan sort descending');
        filtered.sort((a,b) => b.nama_mobil.localeCompare(a.nama_mobil));
    }

    // Setelah Sort
    console.log('setelah sort, filtered[0]: ', filtered[0]?.nama_mobil);

    renderTable(filtered);
}

async function populateBrandFilter() {
    const brand = await getAllBrands();
    brandFilter.innerHTML = `<option value="">All Brand</option>` +
        brand.map(b => `<option value="${b.label}">${b.label}</option>`).join('');
}

async function populateTypeFilter() {
    const jenis = await getAllTypes();
    typeFilter.innerHTML = `<option value="">All Type</option>` +
        jenis.map(j => `<option value="${j.label}">${j.label}</option>`).join('');
}

searchBtn.addEventListener('click', filterData);
searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        filterData();
    }
});
searchInput.addEventListener('keyup', (e) => {
    if (e.key === 'Backspace' || e.key === 'Delete') {
        filterData();
    }
});

// Sertakan Listener
sortSelect.addEventListener('change', filterData);
brandFilter.addEventListener('change', filterData);
typeFilter.addEventListener('change', filterData);

(async function init() {
    const rawCars = await getAllCars();
    allCars = rawCars.filter(car => car.status === 'Approved');  // <- simpan HASIL FILTER ke allCars

    await populateBrandFilter();
    await populateTypeFilter();
    renderTable(allCars);
})();