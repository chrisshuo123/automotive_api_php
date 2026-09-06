<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Car Inventory</title>
</head>
<body>
    <div class="container">
        <h1>Car Inventory</h1>
        <!-- Search Box -->
        <input type="text" id="searchInput" placeholder="Search by car title...">
        <button id="searchBtn">Search</button>
        <!-- Alphabet Sorting -->
        <select id="sortSelect">
            <option value="">Default</option>
            <option value="ascending">A-Z sort</option>
            <option value="descending">Z-A sort</option>
        </select>
        <!-- Brand Filter -->
        <div class="filter-box">
            <label for="brandFilter">Brand:</label>
            <select id="brandFilter">
                <option value="">All Brand</option>
            </select>
        </div>
        <!-- Type Filter -->
        <div class="filter-box">
            <label for="typeFilter">Brand:</label>
            <select id="typeFilter">
                <option value="">All Brand</option>
            </select>
        </div>
        <div class="car-list" id="carList">
            <!-- Cars will be loaded here by JavaScript -->
            <div class="loading"><p>Loading cars...</p></div>
        </div>
    </div>
    <script type="module" src="http://localhost/automotive_api_php/public/js/publicList.js"></script>
</body>
</html>