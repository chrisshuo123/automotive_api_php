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

function deleteCar(idcars) {
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