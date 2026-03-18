<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GradeFlow · student manager</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'DM Sans', sans-serif; margin: 0; }
    .font-display { font-family: 'Playfair Display', serif; }
    .tab-active { border-bottom: 3px solid; }
    .fade-in { animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
    .toast-show { animation: toastIn 0.3s ease, toastOut 0.3s ease 2.5s forwards; }
    @keyframes toastIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes toastOut { from { opacity: 1; } to { opacity: 0; } }
    .progress-bar { transition: width 0.6s ease; }
    .modal-overlay { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
    input:focus, select:focus, textarea:focus { outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.2); }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: #c7c7c7; border-radius: 3px; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    /* Print styles for coupon */
    @media print {
      body * { visibility: hidden; }
      #coupon-print-area, #coupon-print-area * { visibility: visible; }
      #coupon-print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 8.5in;
        height: 5.5in;
        padding: 0.25in;
        background: white;
      }
    }
  </style>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            ink: '#1a1a2e',
            slate_bg: '#f0f1f5',
            card_white: '#ffffff',
            primary: '#4f46e5',
            accent: '#10b981'
          }
        }
      }
    }
  </script>
</head>
<body class="h-full">
<div id="app" class="h-full w-full flex flex-col" style="background-color: #f0f1f5;">
  <!-- Toast container -->
  <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

  <!-- Header -->
  <header id="app-header" class="w-full z-40 flex-shrink-0" style="background-color: #1a1a2e;">
    <div class="px-4 sm:px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #4f46e5;">
          <i data-lucide="graduation-cap" style="width:22px;height:22px;color:#fff;"></i>
        </div>
        <div>
          <h1 id="header-title" class="font-display text-xl font-bold text-white">Student Grade Manager</h1>
          <p id="header-welcome" class="text-xs text-gray-400">Track quizzes, tasks & performance</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <span id="record-count" class="text-xs text-gray-500 hidden sm:inline"></span>
      </div>
    </div>
  </header>

  <!-- Main Layout with Sidebar -->
  <div class="flex flex-1 overflow-hidden">
    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="w-48 flex-shrink-0 overflow-y-auto" style="background-color: #ffffff; border-right: 1px solid #e5e7eb;">
      <nav class="p-4 space-y-2" id="nav-tabs">
        <button onclick="switchView('dashboard')" data-tab="dashboard" class="tab-btn w-full text-left px-4 py-3 text-sm font-medium rounded-lg transition flex items-center gap-3" style="color: #1a1a2e; border-left: 3px solid transparent; border-color: #4f46e5;">
          <i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i> <span>Dashboard</span>
        </button>
        <button onclick="switchView('grades')" data-tab="grades" class="tab-btn w-full text-left px-4 py-3 text-sm font-medium rounded-lg transition flex items-center gap-3 text-gray-600" style="border-left: 3px solid transparent;">
          <i data-lucide="table" style="width:18px;height:18px;"></i> <span>All Grades</span>
        </button>
        <button onclick="switchView('students')" data-tab="students" class="tab-btn w-full text-left px-4 py-3 text-sm font-medium rounded-lg transition flex items-center gap-3 text-gray-600" style="border-left: 3px solid transparent;">
          <i data-lucide="users" style="width:18px;height:18px;"></i> <span>Students</span>
        </button>
        <button onclick="switchView('analytics')" data-tab="analytics" class="tab-btn w-full text-left px-4 py-3 text-sm font-medium rounded-lg transition flex items-center gap-3 text-gray-600" style="border-left: 3px solid transparent;">
          <i data-lucide="bar-chart-3" style="width:18px;height:18px;"></i> <span>Analytics</span>
        </button>
        <div class="pt-4 mt-4 border-t border-gray-200 space-y-2">
          <button onclick="openAddModal()" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition text-white" style="background-color: #4f46e5;">
            <i data-lucide="plus" style="width:18px;height:18px;"></i> <span>Add Grade</span>
          </button>
          <button onclick="showPrintOptions()" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition text-white" style="background-color: #10b981;">
            <i data-lucide="printer" style="width:18px;height:18px;"></i> <span>Print Coupon</span>
          </button>
          <button onclick="logout()" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition text-gray-600 hover:bg-gray-100 border border-gray-200">
            <i data-lucide="log-out" style="width:18px;height:18px;"></i> 
            <span>Logout</span>
          </button>
        </div>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto px-4 sm:px-6 py-6">
      <!-- Dashboard View -->
      <section id="view-dashboard" class="fade-in">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="rounded-xl p-4 card-hover" style="background-color: #ffffff;">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background-color: #eef2ff;"><i data-lucide="users" style="width:18px;height:18px;color:#4f46e5;"></i></div>
            </div>
            <p id="stat-students" class="text-2xl font-bold" style="color: #1a1a2e;">0</p>
            <p class="text-xs text-gray-500">Students</p>
          </div>
          <div class="rounded-xl p-4 card-hover" style="background-color: #ffffff;">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background-color: #ecfdf5;"><i data-lucide="file-text" style="width:18px;height:18px;color:#10b981;"></i></div>
            </div>
            <p id="stat-records" class="text-2xl font-bold" style="color: #1a1a2e;">0</p>
            <p class="text-xs text-gray-500">Total Records</p>
          </div>
          <div class="rounded-xl p-4 card-hover" style="background-color: #ffffff;">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background-color: #fef3c7;"><i data-lucide="bar-chart-3" style="width:18px;height:18px;color:#f59e0b;"></i></div>
            </div>
            <p id="stat-avg" class="text-2xl font-bold" style="color: #1a1a2e;">—</p>
            <p class="text-xs text-gray-500">Avg Score %</p>
          </div>
          <div class="rounded-xl p-4 card-hover" style="background-color: #ffffff;">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background-color: #fce7f3;"><i data-lucide="award" style="width:18px;height:18px;color:#ec4899;"></i></div>
            </div>
            <p id="stat-highest" class="text-2xl font-bold" style="color: #1a1a2e;">—</p>
            <p class="text-xs text-gray-500">Highest %</p>
          </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 rounded-xl p-5" style="background-color: #ffffff;">
            <h2 class="font-display text-lg font-bold mb-4" style="color: #1a1a2e;">Recent Grades</h2>
            <div id="recent-grades" class="space-y-3"></div>
          </div>
          <div class="rounded-xl p-5" style="background-color: #ffffff;">
            <h2 class="font-display text-lg font-bold mb-4" style="color: #1a1a2e;">By Category</h2>
            <div id="category-breakdown" class="space-y-4"></div>
          </div>
        </div>
      </section>

      <!-- All Grades View -->
      <section id="view-grades" class="fade-in hidden">
        <div class="rounded-xl p-5" style="background-color: #ffffff;">
          <div class="flex flex-col sm:flex-row gap-3 mb-5">
            <div class="relative flex-1">
              <i data-lucide="search" style="width:16px;height:16px;" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input type="text" id="search-grades" placeholder="Search by student, subject, or activity..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm" oninput="filterGrades()">
            </div>
            <select id="filter-category" onchange="filterGrades()" class="px-3 py-2.5 rounded-lg border border-gray-200 text-sm">
              <option value="">All Categories</option>
              <option value="Quiz">Quiz</option>
              <option value="Performance Task">Performance Task</option>
              <option value="Exam">Exam</option>
              <option value="Assignment">Assignment</option>
              <option value="Project">Project</option>
            </select>
            <select id="filter-quarter" onchange="filterGrades()" class="px-3 py-2.5 rounded-lg border border-gray-200 text-sm">
              <option value="">All Quarters</option>
              <option value="Q1">Q1</option>
              <option value="Q2">Q2</option>
              <option value="Q3">Q3</option>
              <option value="Q4">Q4</option>
            </select>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="text-left text-gray-500 border-b border-gray-100">
                  <th class="pb-3 font-medium">Student</th>
                  <th class="pb-3 font-medium">Subject</th>
                  <th class="pb-3 font-medium hidden sm:table-cell">Category</th>
                  <th class="pb-3 font-medium">Activity</th>
                  <th class="pb-3 font-medium">Score</th>
                  <th class="pb-3 font-medium hidden sm:table-cell">Quarter</th>
                  <th class="pb-3 font-medium hidden md:table-cell">Date</th>
                  <th class="pb-3 font-medium">Actions</th>
                </tr>
              </thead>
              <tbody id="grades-tbody" class="divide-y divide-gray-50"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Students View -->
      <section id="view-students" class="fade-in hidden">
        <div id="students-list" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
      </section>

      <!-- Analytics View -->
      <section id="view-analytics" class="fade-in hidden">
        <div class="grid lg:grid-cols-2 gap-6">
          <div class="rounded-xl p-5" style="background-color: #ffffff;">
            <h2 class="font-display text-lg font-bold mb-4" style="color: #1a1a2e;">Subject Performance</h2>
            <div id="subject-chart" class="space-y-3"></div>
          </div>
          <div class="rounded-xl p-5" style="background-color: #ffffff;">
            <h2 class="font-display text-lg font-bold mb-4" style="color: #1a1a2e;">Quarter Trends</h2>
            <div id="quarter-chart" class="space-y-3"></div>
          </div>
          <div class="rounded-xl p-5" style="background-color: #ffffff;">
            <h2 class="font-display text-lg font-bold mb-4" style="color: #1a1a2e;">Top Students</h2>
            <div id="top-students" class="space-y-3"></div>
          </div>
          <div class="rounded-xl p-5" style="background-color: #ffffff;">
            <h2 class="font-display text-lg font-bold mb-4" style="color: #1a1a2e;">Grade Distribution</h2>
            <div id="grade-distribution" class="space-y-3"></div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Add/Edit Modal -->
  <div id="modal-overlay" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-2xl p-6 fade-in max-h-[90%] overflow-auto" style="background-color: #ffffff;">
      <div class="flex items-center justify-between mb-5">
        <h2 id="modal-title" class="font-display text-xl font-bold" style="color: #1a1a2e;">Add Grade</h2>
        <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
          <i data-lucide="x" style="width:18px;height:18px;color:#6b7280;"></i>
        </button>
      </div>
      <form id="grade-form" onsubmit="handleFormSubmit(event)" class="space-y-4">
        <input type="hidden" id="edit-id" value="">
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2 sm:col-span-1">
            <label for="inp-student" class="block text-sm font-medium text-gray-700 mb-1">Student Name *</label>
            <input type="text" id="inp-student" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm" placeholder="e.g., Juan Dela Cruz">
          </div>
          <div class="col-span-2 sm:col-span-1">
            <label for="inp-subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
            <input type="text" id="inp-subject" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm" placeholder="e.g., Mathematics">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="inp-category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
            <select id="inp-category" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm">
              <option value="">Select...</option>
              <option value="Quiz">Quiz</option>
              <option value="Performance Task">Performance Task</option>
              <option value="Exam">Exam</option>
              <option value="Assignment">Assignment</option>
              <option value="Project">Project</option>
            </select>
          </div>
          <div>
            <label for="inp-quarter" class="block text-sm font-medium text-gray-700 mb-1">Quarter *</label>
            <select id="inp-quarter" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm">
              <option value="">Select...</option>
              <option value="Q1">Q1</option>
              <option value="Q2">Q2</option>
              <option value="Q3">Q3</option>
              <option value="Q4">Q4</option>
            </select>
          </div>
        </div>
        <div>
          <label for="inp-activity" class="block text-sm font-medium text-gray-700 mb-1">Activity Name *</label>
          <input type="text" id="inp-activity" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm" placeholder="e.g., Quiz #1 - Algebra">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="inp-score" class="block text-sm font-medium text-gray-700 mb-1">Score *</label>
            <input type="number" id="inp-score" required min="0" step="0.5" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm" placeholder="e.g., 85">
          </div>
          <div>
            <label for="inp-total" class="block text-sm font-medium text-gray-700 mb-1">Total Points *</label>
            <input type="number" id="inp-total" required min="1" step="0.5" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm" placeholder="e.g., 100">
          </div>
        </div>
        <div>
          <label for="inp-date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
          <input type="date" id="inp-date" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm">
        </div>
        <div id="form-error" class="text-red-500 text-sm hidden"></div>
        <div class="flex gap-3 pt-2">
          <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Cancel</button>
          <button type="submit" id="btn-submit" class="flex-1 px-4 py-2.5 rounded-lg text-white text-sm font-medium transition hover:brightness-110" style="background-color: #4f46e5;">
            <span id="btn-submit-text">Save Grade</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="delete-modal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4">
    <div class="w-full max-w-sm rounded-2xl p-6 fade-in" style="background-color: #ffffff;">
      <div class="text-center mb-5">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
          <i data-lucide="trash-2" style="width:22px;height:22px;color:#ef4444;"></i>
        </div>
        <h3 class="font-display text-lg font-bold" style="color: #1a1a2e;">Delete Grade?</h3>
        <p id="delete-msg" class="text-sm text-gray-500 mt-1">This action cannot be undone.</p>
      </div>
      <div class="flex gap-3">
        <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Cancel</button>
        <button onclick="confirmDelete()" id="btn-delete-confirm" class="flex-1 px-4 py-2.5 rounded-lg bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition">Delete</button>
      </div>
    </div>
  </div>

  <!-- Print Coupon Modal -->
  <div id="coupon-print-modal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4">
    <div class="w-full max-w-5xl rounded-2xl p-6 fade-in" style="background-color: #ffffff;">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-display text-xl font-bold" style="color: #1a1a2e;">Print Coupon (8.5" x 5.5")</h2>
        <button onclick="closeCouponModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
          <i data-lucide="x" style="width:18px;height:18px;"></i>
        </button>
      </div>
      
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Student for Coupon</label>
        <select id="coupon-student-select" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm">
          <option value="">-- Choose student --</option>
        </select>
      </div>
      
      <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4 overflow-auto">
        <div id="coupon-preview-container" class="flex justify-center">
          <div class="coupon-paper p-6 bg-white shadow-lg rounded-lg" style="width:8.5in; height:5.5in; font-family: 'DM Sans', sans-serif;">
            <div id="coupon-dynamic-content">
              <div class="text-center text-gray-400 py-16">Select a student to preview coupon</div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="flex gap-3 justify-end">
        <button onclick="closeCouponModal()" class="px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Cancel</button>
        <button onclick="printCoupon()" class="px-4 py-2.5 rounded-lg text-white text-sm font-medium transition hover:brightness-110 flex items-center gap-2" style="background-color: #10b981;">
          <i data-lucide="printer" style="width:16px;"></i> <span>Print Coupon</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Hidden coupon print area -->
  <div id="coupon-print-area" style="display: none;"></div>
