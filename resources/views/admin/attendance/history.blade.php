<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance History - Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.3s ease-out forwards; }
    #dropdownMenu.show {
        transform: scaleY(1);
        opacity: 1;
        display: block;
    }
    #dropdownButton[aria-expanded="true"] svg {
        transform: rotate(180deg);
    }
    a:hover svg {
        color: #3B82F6;
    }
    </style>
</head>
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
    <div id="main-content" class="transition-all duration-500 ml-64 main-content">
        <x-admin-nav-bar />


        <!-- Main Content -->
        <div class="min-h-screen">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8 mx-10 mt-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Attendance History</h1>
                </div>
                <x-attendance-menu />
            </div>

            <!-- Filters -->
            <div class="shadcn-card p-6 mb-8 mx-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" id="selectedDate" class="shadcn-input" value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">College</label>
                        <select id="collegeFilter" class="shadcn-input">
                            <option value="">All Colleges</option>
                            <option value="CICS">CICS</option>
                            <option value="CTED">CTED</option>
                            <option value="CCJE">CCJE</option>
                            <option value="CHM">CHM</option>
                            <option value="CBEA">CBEA</option>
                            <option value="CA">CA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="statusFilter" class="shadcn-input">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="logged_out">Logged Out</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button id="printButton" class="shadcn-button bg-blue-600 hover:bg-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button id="exportExcel" class="shadcn-button bg-green-600 hover:bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="shadcn-card overflow-hidden mx-8">
                <div class="overflow-x-auto">
                    <table id="attendanceTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">College</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Table content will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .shadcn-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }
        .shadcn-button {
            background-color: #18181b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .shadcn-button:hover {
            background-color: #27272a;
        }
        .shadcn-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            background-color: white;
        }
        .shadcn-input:focus {
            outline: none;
            border-color: #18181b;
            box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.1);
        }

        /* College-specific colors */
        .college-CICS {
            background-color: #f3e8ff !important; /* violet-100 */
            color: #6b21a8 !important; /* violet-800 */
        }
        .college-CTED {
            background-color: #dbeafe !important; /* blue-100 */
            color: #1e40af !important; /* blue-800 */
        }
        .college-CCJE {
            background-color: #fee2e2 !important; /* red-100 */
            color: #991b1b !important; /* red-800 */
        }
        .college-CHM {
            background-color: #fce7f3 !important; /* pink-100 */
            color: #9d174d !important; /* pink-800 */
        }
        .college-CBEA {
            background-color: #fef9c3 !important; /* yellow-100 */
            color: #854d0e !important; /* yellow-800 */
        }
        .college-CA {
            background-color: #dcfce7 !important; /* green-100 */
            color: #166534 !important; /* green-800 */
        }

        @media print {
            /* Reset all margins and padding */
            * {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Hide everything except the table */
            nav, 
            .shadcn-button, 
            #applyFilter, 
            #printButton, 
            #exportExcel,
            .container > div:not(:last-child),
            .shadcn-card:not(:last-child) {
                display: none !important;
            }

            /* Reset container styles */
            .container {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
            }

            /* Show only the table card */
            .shadcn-card:last-child {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
            }

            /* Ensure table is visible and fits page */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 12px !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Add borders to table cells for better print layout */
            th, td {
                border: 1px solid #000 !important;
                padding: 4px !important;
                text-align: left !important;
            }

            /* Make table headers bold */
            th {
                background-color: #f3f4f6 !important;
                font-weight: bold !important;
            }

            /* Ensure text is black for better printing */
            body {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
                position: relative !important;
            }

            /* Ensure college badges are visible but simpler */
            .college-CICS,
            .college-CTED,
            .college-CCJE,
            .college-CHM,
            .college-CBEA,
            .college-CA {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 2px 4px !important;
                border-radius: 2px !important;
            }

            /* Remove any overflow */
            .overflow-x-auto {
                overflow: visible !important;
            }

            /* Remove any unnecessary spacing */
            .shadcn-card {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Force page breaks */
            table {
                page-break-inside: auto !important;
            }
            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load history data
            async function loadHistoryData() {
                const date = document.getElementById('selectedDate').value;
                const college = document.getElementById('collegeFilter').value;
                const status = document.getElementById('statusFilter').value;

                try {
                    const response = await fetch(`/admin/attendance/history-data?date=${date}&college=${college}&status=${status}`);
                    const data = await response.json();

                    // Update table
                    const tableBody = document.getElementById('historyTableBody');
                    if (data.history.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No attendance records found for the selected filters
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    tableBody.innerHTML = data.history.map(record => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${record.student_id}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${record.student_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full college-${record.college}">${record.college}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${record.activity}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${record.time_in}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${record.time_out || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${record.time_out ? 
                                    '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Logged Out</span>' :
                                    '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>'
                                }
                            </td>
                        </tr>
                    `).join('');
                } catch (error) {
                    console.error('Error loading history data:', error);
                }
            }

            // Load initial data
            loadHistoryData();

            // Add event listeners for instant filtering
            document.getElementById('selectedDate').addEventListener('change', loadHistoryData);
            document.getElementById('collegeFilter').addEventListener('change', loadHistoryData);
            document.getElementById('statusFilter').addEventListener('change', loadHistoryData);

            // Print functionality
            document.getElementById('printButton').addEventListener('click', function() {
                window.print();
            });

            // Excel export functionality
            document.getElementById('exportExcel').addEventListener('click', function() {
                const table = document.getElementById('attendanceTable');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Attendance History"});
                const date = document.getElementById('selectedDate').value;
                XLSX.writeFile(wb, `attendance_history_${date}.xlsx`);
            });
        });
    </script>
</body>
</html> 