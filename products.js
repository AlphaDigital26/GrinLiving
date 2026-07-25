document.addEventListener('DOMContentLoaded', () => {
  const grid = document.querySelector('.product-grid-3');
  const filterChipsContainer = document.querySelector('.filter-chips');
  const categoryHeader = document.getElementById('category-header');
  const categoryTitle = document.getElementById('category-title');
  const categoryDesc = document.getElementById('category-desc');
  const paginationContainer = document.getElementById('pagination');
  
  let products = []; // Will hold data from API
  let currentCategory = 'All';
  let currentPage = 1;
  const limit = 12;

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
    const url = `api/get_products.php?category=${encodeURIComponent(currentCategory)}&page=${currentPage}&limit=${limit}`;
    
    fetch(url)
      .then(response => response.json())
      .then(data => {
        if (Array.isArray(data)) {
           // Fallback in case backend is not updated yet (returns plain array)
           products = data;
           renderProducts();
           updateHeader(currentCategory);
           if (paginationContainer) paginationContainer.style.display = 'none';
        } else {
           // Expected paginated response
           products = data.products;
           renderProducts();
           updateHeader(currentCategory);
           renderPagination(data.total_pages);
        }
      })
      .catch(error => {
        console.error('Error fetching products:', error);
        grid.innerHTML = '<p class="body-md text-charcoal">Failed to load products. Please try again later.</p>';
        if (paginationContainer) paginationContainer.style.display = 'none';
      });
  }

  function renderProducts() {
    grid.innerHTML = ''; // clear grid
    
    if (!products || products.length === 0) {
       grid.innerHTML = '<p class="body-md text-charcoal">No products found.</p>';
       return;
    }

    products.forEach(p => {
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
    });
  }

  function renderPagination(totalPages) {
    if (!paginationContainer) return;
    
    paginationContainer.innerHTML = '';
    
    if (totalPages <= 1) {
      paginationContainer.style.display = 'none';
      return;
    }
    
    paginationContainer.style.display = 'flex';

    // Previous Button
    const prevBtn = document.createElement('button');
    prevBtn.className = 'pagination-btn';
    prevBtn.textContent = 'Prev';
    prevBtn.disabled = currentPage === 1;
    prevBtn.addEventListener('click', () => {
      if (currentPage > 1) {
        window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
        currentPage--;
        fetchProducts();
      }
    });
    paginationContainer.appendChild(prevBtn);

    // Page Numbers
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement('button');
      pageBtn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
      pageBtn.textContent = i;
      if (i !== currentPage) {
        pageBtn.addEventListener('click', () => {
          window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
          currentPage = i;
          fetchProducts();
        });
      }
      paginationContainer.appendChild(pageBtn);
    }

    // Next Button
    const nextBtn = document.createElement('button');
    nextBtn.className = 'pagination-btn';
    nextBtn.textContent = 'Next';
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.addEventListener('click', () => {
      if (currentPage < totalPages) {
        window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
        currentPage++;
        fetchProducts();
      }
    });
    paginationContainer.appendChild(nextBtn);
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
        
        currentCategory = chip.getAttribute('data-filter');
        currentPage = 1; // Reset to page 1 on filter change
        
        fetchProducts();
      });
    });
  }
});
