document.addEventListener('DOMContentLoaded', () => {
    fetchBlogs();
});

function fetchBlogs(page = 1) {
    fetch(`api/get_blogs.php?page=${page}&_t=${Date.now()}`, { cache: 'no-store' })
        .then(response => response.json())
        .then(data => {
            renderBlogs(data.blogs);
            renderPagination(data.totalPages, data.currentPage);
        })
        .catch(error => console.error('Error fetching blogs:', error));
}

function renderBlogs(blogs) {
    const grid = document.querySelector('.testimonials-grid');
    grid.innerHTML = ''; // Clear placeholder

    if (blogs.length === 0) {
        grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">No blogs available at the moment. Please check back later.</p>';
        return;
    }

    blogs.forEach(blog => {
        const card = document.createElement('div');
        card.className = 'product-card card';
        
        // Truncate content to ~100 characters, strip HTML tags for excerpt
        let excerpt = document.createElement('div');
        excerpt.innerHTML = blog.content;
        let textContent = excerpt.textContent || excerpt.innerText || "";
        if (textContent.length > 120) {
            textContent = textContent.substring(0, 120) + '...';
        }

        // Format Date
        const dateObj = new Date(blog.created_at);
        const formattedDate = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

        // Generate SEO Friendly Slug
        const slug = blog.title.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text

        card.innerHTML = `
            <img src="${blog.image}" alt="${blog.title}" class="product-img" style="width: 100%; height: 260px; object-fit: cover; object-position: center; display: block;">
            <div class="product-info">
                <p class="text-heritage-gold mb-8" style="font-size: 0.95rem; font-weight: 600; letter-spacing: 0.8px; text-transform: uppercase; color: #8A6538;">${formattedDate} <span style="margin: 0 8px; color: #CBD5E1;">•</span> ${blog.author}</p>
                <h3 class="headline-sm">${blog.title}</h3>
                <p class="body-sm text-charcoal mt-8">
                    ${textContent}
                </p>
                <div class="mt-24">
                    <a href="blog-${slug}-${blog.id}" class="label-lg text-deep-teal">Read More &rarr;</a>
                </div>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

function renderPagination(totalPages, currentPage) {
    // If you add a pagination container later, this is where it goes
    // E.g., document.getElementById('blog-pagination').innerHTML = ...
}
