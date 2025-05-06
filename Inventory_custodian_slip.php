<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory Custodian Slip</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            university: {
              red: '#DC143C',
              darkred: '#B01030',
              light: '#FFF5F7',
              gold: '#FFD700',
              navy: '#0A2463',
            },
            ics: {
              light: '#F9FAFB',
              border: '#E5E7EB',
              header: '#F3F4F6',
              input: 'rgba(239, 246, 255, 0.6)', // Light blue background for inputs
              focus: '#DBEAFE', // Slightly darker blue for focus
              required: 'rgba(254, 242, 242, 0.6)', // Light red for required fields
            }
          },
          fontFamily: {
            sans: ['Poppins', 'Segoe UI', 'sans-serif'],
            serif: ['Playfair Display', 'Georgia', 'serif'],
          },
          boxShadow: {
            'ics': '0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02)',
            'input': '0 2px 3px rgba(0, 0, 0, 0.05)',
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap');
    
    body {
      background-image: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
      background-attachment: fixed;
    }
    
    /* Animated dotted border for inputs */
    @keyframes borderDance {
      0% { background-position: 0% 0%; }
      100% { background-position: 100% 0%; }
    }
    
    .input-field {
      background-color: rgba(239, 246, 255, 0.6);
      border: 1px solid #E5E7EB;
      border-radius: 4px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
    }
    
    .input-field:hover {
      background-color: rgba(219, 234, 254, 0.7);
      border-color: #93C5FD;
      transform: translateY(-1px);
    }
    
    .input-field:focus {
      background-color: #FFFFFF;
      border-color: #3B82F6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
      outline: none;
      transform: translateY(-1px);
    }
    
    .required-field {
      background-color: rgba(254, 242, 242, 0.6);
      border: 1px solid #FCA5A5;
    }
    
    .required-field:hover {
      background-color: rgba(254, 226, 226, 0.7);
      border-color: #F87171;
    }
    
    .required-field:focus {
      background-color: #FFFFFF;
      border-color: #EF4444;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }
    
    .signature-line {
      background-image: linear-gradient(to right, #CBD5E1 50%, transparent 50%);
      background-size: 8px 1px;
      background-repeat: repeat-x;
      background-position: bottom;
      animation: borderDance 30s linear infinite;
    }
    
    .university-seal {
      position: relative;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: radial-gradient(circle, #FFF5F7 0%, #FFE8EC 100%);
      border: 2px solid #DC143C;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .university-seal::before {
      content: '';
      position: absolute;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      border: 1px solid rgba(220, 20, 60, 0.3);
    }
    
    .university-seal::after {
      content: '';
      position: absolute;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      border: 1px solid rgba(220, 20, 60, 0.2);
    }
    
    .decorative-corner {
      position: absolute;
      width: 100px;
      height: 100px;
      opacity: 0.1;
    }
    
    .top-left {
      top: 0;
      left: 0;
      border-top: 3px solid #DC143C;
      border-left: 3px solid #DC143C;
      border-top-left-radius: 8px;
    }
    
    .top-right {
      top: 0;
      right: 0;
      border-top: 3px solid #DC143C;
      border-right: 3px solid #DC143C;
      border-top-right-radius: 8px;
    }
    
    .bottom-left {
      bottom: 0;
      left: 0;
      border-bottom: 3px solid #DC143C;
      border-left: 3px solid #DC143C;
      border-bottom-left-radius: 8px;
    }
    
    .bottom-right {
      bottom: 0;
      right: 0;
      border-bottom: 3px solid #DC143C;
      border-right: 3px solid #DC143C;
      border-bottom-right-radius: 8px;
    }
    
    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-45deg);
      font-size: 150px;
      color: rgba(220, 20, 60, 0.03);
      font-weight: bold;
      pointer-events: none;
      white-space: nowrap;
      z-index: 0;
    }
    
    /* Table styles */
    .ics-table th {
      position: relative;
      overflow: hidden;
    }
    
    .ics-table th::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background: linear-gradient(to right, transparent, rgba(220, 20, 60, 0.3), transparent);
    }
    
    .ics-table tr:hover td {
      background-color: rgba(249, 250, 251, 0.8);
    }
    
    /* Read-only field styling */
    input[readonly] {
      background-color: rgba(243, 244, 246, 0.7);
      border-color: #D1D5DB;
      cursor: not-allowed;
    }
    
    /* Add row button animation */
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    .add-row-btn:hover {
      animation: pulse 1s infinite;
    }
    
    @media print {
      body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
        background: none;
      }
      .no-print {
        display: none;
      }
      .container {
        box-shadow: none !important;
        max-width: 100% !important;
      }
      .input-field, .required-field {
        border: 1px dashed #CBD5E1 !important;
        background-color: transparent !important;
      }
      .input-field:focus, .required-field:focus {
        outline: none !important;
        box-shadow: none !important;
      }
      .watermark {
        display: none;
      }
    }
  </style>
