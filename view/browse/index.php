<?php
session_start();
require_once '../../config/database.php';
/** @var \PDO $pdo */
$stmt       = $pdo->query("
    SELECT id, name, location, area, short_background
    FROM restaurants
    ORDER BY name ASC
");
$restaurants = $stmt->fetchAll();

// Load all unique areas for the filter dropdown
$areaStmt = $pdo->query("
    SELECT DISTINCT area
    FROM restaurants
    ORDER BY area ASC
");
$areas = $areaStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Restaurants — Online Food Blog</title>
    <style>
        /* ── Reset & Base ── */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        /* ── Navbar ── */
        .navbar {
            background: #c0392b;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
        }
        .navbar a:hover { text-decoration: underline; }

        /* ── Search Section ── */
        .search-section {
            background: white;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .search-section h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #c0392b;
        }
        .search-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .search-bar input,
        .search-bar select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        .search-bar input {
            flex: 1;
            min-width: 200px;
        }
        .search-bar input:focus,
        .search-bar select:focus {
            border-color: #c0392b;
        }
        .btn-search {
            background: #c0392b;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-search:hover { background: #a93226; }
        .btn-clear {
            background: #888;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-clear:hover { background: #666; }

        .main-content {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .results-count {
            color: #666;
            font-size: 14px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 40px;
            color: #888;
        }
        .spinner {
            border: 3px solid #eee;
            border-top: 3px solid #c0392b;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 25px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #c0392b;
            color: #c0392b;
        }

        .restaurant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .restaurant-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .restaurant-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .card-body { padding: 20px; }
        .card-body h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #222;
        }
        .card-meta {
            font-size: 13px;
            color: #888;
            margin-bottom: 10px;
        }
        .card-meta span { margin-right: 12px; }
        .card-bg {
            font-size: 13px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .btn-view {
            display: inline-block;
            background: #c0392b;
            color: white;
            padding: 8px 18px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-view:hover { background: #a93226; }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .menu-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .menu-card:hover { transform: translateY(-2px); }
        .menu-card-body { padding: 15px; }
        .menu-card-body h4 {
            font-size: 15px;
            margin-bottom: 5px;
        }
        .menu-price {
            color: #c0392b;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 5px;
        }
        .menu-restaurant {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #888;
            display: none;
        }
        .no-results h3 { font-size: 20px; margin-bottom: 10px; }
    </style>
</head>
<body>

<nav class="navbar">
    <strong>🍽 Online Food Blog</strong>
    <div>
        <a href="/ONLINE-FOOD-BLOG/view/browse/index.php">Browse</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/ONLINE-FOOD-BLOG/view/profile.php">Profile</a>
            <a href="/logout.php">Logout</a>
        <?php else: ?>
            <a href="/ONLINE-FOOD-BLOG/view/auth/login.php">Login</a>
            <a href="/ONLINE-FOOD-BLOG/view/auth/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="search-section">
    <h1>Find Restaurants & Food</h1>
    <div class="search-bar">

        <input
            type="text"
            id="searchInput"
            placeholder="Search restaurants or food items..."
            autocomplete="off"
        >

        <input
            type="text"
            id="locationInput"
            placeholder="Location (e.g. Dhaka)"
        >

        <select id="areaSelect">
            <option value="">All Areas</option>
            <?php foreach ($areas as $area): ?>
                <option value="<?= htmlspecialchars($area['area']) ?>">
                    <?= htmlspecialchars($area['area']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn-search" onclick="doSearch()">Search</button>
        <button class="btn-clear"  onclick="clearSearch()">Clear</button>
    </div>
</div>

<div class="main-content">

    <div class="loading" id="loadingSpinner">
        <div class="spinner"></div>
        <p>Searching...</p>
    </div>

    <div class="results-header">
        <div class="results-count" id="resultsCount">
            Showing <?= count($restaurants) ?> restaurant(s)
        </div>
    </div>

    <div class="no-results" id="noResults">
        <h3>No results found</h3>
        <p>Try a different search term or clear the filters.</p>
    </div>

    <div class="section-title" id="restaurantTitle">Restaurants</div>
    <div class="restaurant-grid" id="restaurantList">
        <?php foreach ($restaurants as $r): ?>
            <div class="restaurant-card">
                <div class="card-body">
                    <h3><?= htmlspecialchars($r['name']) ?></h3>
                    <div class="card-meta">
                        <span>📍 <?= htmlspecialchars($r['location']) ?></span>
                        <span>🏘 <?= htmlspecialchars($r['area']) ?></span>
                    </div>
                    <p class="card-bg">
                        <?= htmlspecialchars($r['short_background']) ?>
                    </p>
                    <a class="btn-view"
                       href="/ONLINE-FOOD-BLOG/view/browse/restaurant.php?id=<?= $r['id'] ?>">
                        View Restaurant
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section-title" id="menuTitle" style="display:none;">
        Food Items
    </div>
    <div class="menu-grid" id="menuList"></div>

</div>

<script>
let debounceTimer;

document.getElementById('searchInput')
    .addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doSearch, 400);
    });

document.getElementById('locationInput')
    .addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doSearch, 400);
    });

document.getElementById('areaSelect')
    .addEventListener('change', doSearch);


function doSearch() {
    const q        = document.getElementById('searchInput').value.trim();
    const location = document.getElementById('locationInput').value.trim();
    const area     = document.getElementById('areaSelect').value;
    const params = new URLSearchParams({ q, location, area });
    const url    = `/ONLINE-FOOD-BLOG/api/search.php?${params}`;

    showLoading(true);
    fetch(url)
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Server error: ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            // Hide spinner
            showLoading(false);

            if (data.success) {
                renderRestaurants(data.restaurants);
                renderMenuItems(data.menu_items);
                updateCount(data.counts);
            }
        })
        .catch(function(error) {
            showLoading(false);
            console.error('Search failed:', error);
        });
}


