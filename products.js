document.addEventListener('DOMContentLoaded', () => {
  const grid = document.querySelector('.product-grid-3');
  const filterChips = document.querySelectorAll('.filter-chip');
  let products = []; // Will hold data from API

  // Fetch products from backend
  fetch('api/get_products.php')
    .then(response => response.json())
    .then(data => {
      products = data;
      // Initial render after fetching data
      renderProducts('All');
      updateHeader('All');
    })
    .catch(error => {
      console.error('Error fetching products:', error);
      grid.innerHTML = '<p class="body-md text-charcoal">Failed to load products. Please try again later.</p>';
    });

  function renderProducts(filterCategory) {
    grid.innerHTML = ''; // clear grid
    
    if (products.length === 0) {
       grid.innerHTML = '<p class="body-md text-charcoal">No products found.</p>';
       return;
    }

    products.forEach(p => {
      if (filterCategory === 'All' || p.category === filterCategory) {
        // create card
        const card = document.createElement('div');
        card.className = 'product-card';
        card.dataset.category = p.category;
        
        card.innerHTML = `
          <img src="${p.image}" alt="${p.title}" class="product-img">
          <div class="product-info">
            <h3 class="product-title">${p.title}</h3>
          </div>
        `;
        grid.appendChild(card);
      }
    });
  }

  const categoryDescriptions = {
    "All": "Discover our complete collection of premium fabrics, meticulously manufactured for exceptional quality, durability, and style.",
    "Cotton Fabrics": "Experience the breathability and comfort of our premium cotton fabrics. Ideal for high-quality bedsheets and everyday apparel.",
    "Polyester Fabrics": "Durable, wrinkle-resistant polyester fabrics designed for longevity and ease of care.",
    "Poly Spandex Fabrics": "Flexible and resilient poly spandex blends, perfect for activewear and comfortable stretch garments.",
    "Rayon Fabrics": "Soft, breathable, and beautifully draped rayon fabrics available in stunning prints and solids.",
    "Viscose Fabrics": "Luxurious viscose fabrics offering a silk-like feel, perfect for premium fashion and home textiles.",
    "Mesh Fabrics": "Lightweight, breathable mesh fabrics suitable for athletic wear and decorative layering.",
    "Knit Fabrics": "Comfortable and versatile knit fabrics providing excellent stretch and recovery.",
    "Velvet Fabrics": "Plush, opulent velvet fabrics that bring a touch of luxury to any project.",
    "Embroidered Fabrics": "Exquisite embroidered fabrics featuring detailed craftsmanship and intricate designs.",
    "Fancy / Fashion Fabrics": "Unique, trend-setting fashion fabrics designed to make a statement in any collection."
  };

  const categoryHeader = document.getElementById('category-header');
  const categoryTitle = document.getElementById('category-title');
  const categoryDesc = document.getElementById('category-desc');

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

  // Add event listeners
  filterChips.forEach(chip => {
    chip.addEventListener('click', () => {
      // Update active class
      filterChips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      
      // Filter products
      const selectedCategory = chip.getAttribute('data-filter');
      
      updateHeader(selectedCategory);
      renderProducts(selectedCategory);
    });
  });
});
