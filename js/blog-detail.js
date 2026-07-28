document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    let blogId = urlParams.get('id');

    // Fallback to SEO Friendly URL extraction (e.g. blog-title-slug-1)
    if (!blogId) {
        const match = window.location.pathname.match(/-(\d+)$/);
        if (match) {
            blogId = match[1];
        }
    }

    const contentArea = document.getElementById('blog-content-area');

    if (!blogId) {
        contentArea.innerHTML = `
            <div class="text-center" style="padding: 100px 0;">
                <h1 class="display-sm text-charcoal">Blog Not Found</h1>
                <p class="body-md mt-16 mb-32">The article you are looking for does not exist or the link is invalid.</p>
                <a href="blog" class="btn btn-primary">Back to Blogs</a>
            </div>
        `;
        return;
    }

    fetch(`api/get_blog_detail.php?id=${blogId}&_t=${Date.now()}`, { cache: 'no-store' })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.blog) {
                const blog = data.blog;
                
                // Format Date
                const dateObj = new Date(blog.created_at);
                const formattedDate = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

                document.title = `${blog.title} - Grin Living`;

                contentArea.innerHTML = `
                    <a href="blog" class="back-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back to Blogs
                    </a>
                    
                    <div class="blog-header-wrapper" style="text-align: center; margin: 40px 0 48px 0; max-width: 1000px; margin-left: auto; margin-right: auto;">
                        <span class="label-md text-heritage-gold" style="display: inline-block; letter-spacing: 1.5px; text-transform: uppercase;">${formattedDate} <span style="margin: 0 12px; color: #CBD5E1;">•</span> ${blog.author}</span>
                        <h1 class="display-md text-charcoal mt-24 mb-0" style="font-family: 'Playfair Display', serif; line-height: 1.2; font-size: 3.5rem;">${blog.title}</h1>
                    </div>
                    
                    <div style="margin: 0 -10%; text-align: center; margin-bottom: 56px;">
                        <img src="${blog.image}" alt="${blog.title}" class="blog-detail-image" onerror="this.style.display='none'" style="width: 100%; height: auto; max-height: 600px; object-fit: cover; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); margin-bottom: 0;">
                    </div>
                    
                    <div class="blog-detail-content" style="max-width: 900px; margin: 0 auto;">
                        ${blog.content}
                    </div>
                `;
            } else {
                contentArea.innerHTML = `
                    <div class="text-center" style="padding: 100px 0;">
                        <h1 class="display-sm text-charcoal">Article Not Found</h1>
                        <p class="body-md mt-16 mb-32">${data.message || "The article you requested could not be loaded."}</p>
                        <a href="blog" class="btn btn-primary">Browse All Blogs</a>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching blog detail:', error);
            contentArea.innerHTML = `
                <div class="text-center" style="padding: 100px 0;">
                    <h1 class="display-sm text-charcoal">Connection Error</h1>
                    <p class="body-md mt-16 mb-32">Unable to connect to the server. Please try again later.</p>
                    <a href="blog" class="btn btn-primary">Back to Blogs</a>
                </div>
            `;
        });
});
