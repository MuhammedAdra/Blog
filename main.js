// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll effect
    const nav = document.querySelector('nav');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    // Add smooth scrolling to all links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add animation on scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-on-scroll');

        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (elementPosition < windowHeight - 50) {
                element.classList.add('animated');
            }
        });
    };

    // Run animation check on load and scroll
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Run once on page load

    // Categories Slider Navigation
    const categoriesSlider = document.querySelector('.categories__slider');
    const prevBtn = document.querySelector('.categories__nav-btn.prev');
    const nextBtn = document.querySelector('.categories__nav-btn.next');

    if (categoriesSlider && prevBtn && nextBtn) {
        // Scroll to previous categories
        prevBtn.addEventListener('click', function() {
            categoriesSlider.scrollBy({
                left: -600,
                behavior: 'smooth'
            });
        });

        // Scroll to next categories
        nextBtn.addEventListener('click', function() {
            categoriesSlider.scrollBy({
                left: 600,
                behavior: 'smooth'
            });
        });
    }

    // Check for auth notifications
    if (typeof sessionStorage !== 'undefined') {
        const authNotification = sessionStorage.getItem('auth_notification');
        if (authNotification) {
            try {
                const notification = JSON.parse(authNotification);
                // Check if notifications.js is loaded
                if (typeof createNotification === 'function') {
                    createNotification(notification.message, notification.type);
                } else {
                    // Fallback if notifications.js is not loaded
                    alert(notification.message);
                }
                sessionStorage.removeItem('auth_notification');
            } catch (e) {
                console.error('Error parsing auth notification:', e);
            }
        }
    }
    // Mobile Navigation Toggle
    const openNavBtn = document.querySelector('#open__nav-btn');
    const closeNavBtn = document.querySelector('#close__nav-btn');
    const navItems = document.querySelector('.nav__items');

    if (openNavBtn && closeNavBtn && navItems) {
        // Open nav menu
        openNavBtn.addEventListener('click', () => {
            navItems.style.display = 'flex';
            openNavBtn.style.display = 'none';
            closeNavBtn.style.display = 'inline-block';
        });

        // Close nav menu
        closeNavBtn.addEventListener('click', () => {
            navItems.style.display = 'none';
            openNavBtn.style.display = 'inline-block';
            closeNavBtn.style.display = 'none';
        });
    }

    // Add animation to posts
    const posts = document.querySelectorAll('.post');
    posts.forEach((post, index) => {
        post.style.animationDelay = `${index * 0.1}s`;
    });

    // Add animation to category buttons
    const categoryButtons = document.querySelectorAll('.category__buttons .category__button');
    categoryButtons.forEach((button, index) => {
        button.style.animationDelay = `${index * 0.1}s`;
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    // Create error message if it doesn't exist
                    let errorMessage = field.parentElement.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('small');
                        errorMessage.className = 'error-message';
                        errorMessage.style.color = 'var(--color-red)';
                        field.parentElement.appendChild(errorMessage);
                    }
                    errorMessage.textContent = `${field.placeholder || 'This field'} is required`;

                    // Add error styling
                    field.style.borderColor = 'var(--color-red)';
                } else {
                    // Remove error styling if field is valid
                    const errorMessage = field.parentElement.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                    field.style.borderColor = 'var(--color-gray-300)';
                }
            });

            // Email validation for email fields
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value.trim() && !validateEmail(field.value)) {
                    isValid = false;
                    // Create error message if it doesn't exist
                    let errorMessage = field.parentElement.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('small');
                        errorMessage.className = 'error-message';
                        errorMessage.style.color = 'var(--color-red)';
                        field.parentElement.appendChild(errorMessage);
                    }
                    errorMessage.textContent = 'Please enter a valid email address';

                    // Add error styling
                    field.style.borderColor = 'var(--color-red)';
                }
            });

            // Password match validation for signup forms
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="confirmPassword"]');
            if (password && confirmPassword) {
                if (password.value !== confirmPassword.value) {
                    isValid = false;
                    // Create error message if it doesn't exist
                    let errorMessage = confirmPassword.parentElement.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('small');
                        errorMessage.className = 'error-message';
                        errorMessage.style.color = 'var(--color-red)';
                        confirmPassword.parentElement.appendChild(errorMessage);
                    }
                    errorMessage.textContent = 'Passwords do not match';

                    // Add error styling
                    confirmPassword.style.borderColor = 'var(--color-red)';
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Function to validate email format
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    // Image preview for file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewContainer = this.parentElement.querySelector('.image-preview');
            if (previewContainer) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                        previewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });

    // Dashboard sidebar toggle for mobile
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const dashboardAside = document.querySelector('.dashboard aside');
    if (sidebarToggle && dashboardAside) {
        sidebarToggle.addEventListener('click', () => {
            dashboardAside.classList.toggle('show');
        });
    }

    // Automatically hide alert messages after 5 seconds with fade effect
    const alertMessages = document.querySelectorAll('.alert__message');
    alertMessages.forEach(alert => {
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.className = 'alert-close-btn';
        closeBtn.style.cssText = 'position: absolute; right: 10px; top: 10px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: inherit; opacity: 0.7;';
        closeBtn.addEventListener('click', () => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        });
        alert.style.position = 'relative';
        alert.appendChild(closeBtn);

        // Auto hide after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });

    // Add active class to current page in navigation
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav__items a');
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentLocation ||
            currentLocation.includes(link.getAttribute('href')) && link.getAttribute('href') !== '/') {
            link.classList.add('active');
        }
    });

    // Add active class to current page in dashboard sidebar
    const sidebarLinks = document.querySelectorAll('.dashboard aside ul li a');
    sidebarLinks.forEach(link => {
        if (link.getAttribute('href') === currentLocation ||
            currentLocation.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });

    // Back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<i class="uil uil-arrow-up"></i>';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; background: var(--color-primary); color: white; border: none; cursor: pointer; display: none; z-index: 99; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: all 0.3s ease;';
    document.body.appendChild(backToTopBtn);

    // Show/hide back to top button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.style.display = 'block';
            setTimeout(() => {
                backToTopBtn.style.opacity = '1';
            }, 10);
        } else {
            backToTopBtn.style.opacity = '0';
            setTimeout(() => {
                backToTopBtn.style.display = 'none';
            }, 300);
        }
    });

    // Scroll to top when button is clicked
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Add hover effect to all buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = 'var(--shadow-md)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'var(--shadow-sm)';
        });
    });

    // Add lazy loading for images
    const lazyImages = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }
});