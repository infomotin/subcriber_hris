@extends('layouts.subscriber')

@section('title', 'Holiday Calendar Setup')

@section('content')
<style>
    .calendar-month-card {
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 3px;
        font-size: 0.8rem;
    }
    .calendar-header-day {
        text-align: center;
        font-weight: 700;
        color: #64748b;
        padding: 4px 0;
        font-size: 0.75rem;
    }
    .calendar-day-cell {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        font-weight: 500;
    }
    .calendar-day-cell:hover {
        transform: scale(1.1);
        z-index: 10;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    /* Day Status Styles */
    .day-working {
        background-color: #f8fafc;
        color: #334155;
    }
    .day-weekly-holiday {
        background-color: #eef2ff;
        color: #4f46e5;
        border: 1px solid rgba(79, 70, 229, 0.15);
    }
    .day-holy-day {
        background-color: #fff1f2;
        color: #e11d48;
        border: 1px solid rgba(244, 63, 94, 0.15);
    }
    .day-empty {
        background-color: transparent;
        cursor: default;
        pointer-events: none;
    }
    .day-number {
        font-size: 0.85rem;
        font-weight: 700;
    }
    .holiday-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #e11d48;
        margin-top: 1px;
    }
    .weekly-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #4f46e5;
        margin-top: 1px;
    }
    /* Legend styling */
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .legend-color {
        width: 14px;
        height: 14px;
        border-radius: 4px;
    }
</style>

<div class="page-title-box mb-4">
    <div>
        <span class="text-primary fw-bold text-uppercase font-size-10 tracking-wider d-block mb-1">HRIS Setup</span>
        <h4 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0f172a;"><i class="bx bx-calendar text-primary me-2 align-middle"></i>Holiday & Work Calendar Year</h4>
    </div>
    <div class="page-title-right d-flex gap-2">
        <select class="form-select font-size-13 py-1 px-3 border-secondary" id="calendarYearSelect" style="width: 120px; border-radius: 30px; height: 38px !important;">
            <option value="2025">Year 2025</option>
            <option value="2026" selected>Year 2026</option>
            <option value="2027">Year 2027</option>
        </select>
        <button class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm" onclick="saveToSession()">
            <i class="bx bx-save me-1"></i> Save Calendar
        </button>
    </div>
</div>

<!-- Info banner / Legend -->
<div class="card border-0 mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h6 class="fw-bold text-slate-800 mb-1" style="font-family: 'Poppins', sans-serif;">Enterprise Calendar Configuration</h6>
            <p class="text-muted font-size-13 mb-0">Select any date square to update its work status or add organization holiday declarations.</p>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="legend-item">
                <span class="legend-color day-working border"></span>
                <span class="text-slate-600">Working Day</span>
            </div>
            <div class="legend-item">
                <span class="legend-color day-weekly-holiday"></span>
                <span class="text-indigo-600">Weekly Holiday (Fri/Sat)</span>
            </div>
            <div class="legend-item">
                <span class="legend-color day-holy-day"></span>
                <span class="text-danger">Holy Day (Holiday)</span>
            </div>
        </div>
    </div>
</div>

<!-- Full 12 Months Grid -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3 mb-5" id="calendarYearGrid">
    <!-- Rendered via Javascript -->
</div>

