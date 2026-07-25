document.addEventListener('DOMContentLoaded', () => { 
    const t = document.querySelector('.mobile-menu-toggle'); 
    const n = document.querySelector('.nav-links'); 
    if (t && n) { 
        t.addEventListener('click', () => n.classList.toggle('active')); 
    } 
    
    // Fetch Featured Products for homepage
    const featuredContainer = document.getElementById('featured-collage-container');
    if (featuredContainer) {
        fetch('api/get_featured_products.php')
            .then(response => response.json())
            .then(products => {
                featuredContainer.innerHTML = ''; // clear loading text
                
                if (!products || products.length === 0) {
                    featuredContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center;"><p class="body-md text-light">No featured products selected.</p></div>';
                    return;
                }
                
                products.forEach((p, index) => {
                    const item = document.createElement('div');
                    // Add 'collage-tall' to the first two items
                    if (index === 0 || index === 1) {
                        item.className = 'collage-item collage-tall';
                    } else {
                        item.className = 'collage-item';
                    }
                    
                    item.innerHTML = `
                        <img src="${p.image}" alt="${p.title}" onerror="this.src='https://via.placeholder.com/300'">
                        <div class="collage-item-text">${p.title}</div>
                    `;
                    featuredContainer.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Error fetching featured products:', error);
                featuredContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center;"><p class="body-md text-danger">Failed to load featured products.</p></div>';
            });
    }
});
