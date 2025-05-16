        // Mobile Navigation Toggle
        const openNavBtn = document.querySelector('#open__nav-btn');
        const closeNavBtn = document.querySelector('#close__nav-btn');
        const navItems = document.querySelector('.nav__items');

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