<!-- Day Editor Modal -->
<div class="modal fade" id="modalEditDay" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title fw-bold" id="modalEditDayTitle" style="font-family: 'Poppins', sans-serif;"><i class="bx bx-edit text-primary me-2 align-middle"></i>Edit Day Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 text-center bg-light p-3 rounded-3 border border-slate-100">
                    <span class="text-muted font-size-11 d-block mb-1 uppercase tracking-wide">Selected Date</span>
                    <h5 class="fw-bold text-dark mb-0" id="displaySelectedDate">January 1, 2026</h5>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-size-12 uppercase tracking-wide">Day Status</label>
                    <div class="d-flex flex-column gap-2">
                        <label class="d-block cursor-pointer">
                            <input type="radio" name="day_status" id="statusWorking" value="working" class="btn-check" checked>
                            <div class="p-2.5 rounded-3 border text-center font-size-13 fw-semibold text-slate-700 billing-card">
                                Working Day
                            </div>
                        </label>
                        <label class="d-block cursor-pointer">
                            <input type="radio" name="day_status" id="statusWeekly" value="weekly" class="btn-check">
                            <div class="p-2.5 rounded-3 border text-center font-size-13 fw-semibold text-indigo-700 billing-card">
                                Weekly Holiday
                            </div>
                        </label>
                        <label class="d-block cursor-pointer">
                            <input type="radio" name="day_status" id="statusHoly" value="holy" class="btn-check">
                            <div class="p-2.5 rounded-3 border text-center font-size-13 fw-semibold text-danger billing-card">
                                Holy Day (Holiday)
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-3 d-none" id="holidayTitleWrapper">
                    <label for="holidayTitleInput" class="form-label fw-bold font-size-12 uppercase tracking-wide">Holiday Occasion Title</label>
                    <input type="text" class="form-control" id="holidayTitleInput" placeholder="e.g. Independence Day / Annual Outing" style="border: 2px solid #e2e8f0 !important; border-radius: 10px;">
                </div>
            </div>
            <div class="modal-footer border-top py-3 bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" onclick="applyDayChange()"><i class="bx bx-check-circle me-1"></i> Apply Changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. Defined Standard Holidays
    const defaultHolidays = {
        // Format: 'MM-DD': { name: 'Holiday Title', isBDGov: true }
        '01-01': { name: "New Year's Day", isBDGov: false },
        '02-21': { name: 'Language Martyrs Day', isBDGov: true },
        '03-17': { name: 'Bangabandhu Birthday', isBDGov: true },
        '03-26': { name: 'Independence Day', isBDGov: true },
        '04-14': { name: 'Bengali New Year', isBDGov: true },
        '05-01': { name: 'May Day', isBDGov: true },
        '05-28': { name: 'Eid-ul-Fitr (Tentative)', isBDGov: true },
        '08-03': { name: 'Eid-ul-Adha (Tentative)', isBDGov: true },
        '08-15': { name: 'National Mourning Day', isBDGov: true },
        '10-21': { name: 'Durga Puja', isBDGov: true },
        '12-16': { name: 'Victory Day', isBDGov: true },
        '12-25': { name: 'Christmas Day', isBDGov: true }
    };

    let selectedYear = 2026;
    let activeDayCell = null;
    let editDayModal = null;
    let customCalendarState = {};

    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const dayNames = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

    document.addEventListener('DOMContentLoaded', function () {
        editDayModal = new bootstrap.Modal(document.getElementById('modalEditDay'));
        
        // Load settings from localStorage if available
        loadCalendarState();

        const yearSelect = document.getElementById('calendarYearSelect');
        selectedYear = parseInt(yearSelect.value);
        renderFullYear();

        yearSelect.addEventListener('change', function() {
            selectedYear = parseInt(this.value);
            renderFullYear();
        });

        // Modal radio visibility handler
        const statusHolyRadio = document.getElementById('statusHoly');
        const holidayTitleWrapper = document.getElementById('holidayTitleWrapper');
        
        document.querySelectorAll('input[name="day_status"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (statusHolyRadio.checked) {
                    holidayTitleWrapper.classList.remove('d-none');
                } else {
                    holidayTitleWrapper.classList.add('d-none');
                }
            });
        });
    });

    function loadCalendarState() {
        const stored = localStorage.getItem('Nexozaint_Calendar_State');
        if (stored) {
            try {
                customCalendarState = JSON.parse(stored);
            } catch(e) {
                customCalendarState = {};
            }
        }
    }

    function saveCalendarState() {
        localStorage.setItem('Nexozaint_Calendar_State', JSON.stringify(customCalendarState));
    }

    function getDaysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
    }

    function getStartDayOfMonth(year, month) {
        return new Date(year, month, 1).getDay();
    }

    function renderFullYear() {
        const gridContainer = document.getElementById('calendarYearGrid');
        gridContainer.innerHTML = '';

        for (let m = 0; m < 12; m++) {
            const cardCol = document.createElement('div');
            cardCol.className = 'col';

            const card = document.createElement('div');
            card.className = 'card calendar-month-card border-0 p-3';

            const mTitle = document.createElement('h6');
            mTitle.className = 'fw-bold text-slate-800 border-bottom pb-2 mb-3 text-center';
            mTitle.style.fontFamily = "'Poppins', sans-serif";
            mTitle.textContent = monthNames[m] + ' ' + selectedYear;
            card.appendChild(mTitle);

            const grid = document.createElement('div');
            grid.className = 'calendar-grid';

            // Weekday headers
            dayNames.forEach(d => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-header-day';
                dayHeader.textContent = d;
                grid.appendChild(dayHeader);
            });

            // Empty slots at start
            const startDay = getStartDayOfMonth(selectedYear, m);
            for (let i = 0; i < startDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'calendar-day-cell day-empty';
                grid.appendChild(emptyCell);
            }

            // Month days
            const daysCount = getDaysInMonth(selectedYear, m);
            for (let d = 1; d <= daysCount; d++) {
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell';
                cell.setAttribute('data-year', selectedYear);
                cell.setAttribute('data-month', m);
                cell.setAttribute('data-day', d);

                const dayNum = document.createElement('span');
                dayNum.className = 'day-number';
                dayNum.textContent = d;
                cell.appendChild(dayNum);

                // Date Key
                const dateKey = `${selectedYear}-${String(m+1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const shortKey = `${String(m+1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                
                // Determine Day Status
                let status = 'working';
                let holidayName = '';

                // Check custom localStorage state overrides first
                if (customCalendarState[dateKey]) {
                    status = customCalendarState[dateKey].status;
                    holidayName = customCalendarState[dateKey].name || '';
                } else {
                    // Check standard weekly holidays (Fridays and Saturdays in BD)
                    const dayOfWeek = new Date(selectedYear, m, d).getDay();
                    if (dayOfWeek === 5 || dayOfWeek === 6) {
                        status = 'weekly';
                    }
                    // Check BD/International defaults
                    if (defaultHolidays[shortKey]) {
                        status = 'holy';
                        holidayName = defaultHolidays[shortKey].name;
                    }
                }

                // Apply CSS Classes & title tooltips
                if (status === 'weekly') {
                    cell.classList.add('day-weekly-holiday');
                    const dot = document.createElement('div');
                    dot.className = 'weekly-dot';
                    cell.appendChild(dot);
                    cell.title = 'Weekly Holiday';
                } else if (status === 'holy') {
                    cell.classList.add('day-holy-day');
                    const dot = document.createElement('div');
                    dot.className = 'holiday-dot';
                    cell.appendChild(dot);
                    cell.title = holidayName || 'Public Holiday';
                } else {
                    cell.classList.add('day-working');
                    cell.title = 'Working Day';
                }

                // Click action
                cell.addEventListener('click', function() {
                    openDayEditor(this, status, holidayName);
                });

                grid.appendChild(cell);
            }

            card.appendChild(grid);
            cardCol.appendChild(card);
            gridContainer.appendChild(cardCol);
        }
    }

    function openDayEditor(cell, currentStatus, holidayName) {
        activeDayCell = cell;
        
        const y = parseInt(cell.getAttribute('data-year'));
        const m = parseInt(cell.getAttribute('data-month'));
        const d = parseInt(cell.getAttribute('data-day'));

        const dateObj = new Date(y, m, d);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('displaySelectedDate').textContent = dateObj.toLocaleDateString('en-US', options);

        // Prepopulate radio inputs
        if (currentStatus === 'weekly') {
            document.getElementById('statusWeekly').checked = true;
        } else if (currentStatus === 'holy') {
            document.getElementById('statusHoly').checked = true;
        } else {
            document.getElementById('statusWorking').checked = true;
        }

        // Prepopulate holiday title
        const holidayTitleInput = document.getElementById('holidayTitleInput');
        holidayTitleInput.value = holidayName;
        
        if (currentStatus === 'holy') {
            document.getElementById('holidayTitleWrapper').classList.remove('d-none');
        } else {
            document.getElementById('holidayTitleWrapper').classList.add('d-none');
        }

        editDayModal.show();
    }

    function applyDayChange() {
        if (!activeDayCell) return;

        const y = activeDayCell.getAttribute('data-year');
        const m = activeDayCell.getAttribute('data-month');
        const d = activeDayCell.getAttribute('data-day');
        const dateKey = `${y}-${String(parseInt(m)+1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

        // Get status
        let status = 'working';
        if (document.getElementById('statusWeekly').checked) {
            status = 'weekly';
        } else if (document.getElementById('statusHoly').checked) {
            status = 'holy';
        }

        const name = document.getElementById('holidayTitleInput').value.trim();

        // Update customState overrides map
        customCalendarState[dateKey] = { status: status, name: name };
        saveCalendarState();

        // Re-render
        renderFullYear();
        editDayModal.hide();
    }

    function saveToSession() {
        saveCalendarState();
        alert('All updates successfully stored in local session databases! (Mock State Persisted).');
    }
</script>
@endpush
@endsection
