document.addEventListener('DOMContentLoaded', () => { 
    const t = document.querySelector('.mobile-menu-toggle'); 
    const n = document.querySelector('.nav-links'); 
    if (t && n) { 
        t.addEventListener('click', (e) => {
            e.stopPropagation();
            n.classList.toggle('active');
        });
        
        document.addEventListener('click', (e) => {
            if (n.classList.contains('active') && !n.contains(e.target) && !t.contains(e.target)) {
                n.classList.remove('active');
            }
        });
    } 
    
    // Navbar scroll effect for home page
    const homeNav = document.querySelector('.home-navbar');
    if (homeNav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                homeNav.classList.add('scrolled');
            } else {
                homeNav.classList.remove('scrolled');
            }
        });
    }
    
    // Fetch Featured Products for homepage
    const featuredContainer = document.getElementById('featured-collage-container');
    if (featuredContainer) {
        fetch(`api/get_featured_products.php?_t=${Date.now()}`, { cache: 'no-store' })
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

    // --- Toast Notification Helper ---
    function showToast(message, isError = false) {
        let toast = document.getElementById('custom-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'custom-toast';
            toast.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 9999; padding: 16px 24px; border-radius: 12px; font-family: var(--font-sans, Arial, sans-serif); font-size: 15px; font-weight: 500; box-shadow: 0 10px 25px rgba(0,0,0,0.2); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); opacity: 0; transform: translateY(20px); max-width: 420px; color: #ffffff;';
            document.body.appendChild(toast);
        }
        toast.style.backgroundColor = isError ? '#ef4444' : '#0d9488';
        toast.innerHTML = (isError ? '⚠️ ' : '✓ ') + message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        if (window.toastTimeout) clearTimeout(window.toastTimeout);
        window.toastTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 5000);
    }

    // --- Form Submission Handler ---
    function setupInquiryForm(formId, feedbackId) {
        const form = document.getElementById(formId);
        const feedback = document.getElementById(feedbackId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.textContent : 'Submit Inquiry';

            if (submitBtn) {
                submitBtn.textContent = 'Sending Inquiry...';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.7';
            }

            if (feedback) {
                feedback.style.display = 'none';
                feedback.innerHTML = '';
            }

            const payload = {
                name: form.querySelector('[name="name"]')?.value || '',
                phone: form.querySelector('[name="phone"]')?.value || '',
                email: form.querySelector('[name="email"]')?.value || '',
                city: form.querySelector('[name="city"]')?.value || '',
                business_type: form.querySelector('[name="business_type"]')?.value || '',
                message: form.querySelector('[name="message"]')?.value || ''
            };

            fetch('api/send_mail.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.textContent = originalBtnText;
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }

                if (data.success) {
                    form.reset();
                    showToast('Inquiry sent successfully!', false);
                    if (feedback) {
                        feedback.style.display = 'block';
                        feedback.style.padding = '14px 18px';
                        feedback.style.backgroundColor = '#f0fdf4';
                        feedback.style.border = '1px solid #bbf7d0';
                        feedback.style.color = '#15803d';
                        feedback.style.borderRadius = '8px';
                        feedback.style.fontWeight = '500';
                        feedback.style.fontSize = '14px';
                        feedback.innerHTML = '✓ ' + data.message;
                    }
                } else {
                    showToast(data.message || 'Failed to send inquiry. Please try again.', true);
                    if (feedback) {
                        feedback.style.display = 'block';
                        feedback.style.padding = '14px 18px';
                        feedback.style.backgroundColor = '#fef2f2';
                        feedback.style.border = '1px solid #fecaca';
                        feedback.style.color = '#dc2626';
                        feedback.style.borderRadius = '8px';
                        feedback.style.fontWeight = '500';
                        feedback.style.fontSize = '14px';
                        feedback.innerHTML = '⚠️ ' + (data.message || 'Error submitting form.');
                    }
                }
            })
            .catch(error => {
                console.error('Error submitting form:', error);
                if (submitBtn) {
                    submitBtn.textContent = originalBtnText;
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
                showToast('Network error while sending inquiry. Please try again.', true);
                if (feedback) {
                    feedback.style.display = 'block';
                    feedback.style.padding = '14px 18px';
                    feedback.style.backgroundColor = '#fef2f2';
                    feedback.style.border = '1px solid #fecaca';
                    feedback.style.color = '#dc2626';
                    feedback.style.borderRadius = '8px';
                    feedback.style.fontWeight = '500';
                    feedback.style.fontSize = '14px';
                    feedback.innerHTML = '⚠️ Network error while sending inquiry.';
                }
            });
        });
    }

    // Initialize inquiry form handlers on Contact Us and Home pages
    setupInquiryForm('contactForm', 'formFeedback');
    setupInquiryForm('homeContactForm', 'homeFormFeedback');
});
