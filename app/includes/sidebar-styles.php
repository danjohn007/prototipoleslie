<style>
/* Menu Search Styles */
.menu-search {
    padding: 15px 20px;
    border-bottom: 1px solid var(--medium-gray);
    background-color: #f8f9fa;
}

.search-container {
    position: relative;
}

.search-container i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--dark-gray);
    font-size: 14px;
}

.menu-search-input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    border: 1px solid var(--medium-gray);
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.menu-search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
}

/* Submenu indentation */
.nav-link.submenu {
    padding-left: 40px;
    font-size: 14px;
}

/* Sidebar overlay for mobile */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 999;
    display: none;
}

.sidebar-overlay.active {
    display: block;
}

/* Hamburger button for mobile */
.hamburger-btn {
    display: none;
    background-color: var(--primary);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1001;
    font-size: 18px;
}

@media (max-width: 991px) {
    .hamburger-btn {
        display: block;
    }
    
    .sidebar {
        transform: translateX(-280px);
        transition: transform 0.3s ease-in-out;
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
}

/* User info styles */
.user-info {
    padding: 10px 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    margin: 10px 20px;
}

.user-name {
    font-weight: 600;
    color: var(--primary);
    font-size: 14px;
}

.user-role {
    font-size: 12px;
    color: var(--dark-gray);
    text-transform: capitalize;
    margin-top: 2px;
}
</style>