function renderRestaurants(restaurants) {
    const list = document.getElementById('restaurantList');
    list.innerHTML = '';

    if (restaurants.length === 0) {
        checkNoResults();
        return;
    }

    restaurants.forEach(function(r) {
        list.innerHTML += `
            <div class="restaurant-card">
                <div class="card-body">
                    <h3>${escapeHtml(r.name)}</h3>
                    <div class="card-meta">
                        <span>📍 ${escapeHtml(r.location)}</span>
                        <span>🏘 ${escapeHtml(r.area)}</span>
                    </div>
                    <p class="card-bg">${escapeHtml(r.short_background)}</p>
                    <a class="btn-view"
                       href="/ONLINE-FOOD-BLOG/view/browse/restaurant.php?id=${r.id}">
                        View Restaurant
                    </a>
                </div>
            </div>`;
    });
}

function renderMenuItems(menuItems) {
    const list  = document.getElementById('menuList');
    const title = document.getElementById('menuTitle');
    list.innerHTML = '';

    if (menuItems.length === 0) {
        title.style.display = 'none';
        checkNoResults();
        return;
    }

    title.style.display = 'block';

    menuItems.forEach(function(item) {
        list.innerHTML += `
            <div class="menu-card">
                <div class="menu-card-body">
                    <h4>${escapeHtml(item.name)}</h4>
                    <div class="menu-price">৳ ${parseFloat(item.price).toFixed(2)}</div>
                    <div class="menu-restaurant">
                        at ${escapeHtml(item.restaurant_name)}
                    </div>
                    <a class="btn-view"
                       href="/ONLINE-FOOD-BLOG/view/browse/menu_item.php?id=${item.id}">
                        View Item
                    </a>
                </div>
            </div>`;
    });
}

function updateCount(counts) {
    const countEl = document.getElementById('resultsCount');
    const total   = counts.restaurants + counts.menu_items;

    if (total === 0) {
        countEl.textContent = 'No results found';
    } else {
        countEl.textContent =
            `Found ${counts.restaurants} restaurant(s) and ` +
            `${counts.menu_items} food item(s)`;
    }
}

function checkNoResults() {
    const restaurants = document.getElementById('restaurantList').innerHTML.trim();
    const menuItems   = document.getElementById('menuList').innerHTML.trim();
    const noResults   = document.getElementById('noResults');

    if (restaurants === '' && menuItems === '') {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}


function clearSearch() {
    document.getElementById('searchInput').value   = '';
    document.getElementById('locationInput').value = '';
    document.getElementById('areaSelect').value    = '';

    // Reload the page to show all restaurants again
    window.location.reload();
}

function showLoading(show) {
    document.getElementById('loadingSpinner').style.display =
        show ? 'block' : 'none';
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#039;');
}
</script>

</body>
</html>