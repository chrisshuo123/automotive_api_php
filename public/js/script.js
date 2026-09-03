// Insert Car Form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('insertCarForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const BASEURL = 'http://localhost/automotive_api_php/public';
            console.log('BASEURL: ', BASEURL);
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            fetch(BASEURL + '/crud/insertCar', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(data)
            })
            .then(response => response.text())
            .then(text => {
                console.log('Raw response: ', text); // Debug
                const data = JSON.parse(text);
                console.log('Parsed Data: ', data); // Debug
                console.log('data.success: ', data.success); // Debug

                if(data.success === true) {
                    location.reload();
                } else {
                    alert('Gagal menambah mobil!');
                }
            })
            .catch(error => console.error('Error: ', error));
        });
    }
});

// For Loading All List of Cars Row Data
export async function getAllCars() {
    try {
        const response = await fetch('http://localhost/automotive_api_php/public/crud/getCars');
        const result = await response.json();
        return result.data;  // Ambil array-nya dari dalam wrapper
    } catch(error) {
        console.error('Error fetching cars: ', error);
        return [];
    }
}

// To Delete a Car Selected
export function deleteCar(idcars) {
    if(confirm('Are you sure you want to delete this car?')) {
        console.log('Deleting car with ID: ', idcars);

        fetch('http://localhost/automotive_api_php/public/crud/deleteCar', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'idcars=' + idcars
        })
        .then(response => {
            console.log('Response Status: ', response.status);
            console.log('Response Headers: ', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data: ', data);
            if(data.success) {
                location.reload();
            } else {
                console.log('Delete failed: ', data);
                alert('Failed to delete car.');
            }
        })
        .catch(error => {
            console.error('Fetch Error: ', error);
            alert('Error: ', error.message());
        });
    }
}

// To Load Lists of Brand Rows, primarily for the Modal Pop-Up Edit
export async function getAllBrands() {
    try {
        const response = await fetch('http://localhost/automotive_api_php/public/crud/getMerek');
                                // .then(res => res.json())             // Debugging
                                // .then(console.log(result.data[0]));  // Debugging (; atas hapus)
        const result = await response.json();
        return result.data.map(m => ({
            value: m.idmerek,   // ← cek nama field asli dulu, lihat catatan di bawah
            label: m.namamerek  // ← cek nama field asli dulu, lihat catatan di bawah
        }));
    } catch(error) {
        console.error('Error fetching brand list in getAllBrands: ', error);
        return [];
    }
}

export async function insertBrand(namaMerek) {
    try {
        const response = await fetch('http://localhost/automotive_api_php/public/crud/addMerek', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'namamerek= ' + encodeURIComponent(namaMerek)
        });
        const data = await response.json();
        return data;
    } catch(error) {
        console.error('Error inserting brand: ', error);
        return { success: false };
    }
}

export async function editBrand(idMerek, namaMerek) {
    try {
        const response = await fetch('http://localhost/automotive_api_php/public/crud/editMerek', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'idmerek=' + encodeURIComponent(idMerek) + '&namamerek=' + encodeURIComponent(namaMerek)
        });
        const result = await response.json();
        return result;
    } catch(error) {
        console.error('Error editing brand: ', error);
        return { success: false };
    }
}

export async function deleteBrand(idMerek) {
    try {
        const response = await fetch('http://localhost/automotive_api_php/public/crud/deleteMerek', {
            method: 'POST', // Perlu sejenis Drop?
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'idmerek=' + encodeURIComponent(idMerek)
        });
        const result = await response.json();
        return result;
    } catch(error) {
        console.error('Error deleting brand: ', error);
        return { success: false };
    }
}

export async function getAllTypes() {
    try {
        const response = await fetch('http://localhost/automotive_api_php/public/crud/getJenis');
        const result = await response.json();
        return result.data.map(j => ({
            value: j.idjenis,
            label: j.namajenis
        }));
    } catch(error) {
        console.error('Error fetching type list: ', error);
        return [];
    }
}