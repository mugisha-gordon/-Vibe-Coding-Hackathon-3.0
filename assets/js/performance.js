// Performance Optimizations
document.addEventListener('DOMContentLoaded', function() {
    // Loading Screen
    const loadingScreen = document.querySelector('.loading-screen');
    if (loadingScreen) {
        window.addEventListener('load', () => {
            loadingScreen.classList.add('fade-out');
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        });
    }

    // Lazy Loading Images
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.classList.add('loaded');
        });
    }

    // Defer Non-Critical JavaScript
    const deferScripts = () => {
        const scripts = document.querySelectorAll('script[data-defer]');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            newScript.src = script.src;
            newScript.async = true;
            script.parentNode.replaceChild(newScript, script);
        });
    };

    // Optimize Images
    const optimizeImages = () => {
        const images = document.querySelectorAll('img:not([loading="lazy"])');
        images.forEach(img => {
            if (!img.complete) {
                img.classList.add('lazy-load');
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                });
            }
        });
    };

    // Performance Monitoring
    const measurePerformance = () => {
        if ('performance' in window) {
            const timing = performance.timing;
            const loadTime = timing.loadEventEnd - timing.navigationStart;
            console.log(`Page Load Time: ${loadTime}ms`);

            // Log resource timing
            const resources = performance.getEntriesByType('resource');
            resources.forEach(resource => {
                console.log(`${resource.name}: ${resource.duration}ms`);
            });
        }
    };

    // Initialize Performance Optimizations
    deferScripts();
    optimizeImages();
    measurePerformance();

    // Smooth Scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for Animations
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.animate');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, {
            threshold: 0.1
        });

        elements.forEach(element => observer.observe(element));
    };

    // Initialize Animations
    animateOnScroll();

    // Preload Critical Resources
    const preloadResources = () => {
        const criticalResources = [
            '/assets/css/style.css',
            '/assets/js/main.js'
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = resource.endsWith('.css') ? 'style' : 'script';
            link.href = resource;
            document.head.appendChild(link);
        });
    };

    // Initialize Preloading
    preloadResources();

    // Optimize Event Listeners
    const optimizeEventListeners = () => {
        const debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // Optimize scroll events
        window.addEventListener('scroll', debounce(() => {
            // Handle scroll events
        }, 100));

        // Optimize resize events
        window.addEventListener('resize', debounce(() => {
            // Handle resize events
        }, 100));
    };

    // Initialize Event Listener Optimization
    optimizeEventListeners();
}); 