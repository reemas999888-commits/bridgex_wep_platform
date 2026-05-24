// project_form.js — BridgeX Platform
// Multi-step form logic | Aryam

let currentStep = 1;
const totalSteps = 3;

// Next Step
function nextStep(step) {
    if (!validateStep(step)) return;

    document.querySelector(`[data-step="${step}"]`).classList.add('done');
    document.querySelector(`[data-step="${step}"]`).classList.remove('active');

    document.getElementById(`step-${step}`).classList.add('hidden');

    currentStep = step + 1;

    document.getElementById(`step-${currentStep}`).classList.remove('hidden');

    const nextEl = document.querySelector(`[data-step="${currentStep}"]`);
    if (nextEl) {
        nextEl.classList.add('active');
    }

    if (currentStep === 3) {
        buildSummary();
    }

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Previous Step
function prevStep(step) {
    document.getElementById(`step-${step}`).classList.add('hidden');

    currentStep = step - 1;

    document.getElementById(`step-${currentStep}`).classList.remove('hidden');

    document.querySelector(`[data-step="${currentStep}"]`).classList.add('active');
    document.querySelector(`[data-step="${currentStep}"]`).classList.remove('done');
    document.querySelector(`[data-step="${step}"]`).classList.remove('active');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Validate Step
function validateStep(step) {
    let valid = true;

    if (step === 1) {
        valid = checkField('title', 'Please enter the project title')
            & checkField('category', 'Please select a project category')
            & checkTextarea('description', 'Please enter a project description', 20);
    }

    if (step === 2) {
        valid = checkField('budget', 'Please enter the estimated budget')
            & checkField('duration', 'Please select the expected duration');

        const budgetVal = document.getElementById('budget').value.trim();

        if (budgetVal && isNaN(budgetVal)) {
            showError('err-budget', 'Please enter a valid number');
            valid = false;
        }
    }

    return !!valid;
}

function checkField(id, msg) {
    const el = document.getElementById(id);
    const err = document.getElementById('err-' + id);

    if (!el) return true;

    if (!el.value.trim()) {
        if (err) {
            showError('err-' + id, msg);
        }

        el.focus();
        return false;
    }

    if (err) {
        clearError('err-' + id);
    }

    return true;
}

function checkTextarea(id, msg, minLen) {
    const el = document.getElementById(id);
    const err = document.getElementById('err-' + id);

    if (!el) return true;

    if (el.value.trim().length < minLen) {
        if (err) {
            showError('err-' + id, msg + ' (' + minLen + ' characters minimum)');
        }

        el.focus();
        return false;
    }

    if (err) {
        clearError('err-' + id);
    }

    return true;
}

function showError(id, msg) {
    const el = document.getElementById(id);

    if (el) {
        el.textContent = msg;
    }
}

function clearError(id) {
    const el = document.getElementById(id);

    if (el) {
        el.textContent = '';
    }
}

// Build Summary
function buildSummary() {
    const categoryLabels = {
        web: 'Website',
        mobile: 'Mobile App',
        design: 'UI/UX Design',
        backend: 'Backend / API',
        ecommerce: 'E-commerce Store',
        other: 'Other'
    };

    const durationLabels = {
        less_week: 'Less than a week',
        '1_2_weeks': '1-2 weeks',
        '1_month': '1 month',
        '2_3_months': '2-3 months',
        more_3: 'More than 3 months'
    };

    const title = document.getElementById('title').value;
    const category = document.getElementById('category').value;
    const desc = document.getElementById('description').value;
    const budget = document.getElementById('budget').value;
    const duration = document.getElementById('duration').value;
    const skills = document.getElementById('skills').value;
    const notes = document.getElementById('notes').value;

    const items = [
        {
            label: 'Project Title',
            value: title,
            full: false
        },
        {
            label: 'Category',
            value: categoryLabels[category] || category,
            full: false
        },
        {
            label: 'Budget',
            value: budget + ' SAR',
            full: false
        },
        {
            label: 'Expected Duration',
            value: durationLabels[duration] || duration,
            full: false
        },
        {
            label: 'Required Skills',
            value: skills || '—',
            full: false
        },
        {
            label: 'Project Description',
            value: desc,
            full: true
        },
        {
            label: 'Additional Notes',
            value: notes || '—',
            full: true
        }
    ];

    const container = document.getElementById('project-summary');

    container.innerHTML = items.map(function (item) {
        return `
            <div class="summary-item ${item.full ? 'full-width' : ''}">
                <label>${item.label}</label>
                <span>${escapeHtml(item.value)}</span>
            </div>
        `;
    }).join('');
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// Live clear errors on input
document.addEventListener('DOMContentLoaded', function () {
    ['title', 'category', 'description', 'budget', 'duration'].forEach(function (id) {
        const el = document.getElementById(id);

        if (el) {
            el.addEventListener('input', function () {
                clearError('err-' + id);
            });

            el.addEventListener('change', function () {
                clearError('err-' + id);
            });
        }
    });
});