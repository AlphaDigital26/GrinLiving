document.addEventListener('DOMContentLoaded', () => {
  const grid = document.querySelector('.product-grid-3');
  const filterChipsContainer = document.querySelector('.filter-chips');
  const categoryHeader = document.getElementById('category-header');
  const categoryTitle = document.getElementById('category-title');
  const categoryDesc = document.getElementById('category-desc');
  
  let products = []; // Will hold data from API
  let categoryDescriptions = {
    "All": "Discover our complete collection of premium fabrics, meticulously manufactured for exceptional quality, durability, and style."
  };

  // Fetch categories
  fetch('api/get_categories.php')
    .then(response => response.json())
    .then(categories => {
      categories.forEach(cat => {
        // Add to dictionary
        categoryDescriptions[cat.name] = cat.description;
        
        // Create chip
        const chip = document.createElement('span');
        chip.className = 'filter-chip';
        chip.setAttribute('data-filter', cat.name);
        chip.textContent = cat.name;
        filterChipsContainer.appendChild(chip);
      });
      
      // Bind events to newly created chips
      bindChipEvents();

      // Now fetch products
      fetchProducts();
    })
    .catch(error => {
      console.error('Error fetching categories:', error);
      fetchProducts(); // Try to fetch products anyway
    });

  function fetchProducts() {
    fetch('api/get_products.php')
      .then(response => response.json())
      .then(data => {
        products = data;
        renderProducts('All');
        updateHeader('All');
      })
      .catch(error => {
        console.error('Error fetching products:', error);
        grid.innerHTML = '<p class="body-md text-charcoal">Failed to load products. Please try again later.</p>';
      });
  }

  function renderProducts(filterCategory) {
    grid.innerHTML = ''; // clear grid
    
    if (products.length === 0) {
       grid.innerHTML = '<p class="body-md text-charcoal">No products found.</p>';
       return;
    }

    products.forEach(p => {
      if (filterCategory === 'All' || p.category === filterCategory) {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.dataset.category = p.category;
        
        card.innerHTML = `
          <img src="${p.image}" alt="${p.title}" class="product-img" onerror="this.src='https://via.placeholder.com/300'">
          <div class="product-info">
            <h3 class="product-title">${p.title}</h3>
          </div>
        `;
        grid.appendChild(card);
      }
    });
  }

  function updateHeader(selectedCategory) {
    if (categoryHeader && categoryTitle && categoryDesc) {
      if (selectedCategory === 'All') {
        categoryTitle.textContent = "All Fabrics";
      } else {
        categoryTitle.textContent = selectedCategory;
      }
      categoryDesc.textContent = categoryDescriptions[selectedCategory] || "Explore our premium selection of " + selectedCategory.toLowerCase() + ".";
      categoryHeader.style.display = 'block';
    }
  }

  function bindChipEvents() {
    const chips = document.querySelectorAll('.filter-chip');
    chips.forEach(chip => {
      chip.addEventListener('click', () => {
        chips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        
        const selectedCategory = chip.getAttribute('data-filter');
        updateHeader(selectedCategory);
        renderProducts(selectedCategory);
      });
    });
  }
});
