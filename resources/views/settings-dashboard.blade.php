<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Settings Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .setting-card {
            transition: all 0.2s;
        }
        .setting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">App Settings Dashboard</h1>
                    <div class="flex gap-3">
                        <button onclick="initializeDefaults()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Initialize Defaults
                        </button>
                        <button onclick="loadSettings()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auth Section -->
        <div id="auth-section" class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Admin Authentication</h2>
                <div class="flex gap-4">
                    <input type="email" id="email" value="admin@socialmediaapp.com"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Admin Email">
                    <input type="password" id="password" value="Admin@12345"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Password">
                    <button onclick="login()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Login
                    </button>
                </div>
                <div id="auth-status" class="mt-3 text-sm"></div>
            </div>
        </div>

        <!-- Categories Tabs -->
        <div class="max-w-7xl mx-auto px-4 py-2 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-3 flex gap-2 overflow-x-auto">
                <button onclick="filterGroup('all')" class="group-tab px-4 py-2 rounded-lg bg-blue-600 text-white transition" data-group="all">All</button>
                <button onclick="filterGroup('general')" class="group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-group="general">General</button>
                <button onclick="filterGroup('theme')" class="group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-group="theme">Theme</button>
                <button onclick="filterGroup('branding')" class="group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-group="branding">Branding</button>
                <button onclick="filterGroup('features')" class="group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-group="features">Features</button>
                <button onclick="filterGroup('integrations')" class="group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-group="integrations">Integrations</button>
                <button onclick="filterGroup('notifications')" class="group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-group="notifications">Notifications</button>
                <button onclick="filterGroup('security')" class="group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-group="security">Security</button>
            </div>
        </div>

        <!-- Settings Grid -->
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div id="settings-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Settings will be loaded here -->
            </div>
        </div>

        <!-- Save Button -->
        <div id="save-bar" class="hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <span id="changes-count" class="text-gray-700 font-medium">0 changes</span>
                <div class="flex gap-3">
                    <button onclick="cancelChanges()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                    <button onclick="saveChanges()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let token = '';
        let allSettings = [];
        let changes = {};
        let currentFilter = 'all';

        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('http://127.0.0.1:8000/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.token) {
                    token = data.token;
                    document.getElementById('auth-status').innerHTML =
                        '<span class="text-green-600">✓ Logged in as ' + data.user.email + '</span>';
                    await loadSettings();
                } else {
                    document.getElementById('auth-status').innerHTML =
                        '<span class="text-red-600">✗ Login failed</span>';
                }
            } catch (error) {
                document.getElementById('auth-status').innerHTML =
                    '<span class="text-red-600">✗ Error: ' + error.message + '</span>';
            }
        }

        async function loadSettings() {
            if (!token) {
                alert('Please login first');
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8000/api/admin/v2/settings/', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                const data = await response.json();

                if (data.success) {
                    allSettings = [];
                    Object.keys(data.data).forEach(group => {
                        data.data[group].forEach(setting => {
                            allSettings.push(setting);
                        });
                    });
                    renderSettings();
                }
            } catch (error) {
                alert('Error loading settings: ' + error.message);
            }
        }

        async function initializeDefaults() {
            if (!token) {
                alert('Please login first');
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8000/api/admin/v2/settings/initialize', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                const data = await response.json();
                alert(data.message);
                await loadSettings();
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function renderSettings() {
            const container = document.getElementById('settings-container');
            const filtered = currentFilter === 'all'
                ? allSettings
                : allSettings.filter(s => s.group === currentFilter);

            container.innerHTML = filtered.map(setting => {
                const value = changes[setting.key] !== undefined ? changes[setting.key] : setting.value;
                const inputHtml = getInputHtml(setting, value);

                return `
                    <div class="setting-card bg-white rounded-lg shadow-md p-6" data-group="${setting.group}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">${formatKey(setting.key)}</h3>
                                <p class="text-sm text-gray-500 mt-1">${setting.description || ''}</p>
                                <span class="inline-block mt-2 px-2 py-1 text-xs rounded ${getGroupColor(setting.group)} text-white">
                                    ${setting.group}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            ${inputHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function getInputHtml(setting, value) {
            if (setting.type === 'boolean') {
                const checked = value === 'true' || value === true ? 'checked' : '';
                return `
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" ${checked}
                                   onchange="updateSetting('${setting.key}', this.checked ? 'true' : 'false')"
                                   class="sr-only">
                            <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner"></div>
                            <div class="dot absolute w-4 h-4 bg-white rounded-full shadow left-1 top-1 transition"></div>
                        </div>
                        <div class="ml-3 text-gray-700 font-medium">
                            ${value === 'true' || value === true ? 'Enabled' : 'Disabled'}
                        </div>
                    </label>
                `;
            } else if (setting.type === 'integer' || setting.type === 'float') {
                return `
                    <input type="number" value="${value}"
                           onchange="updateSetting('${setting.key}', this.value)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                `;
            } else {
                return `
                    <input type="text" value="${value}"
                           onchange="updateSetting('${setting.key}', this.value)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                `;
            }
        }

        function updateSetting(key, value) {
            changes[key] = value;
            updateSaveBar();
        }

        function updateSaveBar() {
            const count = Object.keys(changes).length;
            document.getElementById('changes-count').textContent = count + ' change' + (count !== 1 ? 's' : '');
            document.getElementById('save-bar').classList.toggle('hidden', count === 0);
        }

        function cancelChanges() {
            changes = {};
            updateSaveBar();
            renderSettings();
        }

        async function saveChanges() {
            if (!token) {
                alert('Please login first');
                return;
            }

            const settings = Object.keys(changes).map(key => ({
                key: key,
                value: changes[key]
            }));

            try {
                const response = await fetch('http://127.0.0.1:8000/api/admin/v2/settings/bulk', {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ settings })
                });

                const data = await response.json();
                alert(data.message);
                changes = {};
                updateSaveBar();
                await loadSettings();
            } catch (error) {
                alert('Error saving changes: ' + error.message);
            }
        }

        function filterGroup(group) {
            currentFilter = group;
            document.querySelectorAll('.group-tab').forEach(btn => {
                if (btn.dataset.group === group) {
                    btn.className = 'group-tab px-4 py-2 rounded-lg bg-blue-600 text-white transition';
                } else {
                    btn.className = 'group-tab px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition';
                }
            });
            renderSettings();
        }

        function formatKey(key) {
            return key.split('_').map(word =>
                word.charAt(0).toUpperCase() + word.slice(1)
            ).join(' ');
        }

        function getGroupColor(group) {
            const colors = {
                general: 'bg-blue-600',
                theme: 'bg-purple-600',
                branding: 'bg-pink-600',
                features: 'bg-amber-600',
                integrations: 'bg-green-600',
                notifications: 'bg-cyan-600',
                security: 'bg-red-600'
            };
            return colors[group] || 'bg-gray-600';
        }

        // Auto-login on page load (for testing)
        window.onload = () => {
            login();
        };
    </script>
</body>
</html>
