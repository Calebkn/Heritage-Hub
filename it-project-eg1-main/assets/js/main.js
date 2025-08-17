// Main JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add loading states to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
});

// Product search functionality
function searchProducts() {
    const searchInput = document.getElementById('product-search');
    const categorySelect = document.getElementById('category-filter');
    
    if (searchInput && categorySelect) {
        const search = searchInput.value;
        const category = categorySelect.value;
        
        window.location.href = `index.php?page=products&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`;
    }
}

// Order tracking
function trackOrder(trackingNumber) {
    fetch(`api/tracking.php?tracking_number=${trackingNumber}`)
        .then(response => response.json())
        .then(data => {
            console.log('Tracking info:', data);
            // Update tracking display
        })
        .catch(error => {
            console.error('Error fetching tracking info:', error);
        });
}

// Real-time notifications (WebSocket simulation)
function initializeNotifications() {
    // In a real implementation, this would use WebSockets
    setInterval(() => {
        // Check for new orders/updates
        if (window.location.pathname.includes('dashboard') || window.location.pathname.includes('orders')) {
            // Refresh order data
            location.reload();
        }
    }, 30000); // Check every 30 seconds
}

// Initialize notifications if user is logged in
if (document.querySelector('.user-avatar')) {
    initializeNotifications();
}