</div>

<script>
  // ========== PERSISTENT STORAGE USING localStorage ==========
  let allRecords = [];
  let currentView = 'dashboard';
  let deleteTarget = null;
  let nextId = 1;

  // Load data from localStorage on startup
  function loadFromStorage() {
    const stored = localStorage.getItem('gradeManagerRecords');
    if (stored) {
      try {
        allRecords = JSON.parse(stored);
        // Find the highest ID to set nextId
        if (allRecords.length > 0) {
          const maxId = Math.max(...allRecords.map(r => {
            const idNum = parseInt(r.__backendId.replace('rec_', ''));
            return isNaN(idNum) ? 0 : idNum;
          }));
          nextId = maxId + 1;
        }
      } catch (e) {
        console.error('Failed to load from storage', e);
        allRecords = [];
      }
    } else {
      // Seed with demo data if no existing data
      allRecords = [
       
      ];
      nextId = 7;
      saveToStorage();
    }
  }

  // Save data to localStorage
  function saveToStorage() {
    localStorage.setItem('gradeManagerRecords', JSON.stringify(allRecords));
  }

  // Initialize data
  loadFromStorage();

  // ========== SDK SIMULATION (simplified) ==========
  window.elementSdk = {
    _config: {
      app_title: 'Student Grade Manager',
      welcome_message: 'Track quizzes, tasks & performance',
      background_color: '#f0f1f5',
      surface_color: '#ffffff',
      text_color: '#1a1a2e',
      primary_color: '#4f46e5',
      accent_color: '#10b981',
      font_family: 'DM Sans',
      font_size: 14
    },
    init: function(opts) {
      if (opts && opts.onConfigChange) opts.onConfigChange(this._config);
    },
    setConfig: function(updates) { Object.assign(this._config, updates); },
    get config() { return this._config; }
  };

  window.dataSdk = {
    create: async function(record) {
      const newRec = { ...record, type: 'grade', __backendId: 'rec_' + (nextId++) };
      allRecords.push(newRec);
      saveToStorage();
      if (this._handler) this._handler.onDataChanged([...allRecords]);
      return { isOk: true, data: newRec };
    },
    update: async function(record) {
      const idx = allRecords.findIndex(r => r.__backendId === record.__backendId);
      if (idx >= 0) {
        allRecords[idx] = { ...allRecords[idx], ...record };
        saveToStorage();
        if (this._handler) this._handler.onDataChanged([...allRecords]);
        return { isOk: true };
      }
      return { isOk: false };
    },
    delete: async function(record) {
      const idx = allRecords.findIndex(r => r.__backendId === record.__backendId);
      if (idx >= 0) {
        allRecords.splice(idx, 1);
        saveToStorage();
        if (this._handler) this._handler.onDataChanged([...allRecords]);
        return { isOk: true };
      }
      return { isOk: false };
    },
    _handler: null,
    init: async function(handler) {
      this._handler = handler;
      if (handler) handler.onDataChanged([...allRecords]);
      return { isOk: true };
    }
  };

  // Initialize SDK
  (async () => {
    await window.dataSdk.init({
      onDataChanged: (data) => {
        allRecords = data;
        renderAll();
        document.getElementById('record-count').textContent = `${data.length}/999`;
      }
    });
  })();

  // ========== HELPER FUNCTIONS ==========
  function showToast(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    const colors = type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-red-500' : 'bg-amber-500';
    toast.className = `toast-show ${colors} text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium mb-2`;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }

  function getPercentage(score, total) { return total > 0 ? Math.round((score / total) * 100) : 0; }
  
  function getGradeLetter(pct) {
    if (pct >= 97) return 'A+'; if (pct >= 93) return 'A'; if (pct >= 90) return 'A-'; if (pct >= 87) return 'B+';
    if (pct >= 83) return 'B'; if (pct >= 80) return 'B-'; if (pct >= 77) return 'C+'; if (pct >= 73) return 'C';
    if (pct >= 70) return 'C-'; if (pct >= 67) return 'D+'; if (pct >= 63) return 'D'; if (pct >= 60) return 'D-'; return 'F';
  }
  
  function getGradeColor(pct) {
    if (pct >= 90) return '#10b981'; if (pct >= 80) return '#3b82f6'; if (pct >= 70) return '#f59e0b'; if (pct >= 60) return '#f97316'; return '#ef4444';
  }
  
  function getCategoryIcon(cat) {
    const map = { 'Quiz': 'help-circle', 'Performance Task': 'clipboard-list', 'Exam': 'file-text', 'Assignment': 'edit', 'Project': 'layers' };
    return map[cat] || 'file';
  }

  // ========== VIEW FUNCTIONS ==========
  function switchView(view) {
    currentView = view;
    document.querySelectorAll('[id^="view-"]').forEach(el => el.classList.add('hidden'));
    document.getElementById(`view-${view}`).classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(btn => {
      const isActive = btn.dataset.tab === view;
      btn.style.borderColor = isActive ? '#4f46e5' : 'transparent';
      btn.style.backgroundColor = isActive ? '#eef2ff' : 'transparent';
      btn.style.color = isActive ? '#4f46e5' : '#6b7280';
    });
  }

  function renderAll() { 
    renderDashboard(); 
    renderGradesTable(); 
    renderStudents(); 
    renderAnalytics(); 
    updateCouponStudentList();
    lucide.createIcons(); 
  }

  function filterGrades() { renderGradesTable(); lucide.createIcons(); }

  function renderDashboard() {
    const students = [...new Set(allRecords.map(r => r.student_name))];
    const avgPct = allRecords.length > 0 ? Math.round(allRecords.reduce((s, r) => s + getPercentage(r.score, r.total), 0) / allRecords.length) : null;
    const highPct = allRecords.length > 0 ? Math.max(...allRecords.map(r => getPercentage(r.score, r.total))) : null;
    
    document.getElementById('stat-students').textContent = students.length;
    document.getElementById('stat-records').textContent = allRecords.length;
    document.getElementById('stat-avg').textContent = avgPct !== null ? `${avgPct}%` : '—';
    document.getElementById('stat-highest').textContent = highPct !== null ? `${highPct}%` : '—';

    const recent = [...allRecords].sort((a, b) => (b.date || '').localeCompare(a.date || '')).slice(0, 5);
    const recentEl = document.getElementById('recent-grades');
    if (recent.length === 0) {
      recentEl.innerHTML = '<p class="text-gray-400 text-sm text-center py-8">No grades yet. Click "Add Grade" to get started!</p>';
    } else {
      recentEl.innerHTML = recent.map(r => {
        const pct = getPercentage(r.score, r.total);
        return `<div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color:${getGradeColor(pct)}20;">
              <i data-lucide="${getCategoryIcon(r.category)}" style="width:15px;height:15px;color:${getGradeColor(pct)};"></i>
            </div>
            <div>
              <p class="text-sm font-medium" style="color:#1a1a2e;">${r.student_name}</p>
              <p class="text-xs text-gray-500">${r.activity_name} · ${r.subject}</p>
            </div>
          </div>
          <div class="text-right">
            <span class="text-sm font-bold" style="color:${getGradeColor(pct)};">${pct}%</span>
            <p class="text-xs text-gray-400">${r.score}/${r.total}</p>
          </div>
        </div>`;
      }).join('');
    }

    const cats = {};
    allRecords.forEach(r => { 
      if (!cats[r.category]) cats[r.category] = { total: 0, count: 0 }; 
      cats[r.category].total += getPercentage(r.score, r.total); 
      cats[r.category].count++; 
    });
    
    const catEl = document.getElementById('category-breakdown');
    const catKeys = Object.keys(cats);
    if (catKeys.length === 0) {
      catEl.innerHTML = '<p class="text-gray-400 text-sm text-center py-8">No data yet</p>';
    } else {
      catEl.innerHTML = catKeys.map(k => {
        const avg = Math.round(cats[k].total / cats[k].count);
        return `<div>
          <div class="flex justify-between text-sm mb-1">
            <span class="font-medium">${k}</span>
            <span style="color:${getGradeColor(avg)};" class="font-bold">${avg}%</span>
          </div>
          <div class="w-full h-2 rounded-full bg-gray-100">
            <div class="h-2 rounded-full progress-bar" style="width:${avg}%;background-color:${getGradeColor(avg)};"></div>
          </div>
          <p class="text-xs text-gray-400 mt-1">${cats[k].count} record${cats[k].count > 1 ? 's' : ''}</p>
        </div>`;
      }).join('');
    }
  }

  function renderGradesTable() {
    const tbody = document.getElementById('grades-tbody');
    let filtered = getFilteredRecords();
    
    if (filtered.length === 0) { 
      tbody.innerHTML = '<tr><td colspan="8" class="py-12 text-center text-gray-400">No grades match your filters</td></tr>'; 
      return; 
    }
    
    filtered.sort((a, b) => (b.date || '').localeCompare(a.date || ''));
    tbody.innerHTML = filtered.map(r => {
      const pct = getPercentage(r.score, r.total);
      return `<tr class="hover:bg-gray-50 transition">
        <td class="py-3 pr-3"><span class="font-medium">${r.student_name}</span></td>
        <td class="py-3 pr-3 text-gray-600">${r.subject}</td>
        <td class="py-3 pr-3 hidden sm:table-cell">
          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium" style="background-color:${getGradeColor(pct)}15;color:${getGradeColor(pct)};">${r.category}</span>
        </td>
        <td class="py-3 pr-3 text-gray-600">${r.activity_name}</td>
        <td class="py-3 pr-3">
          <span class="font-bold" style="color:${getGradeColor(pct)};">${r.score}/${r.total}</span> 
          <span class="text-xs text-gray-400">(${pct}%)</span>
        </td>
        <td class="py-3 pr-3 text-gray-500 hidden sm:table-cell">${r.quarter}</td>
        <td class="py-3 pr-3 text-gray-500 hidden md:table-cell">${r.date || '—'}</td>
        <td class="py-3">
          <div class="flex gap-1">
            <button onclick="editGrade('${r.__backendId}')" class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-100 transition">
              <i data-lucide="edit" style="width:14px;height:14px;color:#6b7280;"></i>
            </button>
            <button onclick="openDeleteModal('${r.__backendId}')" class="w-7 h-7 flex items-center justify-center rounded hover:bg-red-50 transition">
              <i data-lucide="trash-2" style="width:14px;height:14px;color:#ef4444;"></i>
            </button>
          </div>
        </td>
      </tr>`;
    }).join('');
  }

  function getFilteredRecords() {
    const search = (document.getElementById('search-grades').value || '').toLowerCase();
    const cat = document.getElementById('filter-category').value;
    const qtr = document.getElementById('filter-quarter').value;
    
    return allRecords.filter(r => {
      if (cat && r.category !== cat) return false;
      if (qtr && r.quarter !== qtr) return false;
      if (search) {
        const haystack = `${r.student_name} ${r.subject} ${r.activity_name}`.toLowerCase();
        if (!haystack.includes(search)) return false;
      }
      return true;
    });
  }

  function renderStudents() {
    const studentMap = {}; 
    allRecords.forEach(r => { 
      if (!studentMap[r.student_name]) studentMap[r.student_name] = []; 
      studentMap[r.student_name].push(r); 
    });
    
    const container = document.getElementById('students-list'); 
    const names = Object.keys(studentMap).sort();
    
    if (names.length === 0) { 
      container.innerHTML = '<div class="col-span-full text-center text-gray-400 py-12 rounded-xl" style="background-color:#ffffff;">No students found</div>'; 
      return; 
    }
    
    container.innerHTML = names.map(name => {
      const records = studentMap[name]; 
      const avg = Math.round(records.reduce((s, r) => s + getPercentage(r.score, r.total), 0) / records.length);
      const subjects = [...new Set(records.map(r => r.subject))];
      
      return `<div class="rounded-xl p-5 card-hover" style="background-color:#ffffff;">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color:${getGradeColor(avg)};">${name.charAt(0).toUpperCase()}</div>
          <div>
            <h3 class="font-medium text-sm">${name}</h3>
            <p class="text-xs text-gray-500">${records.length} grade${records.length>1?'s':''} · ${subjects.length} subject${subjects.length>1?'s':''}</p>
          </div>
        </div>
        <div class="flex items-center justify-between">
          <div>
            <span class="text-2xl font-bold" style="color:${getGradeColor(avg)};">${avg}%</span>
            <span class="text-xs text-gray-400 ml-1">${getGradeLetter(avg)}</span>
          </div>
          <div class="w-24 h-2 rounded-full bg-gray-100">
            <div class="h-2 rounded-full progress-bar" style="width:${avg}%;background-color:${getGradeColor(avg)};"></div>
          </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-1">
          ${subjects.map(s => `<span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">${s}</span>`).join('')}
        </div>
      </div>`;
    }).join('');
  }

  function renderAnalytics() {
    // Subject Performance
    const subjMap = {}; 
    allRecords.forEach(r => { 
      if (!subjMap[r.subject]) subjMap[r.subject] = { total: 0, count: 0 }; 
      subjMap[r.subject].total += getPercentage(r.score, r.total); 
      subjMap[r.subject].count++; 
    });
    
    const subjEl = document.getElementById('subject-chart'); 
    const subjKeys = Object.keys(subjMap);
    
    if (!subjKeys.length) {
      subjEl.innerHTML = '<p class="text-gray-400 text-sm text-center py-8">No data available</p>';
    } else {
      subjEl.innerHTML = subjKeys.map(k => { 
        const avg = Math.round(subjMap[k].total / subjMap[k].count); 
        return `<div class="flex items-center gap-3">
          <span class="text-sm w-24 truncate text-gray-600">${k}</span>
          <div class="flex-1 h-6 rounded bg-gray-100 relative overflow-hidden">
            <div class="h-6 rounded progress-bar flex items-center justify-end pr-2" style="width:${avg}%;background-color:${getGradeColor(avg)};">
              <span class="text-xs text-white font-bold">${avg}%</span>
            </div>
          </div>
        </div>`; 
      }).join('');
    }

    // Top Students
    const studAvg = {};
    allRecords.forEach(r => {
      if (!studAvg[r.student_name]) studAvg[r.student_name] = { total: 0, count: 0 };
      studAvg[r.student_name].total += getPercentage(r.score, r.total);
      studAvg[r.student_name].count++;
    });
    
    const topEl = document.getElementById('top-students');
    const sorted = Object.entries(studAvg).map(([name, d]) => ({ name, avg: Math.round(d.total / d.count) })).sort((a, b) => b.avg - a.avg).slice(0, 5);
    
    if (sorted.length === 0) {
      topEl.innerHTML = '<p class="text-gray-400 text-sm text-center py-8">No data available</p>';
    } else {
      topEl.innerHTML = sorted.map((s, i) => `<div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold ${i === 0 ? 'bg-yellow-100 text-yellow-700' : i === 1 ? 'bg-gray-100 text-gray-600' : i === 2 ? 'bg-amber-50 text-amber-700' : 'bg-gray-50 text-gray-500'}">${i + 1}</span>
        <span class="flex-1 text-sm font-medium" style="color:#1a1a2e;">${s.name}</span>
        <span class="text-sm font-bold" style="color:${getGradeColor(s.avg)};">${s.avg}%</span>
      </div>`).join('');
    }
  }

  // ========== MODAL FUNCTIONS ==========
  function openAddModal() { 
    document.getElementById('edit-id').value = ''; 
    document.getElementById('grade-form').reset(); 
    document.getElementById('inp-date').value = new Date().toISOString().split('T')[0]; 
    document.getElementById('modal-title').textContent = 'Add Grade'; 
    document.getElementById('btn-submit-text').textContent = 'Save Grade'; 
    document.getElementById('form-error').classList.add('hidden'); 
    document.getElementById('modal-overlay').classList.remove('hidden'); 
  }

  function editGrade(backendId) {
    const record = allRecords.find(r => r.__backendId === backendId); 
    if (!record) return;
    
    document.getElementById('edit-id').value = backendId; 
    document.getElementById('inp-student').value = record.student_name; 
    document.getElementById('inp-subject').value = record.subject; 
    document.getElementById('inp-category').value = record.category; 
    document.getElementById('inp-quarter').value = record.quarter; 
    document.getElementById('inp-activity').value = record.activity_name; 
    document.getElementById('inp-score').value = record.score; 
    document.getElementById('inp-total').value = record.total; 
    document.getElementById('inp-date').value = record.date; 
    document.getElementById('modal-title').textContent = 'Edit Grade'; 
    document.getElementById('btn-submit-text').textContent = 'Update Grade'; 
    document.getElementById('form-error').classList.add('hidden'); 
    document.getElementById('modal-overlay').classList.remove('hidden'); 
    lucide.createIcons();
  }

  function closeModal() { document.getElementById('modal-overlay').classList.add('hidden'); }

  async function handleFormSubmit(e) {
    e.preventDefault(); 
    const errEl = document.getElementById('form-error'); 
    errEl.classList.add('hidden'); 
    const btn = document.getElementById('btn-submit'); 
    const btnText = document.getElementById('btn-submit-text');
    
    const data = { 
      student_name: document.getElementById('inp-student').value.trim(), 
      subject: document.getElementById('inp-subject').value.trim(), 
      category: document.getElementById('inp-category').value, 
      activity_name: document.getElementById('inp-activity').value.trim(), 
      score: parseFloat(document.getElementById('inp-score').value), 
      total: parseFloat(document.getElementById('inp-total').value), 
      date: document.getElementById('inp-date').value, 
      quarter: document.getElementById('inp-quarter').value,
      type: 'grade'
    };
    
    if (data.score > data.total) { 
      errEl.textContent = 'Score cannot exceed total points.'; 
      errEl.classList.remove('hidden'); 
      return; 
    }
    
    btn.disabled = true; 
    btnText.textContent = 'Saving...';
    
    const editId = document.getElementById('edit-id').value; 
    let result;
    
    if (editId) { 
      const existing = allRecords.find(r => r.__backendId === editId); 
      if (existing) result = await window.dataSdk.update({ ...existing, ...data }); 
    } else { 
      result = await window.dataSdk.create(data); 
    }
    
    btn.disabled = false; 
    btnText.textContent = editId ? 'Update Grade' : 'Save Grade';
    
    if (result && result.isOk) { 
      showToast(editId ? 'Grade updated!' : 'Grade saved!'); 
      closeModal(); 
    } else { 
      errEl.textContent = 'Something went wrong. Please try again.'; 
      errEl.classList.remove('hidden'); 
    }
  }

  function openDeleteModal(backendId) { 
    deleteTarget = allRecords.find(r => r.__backendId === backendId); 
    if (!deleteTarget) return; 
    document.getElementById('delete-msg').textContent = `Delete "${deleteTarget.activity_name}" for ${deleteTarget.student_name}?`; 
    document.getElementById('delete-modal').classList.remove('hidden'); 
    lucide.createIcons(); 
  }

  function closeDeleteModal() { 
    deleteTarget = null; 
    document.getElementById('delete-modal').classList.add('hidden'); 
  }

  async function confirmDelete() {
    if (!deleteTarget) return; 
    const btn = document.getElementById('btn-delete-confirm'); 
    btn.disabled = true; 
    btn.textContent = 'Deleting...';
    
    const result = await window.dataSdk.delete(deleteTarget); 
    
    btn.disabled = false; 
    btn.textContent = 'Delete';
    
    if (result.isOk) { 
      showToast('Grade deleted'); 
      closeDeleteModal(); 
    } else { 
      showToast('Failed to delete. Please try again.', 'error'); 
    }
  }

  // ========== COUPON PRINT FEATURE ==========
  function updateCouponStudentList() {
    const select = document.getElementById('coupon-student-select');
    if (!select) return;
    const students = [...new Set(allRecords.map(r => r.student_name))].sort();
    select.innerHTML = '<option value="">-- Choose student --</option>' + 
      students.map(s => `<option value="${s}">${s}</option>`).join('');
  }

  function showPrintOptions() {
    if (allRecords.length === 0) {
      showToast('No data to print', 'warning');
      return;
    }
    updateCouponStudentList();
    document.getElementById('coupon-print-modal').classList.remove('hidden');
    lucide.createIcons();
  }

  function closeCouponModal() {
    document.getElementById('coupon-print-modal').classList.add('hidden');
  }

  // Live preview when student selected
  document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('coupon-student-select');
    if (select) {
      select.addEventListener('change', function(e) {
        const student = e.target.value;
        if (!student) {
          document.getElementById('coupon-dynamic-content').innerHTML = '<div class="text-center text-gray-400 py-16">Select a student to preview coupon</div>';
          return;
        }
        generateCouponPreview(student);
      });
    }
  });

  function generateCouponPreview(studentName) {
    const records = allRecords.filter(r => r.student_name === studentName);
    if (records.length === 0) {
      document.getElementById('coupon-dynamic-content').innerHTML = '<div class="text-center text-gray-400">No records found</div>';
      return;
    }
    
    const avg = Math.round(records.reduce((s, r) => s + getPercentage(r.score, r.total), 0) / records.length);
    const gradeLetter = getGradeLetter(avg);
    const gradeColor = getGradeColor(avg);
    const subjects = [...new Set(records.map(r => r.subject))].join(', ');
    
    const html = `
      <div style="height:100%; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
          <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px dashed #e5e7eb; padding-bottom:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="background-color:#4f46e5; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M22 10v6M2 10v6M2 16h20M4 4h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="M12 8v4"/><path d="M8 12h8"/></svg>
              </div>
              <div>
                <h2 style="font-size:18px; font-weight:bold; margin:0;">Student Grade Coupon</h2>
                <p style="font-size:11px; color:#6b7280; margin:0;">Official record · ${new Date().toLocaleDateString()}</p>
              </div>
            </div>
            <div style="background-color:${gradeColor}20; padding:6px 12px; border-radius:20px;">
              <span style="color:${gradeColor}; font-weight:bold; font-size:16px;">${avg}%</span>
            </div>
          </div>
          
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
            <div>
              <p style="font-size:11px; color:#6b7280; margin:0 0 4px 0;">STUDENT</p>
              <p style="font-size:16px; font-weight:600; margin:0;">${studentName}</p>
            </div>
            <div>
              <p style="font-size:11px; color:#6b7280; margin:0 0 4px 0;">SUBJECTS</p>
              <p style="font-size:14px; font-weight:500; margin:0;">${subjects}</p>
            </div>
            <div>
              <p style="font-size:11px; color:#6b7280; margin:0 0 4px 0;">LETTER GRADE</p>
              <p style="font-size:24px; font-weight:700; margin:0; color:${gradeColor};">${gradeLetter}</p>
            </div>
          </div>
          
          <div style="margin-top:20px;">
            <p style="font-size:11px; color:#6b7280; margin:0 0 8px 0;">RECENT PERFORMANCE</p>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
              ${records.slice(0,3).map(r => {
                const pct = getPercentage(r.score, r.total);
                return `<div style="background:#f3f4f6; border-radius:8px; padding:8px; flex:1; min-width:80px;">
                  <p style="font-size:10px; color:#6b7280; margin:0;">${r.subject}</p>
                  <p style="font-size:14px; font-weight:600; margin:2px 0 0 0; color:${getGradeColor(pct)};">${pct}%</p>
                  <p style="font-size:9px; color:#9ca3af; margin:0;">${r.activity_name}</p>
                </div>`;
              }).join('')}
            </div>
          </div>
        </div>
        
        <div style="border-top:1px solid #e5e7eb; padding-top:12px; display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
          <div>
            <p style="font-size:9px; color:#9ca3af; margin:0;">Valid until ${new Date(Date.now() + 30*86400000).toLocaleDateString()}</p>
          </div>
          <div style="display:flex; gap:15px;">
            <span style="font-size:9px; color:#9ca3af;">Principal's approval</span>
            <span style="font-size:9px; color:#9ca3af;">Registrar</span>
          </div>
        </div>
      </div>
    `;
    
    document.getElementById('coupon-dynamic-content').innerHTML = html;
    lucide.createIcons();
  }

  function printCoupon() {
    const student = document.getElementById('coupon-student-select').value;
    if (!student) {
      showToast('Please select a student', 'warning');
      return;
    }
    
    const records = allRecords.filter(r => r.student_name === student);
    if (records.length === 0) {
      showToast('No records for this student', 'warning');
      return;
    }
    
    const avg = Math.round(records.reduce((s, r) => s + getPercentage(r.score, r.total), 0) / records.length);
    const gradeLetter = getGradeLetter(avg);
    const gradeColor = getGradeColor(avg);
    const subjects = [...new Set(records.map(r => r.subject))].join(', ');
    
    const html = `
      <div id="coupon-print-area" style="width:8.5in; height:5.5in; padding:0.25in; background:white; margin:0; font-family:'DM Sans',sans-serif;">
        <div style="height:100%; display:flex; flex-direction:column; justify-content:space-between;">
          <div>
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px dashed #e5e7eb; padding-bottom:10px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <div style="background-color:#4f46e5; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M22 10v6M2 10v6M2 16h20M4 4h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="M12 8v4"/><path d="M8 12h8"/></svg>
                </div>
                <div>
                  <h2 style="font-size:18px; font-weight:bold; margin:0;">Student Grade Coupon</h2>
                  <p style="font-size:11px; color:#6b7280; margin:0;">Official record · ${new Date().toLocaleDateString()}</p>
                </div>
              </div>
              <div style="background-color:${gradeColor}20; padding:6px 12px; border-radius:20px;">
                <span style="color:${gradeColor}; font-weight:bold; font-size:16px;">${avg}%</span>
              </div>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
              <div>
                <p style="font-size:11px; color:#6b7280; margin:0 0 4px 0;">STUDENT</p>
                <p style="font-size:16px; font-weight:600; margin:0;">${student}</p>
              </div>
              <div>
                <p style="font-size:11px; color:#6b7280; margin:0 0 4px 0;">SUBJECTS</p>
                <p style="font-size:14px; font-weight:500; margin:0;">${subjects}</p>
              </div>
              <div>
                <p style="font-size:11px; color:#6b7280; margin:0 0 4px 0;">LETTER GRADE</p>
                <p style="font-size:24px; font-weight:700; margin:0; color:${gradeColor};">${gradeLetter}</p>
              </div>
            </div>
            
            <div style="margin-top:20px;">
              <p style="font-size:11px; color:#6b7280; margin:0 0 8px 0;">RECENT PERFORMANCE</p>
              <div style="display:flex; gap:8px; flex-wrap:wrap;">
                ${records.slice(0,3).map(r => {
                  const pct = getPercentage(r.score, r.total);
                  return `<div style="background:#f3f4f6; border-radius:8px; padding:8px; flex:1; min-width:80px;">
                    <p style="font-size:10px; color:#6b7280; margin:0;">${r.subject}</p>
                    <p style="font-size:14px; font-weight:600; margin:2px 0 0 0; color:${getGradeColor(pct)};">${pct}%</p>
                    <p style="font-size:9px; color:#9ca3af; margin:0;">${r.activity_name}</p>
                  </div>`;
                }).join('')}
              </div>
            </div>
          </div>
          
          <div style="border-top:1px solid #e5e7eb; padding-top:12px; display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
            <div>
              <p style="font-size:9px; color:#9ca3af; margin:0;">Valid until ${new Date(Date.now() + 30*86400000).toLocaleDateString()}</p>
            </div>
            <div style="display:flex; gap:15px;">
              <span style="font-size:9px; color:#9ca3af;">Principal's approval</span>
              <span style="font-size:9px; color:#9ca3af;">Registrar</span>
            </div>
          </div>
        </div>
      </div>
    `;
    
    const printArea = document.getElementById('coupon-print-area');
    printArea.style.display = 'block';
    printArea.innerHTML = html;
    
    window.print();
    
    setTimeout(() => {
      printArea.style.display = 'none';
      printArea.innerHTML = '';
    }, 1000);
    
    closeCouponModal();
    showToast(`Printing coupon for ${student}`, 'success');
  }

  // ========== LOGOUT FUNCTION ==========
  function logout() {
    // Redirect to index.php (login page)
    window.location.href = 'index.php';
  }

  // Initialize
  switchView('dashboard');
  lucide.createIcons();
</script>
</body>
</html>