</head>
<body class="py-8 px-4 sm:px-6 md:px-8 min-h-screen">
  <!-- Instructions Banner -->
  <div class="max-w-5xl mx-auto mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 rounded-md shadow-md no-print">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-blue-800">Filling Instructions</h3>
        <div class="mt-1 text-sm text-blue-700">
          <p>Click on highlighted areas to fill in the required information. <span class="bg-ics-input px-2 py-0.5 rounded border border-blue-200">Blue fields</span> are optional and <span class="bg-ics-required px-2 py-0.5 rounded border border-red-200">red fields</span> are required. Click "Add Row" to add more items.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Print Button -->
  <div class="max-w-5xl mx-auto mb-4 flex justify-end no-print">
    <button onclick="window.print()" class="bg-gradient-to-r from-university-red to-university-darkred text-white px-5 py-2.5 rounded-md shadow-md transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
      </svg>
      Print Inventory Slip
    </button>
  </div>

  <!-- Main Container -->
  <div class="container max-w-5xl mx-auto bg-white rounded-xl shadow-ics overflow-hidden relative">
    <!-- Decorative Corners -->
    <div class="decorative-corner top-left"></div>
    <div class="decorative-corner top-right"></div>
    <div class="decorative-corner bottom-left"></div>
    <div class="decorative-corner bottom-right"></div>
    
    <!-- Watermark -->
    <div class="watermark">WMSU</div>
    
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-university-light via-white to-university-light border-b border-ics-border relative">
      <div class="py-8 px-8">
        <div class="flex flex-col items-center justify-center">
          <div class="flex items-center mb-4">
            <div class="university-seal mr-4">
              <span class="text-university-red font-serif font-bold text-xl">WMSU</span>
            </div>
            <div class="text-center">
              <h1 class="text-2xl md:text-3xl font-bold text-university-red tracking-tight font-serif">WESTERN MINDANAO STATE UNIVERSITY</h1>
              <div class="h-0.5 bg-gradient-to-r from-transparent via-university-red to-transparent mt-1 mb-2"></div>
              <h2 class="text-lg md:text-xl font-semibold text-gray-700 mt-2">Property Management Office</h2>
              <h3 class="text-gray-600 font-medium">Zamboanga City</h3>
            </div>
          </div>
          <div class="mt-4 bg-gradient-to-r from-university-red/10 via-university-red/20 to-university-red/10 px-8 py-3 rounded-full border border-university-red/20 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800 font-serif tracking-wide">INVENTORY CUSTODIAN SLIP</h2>
          </div>
        </div>
      </div>
      <!-- Decorative Wave -->
      <div class="absolute bottom-0 left-0 right-0 h-2 bg-gradient-to-r from-university-red/0 via-university-red/30 to-university-red/0"></div>
    </div>

    <!-- Document Body -->
    <div class="p-6 md:p-8 relative z-10">
      <!-- ICS Number Section -->
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
          <span class="font-semibold text-gray-700 mr-2">ICS No:</span>
          <input type="text" placeholder="Enter ICS Number" class="input-field required-field px-3 py-2 w-48 text-gray-700" />
          <div class="ml-1 text-university-red text-lg">*</div>
        </div>
        <div class="flex items-center">
          <span class="font-semibold text-gray-700 mr-2">Date:</span>
          <input type="text" placeholder="April 2, 2025" class="input-field required-field px-3 py-2 w-48 text-gray-700" />
          <div class="ml-1 text-university-red text-lg">*</div>
        </div>
      </div>

      <!-- Items Table -->
      <div class="overflow-x-auto mb-4">
        <table class="w-full border-collapse ics-table">
          <thead>
            <tr class="bg-gradient-to-r from-gray-100 to-gray-50">
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">QTY</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">UNIT</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">ARTICLE</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">DESCRIPTION</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">ACCOUNT CODE</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">INVENTORY ITEM NO.</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">ESTIMATED USEFUL LIFE</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">AMOUNT</th>
              <th class="border border-ics-border px-4 py-3 text-left text-sm font-semibold text-gray-700">TOTAL AMOUNT</th>
            </tr>
          </thead>
          <tbody id="item-rows">
            <tr class="hover:bg-gray-50 transition-colors duration-150">
              <td class="border border-ics-border px-4 py-3">
                <input type="number" id="qty-1" value="1" min="1" class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm" onchange="updateTotal(1)" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., piece" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., Headset" class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="Enter description" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., 2-02-01-050" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="Enter Inventory No." class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., 1 year" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="number" id="amount-1" value="0.00" step="0.01" class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm text-right" onchange="updateTotal(1)" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="number" id="total-1" value="0.00" class="w-full px-2 py-1 text-gray-700 text-sm text-right bg-gray-50" readonly />
              </td>
            </tr>
            <tr class="hover:bg-gray-50 transition-colors duration-150">
              <td class="border border-ics-border px-4 py-3">
                <input type="number" id="qty-2" value="1" min="1" class="input-field w-full px-2 py-1 text-gray-700 text-sm" onchange="updateTotal(2)" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., piece" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., Flash Drive" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="Enter description" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., 2-02-01-050" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="Enter Inventory No." class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="text" placeholder="e.g., 1 year" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="number" id="amount-2" value="0.00" step="0.01" class="input-field w-full px-2 py-1 text-gray-700 text-sm text-right" onchange="updateTotal(2)" />
              </td>
              <td class="border border-ics-border px-4 py-3">
                <input type="number" id="total-2" value="0.00" class="w-full px-2 py-1 text-gray-700 text-sm text-right bg-gray-50" readonly />
              </td>
            </tr>
            <!-- Total Row will be added by JavaScript -->
          </tbody>
          <tfoot>
            <tr class="bg-gradient-to-r from-gray-100 to-gray-50 font-medium" id="total-row">
              <td colspan="8" class="border border-ics-border px-4 py-3 text-right">
                <span class="font-semibold text-gray-700">TOTAL:</span>
              </td>
              <td class="border border-ics-border px-4 py-3 bg-gray-50">
                <div id="grand-total" class="text-right font-bold text-gray-800 px-2">P 0.00</div>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
      
      <!-- Add Row Button -->
      <div class="flex justify-center mb-8 no-print">
        <button id="add-row-btn" class="add-row-btn bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-md shadow-sm transition-all duration-300 hover:shadow-md flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Row
        </button>
      </div>

      <!-- Notes Section -->
      <div class="mb-8 bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="mb-4">
          <p class="font-semibold text-gray-700 mb-2">NOTE:</p>
          <input type="text" placeholder="Additional to Existing ICS / Other Notes" class="input-field w-full px-3 py-2 text-gray-700" />
        </div>
        
        <div>
          <p class="font-semibold text-gray-700 mb-2">Delivery Details:</p>
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center">
              <span class="text-gray-600 mr-2">Inv. No.:</span>
              <input type="text" placeholder="e.g., 08899" class="input-field w-40 px-3 py-2 text-gray-700" />
            </div>
            <div class="flex items-center">
              <span class="text-gray-600 mr-2">D.R.:</span>
              <input type="text" placeholder="e.g., 11318" class="input-field w-40 px-3 py-2 text-gray-700" />
            </div>
          </div>
        </div>
      </div>

      <!-- Signature Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Left Signature Block -->
        <div class="border border-ics-border rounded-lg overflow-hidden shadow-sm">
          <div class="bg-gradient-to-r from-gray-100 to-gray-50 px-4 py-3 border-b border-ics-border">
            <h3 class="font-semibold text-gray-700">Received by:</h3>
          </div>
          <div class="p-5">
            <div class="mb-6">
              <input type="text" placeholder="Name of Recipient" class="input-field required-field w-full px-3 py-2 text-gray-700 font-medium text-center" />
              <div class="text-university-red text-lg text-center mt-1">*</div>
              <p class="text-xs text-gray-500 text-center mt-1">Signature Over Printed Name</p>
            </div>
            
            <div class="mb-6">
              <input type="text" placeholder="Position/Office" class="input-field w-full px-3 py-2 text-gray-700 text-center" />
              <p class="text-xs text-gray-500 text-center mt-1">Position/Office</p>
            </div>
            
            <div>
              <input type="text" placeholder="Date (e.g., April 2, 2025)" class="input-field required-field w-full px-3 py-2 text-gray-700 text-center" />
              <div class="text-university-red text-lg text-center mt-1">*</div>
              <p class="text-xs text-gray-500 text-center mt-1">Date</p>
            </div>
          </div>
        </div>
        
        <!-- Right Signature Block -->
        <div class="border border-ics-border rounded-lg overflow-hidden shadow-sm">
          <div class="bg-gradient-to-r from-gray-100 to-gray-50 px-4 py-3 border-b border-ics-border">
            <h3 class="font-semibold text-gray-700">Received from:</h3>
          </div>
          <div class="p-5">
            <div class="mb-6">
              <input type="text" placeholder="Name of Property Officer" class="input-field required-field w-full px-3 py-2 text-gray-700 font-medium text-center" />
              <div class="text-university-red text-lg text-center mt-1">*</div>
              <p class="text-xs text-gray-500 text-center mt-1">Signature Over Printed Name</p>
            </div>
            
            <div class="mb-6">
              <input type="text" placeholder="Position/Office" class="input-field w-full px-3 py-2 text-gray-700 text-center" />
              <p class="text-xs text-gray-500 text-center mt-1">Office</p>
            </div>
            
            <div>
              <input type="text" placeholder="Date (e.g., April 2, 2025)" class="input-field required-field w-full px-3 py-2 text-gray-700 text-center" />
              <div class="text-university-red text-lg text-center mt-1">*</div>
              <p class="text-xs text-gray-500 text-center mt-1">Date</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="bg-gradient-to-r from-gray-100 to-gray-50 py-4 px-8 border-t border-ics-border mt-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center">
          <div class="w-3 h-3 rounded-full bg-university-red mr-2"></div>
          <p class="text-xs text-gray-500 font-medium">WMSU-PMO-ICS-001</p>
        </div>
        <div class="flex items-center">
          <div class="flex items-center mr-4">
            <div class="w-3 h-3 bg-ics-input border border-blue-200 rounded-sm mr-1"></div>
            <span class="text-xs text-gray-500">Optional Field</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 bg-ics-required border border-red-200 rounded-sm mr-1"></div>
            <span class="text-xs text-gray-500">Required Field</span>
          </div>
        </div>
        <div class="flex items-center">
          <p class="text-xs text-gray-500">Page 1 of 1</p>
          <div class="w-3 h-3 rounded-full bg-university-red ml-2"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let rowCount = 2;
    
    function updateTotal(row) {
      let qty = parseFloat(document.getElementById(`qty-${row}`).value) || 0;
      let amount = parseFloat(document.getElementById(`amount-${row}`).value) || 0;
      let total = (qty * amount).toFixed(2);
      document.getElementById(`total-${row}`).value = total;
      updateGrandTotal();
    }

    function updateGrandTotal() {
      let grandTotal = 0;
      for (let i = 1; i <= rowCount; i++) {
        let rowTotal = parseFloat(document.getElementById(`total-${i}`)?.value) || 0;
        grandTotal += rowTotal;
      }
      document.getElementById("grand-total").innerText = "P " + grandTotal.toFixed(2);
    }
    
    document.getElementById('add-row-btn').addEventListener('click', function() {
      rowCount++;
      const newRow = document.createElement('tr');
      newRow.className = 'hover:bg-gray-50 transition-colors duration-150';
      
      newRow.innerHTML = `
        <td class="border border-ics-border px-4 py-3">
          <input type="number" id="qty-${rowCount}" value="1" min="1" class="input-field w-full px-2 py-1 text-gray-700 text-sm" onchange="updateTotal(${rowCount})" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="text" placeholder="e.g., piece" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="text" placeholder="Enter article name" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="text" placeholder="Enter description" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="text" placeholder="e.g., 2-02-01-050" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="text" placeholder="Enter Inventory No." class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="text" placeholder="e.g., 1 year" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="number" id="amount-${rowCount}" value="0.00" step="0.01" class="input-field w-full px-2 py-1 text-gray-700 text-sm text-right" onchange="updateTotal(${rowCount})" />
        </td>
        <td class="border border-ics-border px-4 py-3">
          <input type="number" id="total-${rowCount}" value="0.00" class="w-full px-2 py-1 text-gray-700 text-sm text-right bg-gray-50" readonly />
        </td>
      `;
      
      // Insert the new row before the total row
      const totalRow = document.getElementById('total-row');
      document.getElementById('item-rows').insertBefore(newRow, null);
      
      // Update calculations
      updateTotal(rowCount);
    });
    
    // Optional: Add JavaScript to highlight unfilled required fields when printing
    document.querySelector('button[onclick="window.print()"]').addEventListener('click', function() {
      const requiredFields = document.querySelectorAll('.required-field');
      let allFilled = true;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.style.backgroundColor = '#FECACA';
          allFilled = false;
        }
      });
      
      if (!allFilled) {
        if (!confirm('Some required fields are not filled. Do you still want to print?')) {
          event.preventDefault();
          return false;
        }
      }
    });
  </script>
</body>
</html>