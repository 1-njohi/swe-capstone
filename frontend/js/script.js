const API_URL = 'https://1e2f-102-0-16-184.ngrok-free.app'; // Change to your PHP backend URL

let currentUser = null;
let token = null;

// DOM Elements
const loginSection = document.getElementById('loginSection');
const dashboardSection = document.getElementById('dashboardSection');
const loginBtn = document.getElementById('loginBtn');
const logoutBtn = document.getElementById('logoutBtn');
const userName = document.getElementById('userName');
const loginUsername = document.getElementById('loginUsername');
const loginPassword = document.getElementById('loginPassword');
const availableCourses = document.getElementById('availableCourses');
const myRegistrations = document.getElementById('myRegistrations');
const refreshCoursesBtn = document.getElementById('refreshCoursesBtn');
const refreshRegistrationsBtn = document.getElementById('refreshRegistrationsBtn');

// Helper functions
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function getHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };
}

async function apiRequest(endpoint, method = 'GET', data = null) {
    const options = {
        method,
        headers: getHeaders()
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    const response = await fetch(`${API_URL}/${endpoint}`, options);
    const result = await response.json();
    
    if (!response.ok) {
        throw new Error(result.error || 'Request failed');
    }
    
    return result;
}

// Login
async function login() {
    const username = loginUsername.value.trim();
    const password = loginPassword.value.trim();
    
    if (!username || !password) {
        showToast('Please enter username and password', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Login failed');
        }
        
        token = data.token;
        currentUser = data.user;
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(currentUser));
        
        showToast(`Welcome, ${currentUser.name}!`);
        showDashboard();
    } catch (error) {
        showToast(error.message, 'error');
    }
}

// Logout
function logout() {
    token = null;
    currentUser = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    showLogin();
    showToast('Logged out successfully');
}

// Show login
function showLogin() {
    loginSection.style.display = 'block';
    dashboardSection.style.display = 'none';
}

// Show dashboard
function showDashboard() {
    loginSection.style.display = 'none';
    dashboardSection.style.display = 'block';
    userName.textContent = `${currentUser.name} (${currentUser.role})`;
    loadCourses();
    loadRegistrations();
}

// Load available courses
async function loadCourses() {
    try {
        const courses = await apiRequest('courses');
        availableCourses.innerHTML = courses.map(course => `
            <div class="course-item">
                <div class="course-info">
                    <div>
                        <span class="course-code">${course.code}</span>
                        <span class="course-name">${course.name}</span>
                    </div>
                    <div class="course-details">
                        Credits: ${course.credits} | Capacity: ${course.enrolled}/${course.capacity} 
                        <span class="course-available">(${course.available_spots || 0} spots left)</span>
                    </div>
                </div>
                <div class="course-actions">
                    ${course.available_spots > 0 ? 
                        `<button class="btn btn-success btn-sm" onclick="registerForCourse(${course.id})">Register</button>` :
                        '<span style="color: #e74c3c; font-size: 12px;">Full</span>'
                    }
                </div>
            </div>
        `).join('');
    } catch (error) {
        availableCourses.innerHTML = `<p class="empty-message">Error loading courses: ${error.message}</p>`;
    }
}

// Load user's registrations
async function loadRegistrations() {
    try {
        const registrations = await apiRequest('registrations/my');
        
        if (registrations.length === 0) {
            myRegistrations.innerHTML = '<p class="empty-message">You are not registered for any courses yet.</p>';
            return;
        }
        
        myRegistrations.innerHTML = registrations.map(course => `
            <div class="course-item" style="border-left-color: #2ecc71;">
                <div class="course-info">
                    <div>
                        <span class="course-code">${course.code}</span>
                        <span class="course-name">${course.name}</span>
                    </div>
                    <div class="course-details">
                        Credits: ${course.credits} | Registered: ${new Date(course.registered_at).toLocaleDateString()}
                    </div>
                </div>
                <div class="course-actions">
                    <button class="btn btn-danger btn-sm" onclick="dropCourse(${course.id})">Drop</button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        myRegistrations.innerHTML = `<p class="empty-message">Error loading registrations: ${error.message}</p>`;
    }
}

// Register for a course
async function registerForCourse(courseId) {
    try {
        await apiRequest('registrations/register', 'POST', { course_id: courseId });
        showToast('Successfully registered for course!');
        loadCourses();
        loadRegistrations();
    } catch (error) {
        showToast(error.message, 'error');
    }
}

// Drop a course
async function dropCourse(courseId) {
    if (!confirm('Are you sure you want to drop this course?')) return;
    
    try {
        await apiRequest('registrations/drop', 'POST', { course_id: courseId });
        showToast('Course dropped successfully');
        loadCourses();
        loadRegistrations();
    } catch (error) {
        showToast(error.message, 'error');
    }
}

// Event Listeners
loginBtn.addEventListener('click', login);
logoutBtn.addEventListener('click', logout);
refreshCoursesBtn.addEventListener('click', loadCourses);
refreshRegistrationsBtn.addEventListener('click', loadRegistrations);

// Enter key support
loginUsername.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loginPassword.focus();
});
loginPassword.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') login();
});

// Check for existing session
function checkSession() {
    const savedToken = localStorage.getItem('token');
    const savedUser = localStorage.getItem('user');
    
    if (savedToken && savedUser) {
        token = savedToken;
        currentUser = JSON.parse(savedUser);
        showDashboard();
    } else {
        showLogin();
    }
}

// Initialize
checkSession();

// Make functions global for onclick handlers
window.registerForCourse = registerForCourse;
window.dropCourse = dropCourse;