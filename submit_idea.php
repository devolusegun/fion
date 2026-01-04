<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "db.php";
$user_id = $_SESSION['user_id'];

if (!function_exists('profileCompletionPercent')) {
    function profileCompletionPercent($profile)
    {
        $total = 4;
        $done = 0;

        if (!empty($profile['phone']))   $done++;
        if (!empty($profile['address'])) $done++;
        if (!empty($profile['dob']))     $done++;
        if (!empty($profile['gender']))  $done++;

        return intval(($done / $total) * 100);
    }
}

$p = $conn->prepare("
  SELECT phone, address, dob, gender 
  FROM registered 
  WHERE id=?
");
$p->bind_param("i", $user_id);
$p->execute();
$profile = $p->get_result()->fetch_assoc();

$percent = profileCompletionPercent($profile);

if ($percent < 75) {
    include "profileincomplete.php";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Submit Project Idea</title>
    <link rel="stylesheet" href="css/subidea.css" />
</head>

<body>

    <div class="page-wrapper">

        <!-- Left Ad Space -->
        <aside class="ad-banner left-ad">AD SPACE</aside>

        <!-- Main Content -->
        <main class="form-container">
            <h2 class="form-title">Submit Project Idea</h2>

            <form id="ideaForm" method="POST" enctype="multipart/form-data" novalidate>

                <label>Category</label>
                <select name="category" id="category" required>
                    <option value="">-- Select Category --</option>
                    <option value="dredging_river_niger">Dredging of River Niger & Upper Benue (Inland Ports)</option>
                    <option value="steel_industry">Steel Industry & Heavy-Duty Equipment Factories</option>
                    <option value="retail_system_reform">Retail System Reform & National Co-operative Capital Base</option>
                    <option value="land_geopin_mapping">Land Mapping via Geopin & Ownership System</option>
                    <option value="water_dam_city_planning">Water Dam, Pipeborne Water & City Town Planning</option>
                </select>

                <label>Project Title</label>
                <input id="title" type="text" name="title" maxlength="200" required>

                <label>Summary (Short Description)</label>
                <textarea id="summary" name="summary" rows="3" maxlength="500" required></textarea>

                <label>Justification (Why is this important?)</label>
                <textarea id="justification" name="justification" rows="5" maxlength="2000" required></textarea>

                <label>Estimated Budget</label>
                <input id="budget" type="text" name="estimated_budget" placeholder="e.g., ₦500 million or $2.3M">

                <label>Project Location</label>
                <input id="location" type="text" name="location" placeholder="City, Region, State">

                <label>Qualification Document (PDF/DOC, max 10MB)</label>
                <input id="qualification" type="file" name="qualification" accept=".pdf,.doc,.docx" required>

                <label>Proposal Document (PDF/DOC, max 10MB)</label>
                <input id="proposal" type="file" name="proposal" accept=".pdf,.doc,.docx" required>

                <div id="formErrors" class="form-errors" aria-live="polite"></div>

                <button id="submitBtn" type="submit" class="submit-btn">Submit Idea</button>

            </form>

            <!-- Upload progress (hidden until upload starts) -->
            <div id="uploadProgress" class="upload-progress" aria-hidden="true">
                <div class="progress-bar-outer">
                    <div id="progressFill" class="progress-fill" style="width:0%"></div>
                </div>
                <div id="progressText" class="progress-text">0%</div>
            </div>
        </main>

        <!-- Right Ad Space -->
        <aside class="ad-banner right-ad">AD SPACE</aside>
    </div>

    <!-- LOADING OVERLAY (kept for legacy fallback) -->
    <div id="loadingOverlay" class="overlay" aria-hidden="true">
        <div class="spinner"></div>
        <p>Uploading files… Please wait</p>
    </div>

    <!-- SUCCESS MODAL (S3 Purple Style) -->
    <div id="successModal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="modal-card">
            <h3>🎉 Submitted!</h3>
            <p id="successMsg">Your project idea has been successfully sent for review.</p>
            <div class="modal-actions">
                <button id="viewDashboard" class="ok-btn">Go to Dashboard</button>
                <button id="submitAnother" class="ok-btn alt">Submit Another</button>
            </div>
        </div>
    </div>

    <!-- ERROR MODAL -->
    <div id="errorModal" class="modal error-modal" aria-hidden="true" role="alert">
        <div class="modal-card">
            <h3>⚠️ Submission Failed</h3>
            <div id="errorList" class="error-list"></div>
            <div class="modal-actions">
                <button id="closeError" class="ok-btn alt">Close</button>
            </div>
        </div>
    </div>

    <script>
        /* Client-side validation + AJAX upload with progress */
        const form = document.getElementById('ideaForm');
        const submitBtn = document.getElementById('submitBtn');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const formErrors = document.getElementById('formErrors');

        const successModal = document.getElementById('successModal');
        const errorModal = document.getElementById('errorModal');
        const errorList = document.getElementById('errorList');
        const viewDashboard = document.getElementById('viewDashboard');
        const submitAnother = document.getElementById('submitAnother');
        const closeError = document.getElementById('closeError');

        const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
        const ALLOWED_EXT = ['pdf', 'doc', 'docx'];

        function resetProgress() {
            progressFill.style.width = '0%';
            progressText.textContent = '0%';
            uploadProgress.style.display = 'none';
        }

        function showOverlay(show) {
            loadingOverlay.style.display = show ? 'flex' : 'none';
        }

        function openModal(modal) {
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal(modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }

        viewDashboard?.addEventListener('click', () => {
            location.href = 'dashboard.php';
        });
        submitAnother?.addEventListener('click', () => {
            closeModal(successModal);
            form.reset();
            resetProgress();
        });
        closeError?.addEventListener('click', () => {
            closeModal(errorModal);
        });

        function clientValidate() {
            formErrors.innerHTML = '';
            const errors = [];
            const requiredFields = [{
                    id: 'category',
                    label: 'Category'
                },
                {
                    id: 'title',
                    label: 'Title'
                },
                {
                    id: 'summary',
                    label: 'Summary'
                },
                {
                    id: 'justification',
                    label: 'Justification'
                },
                {
                    id: 'qualification',
                    label: 'Qualification Document'
                },
                {
                    id: 'proposal',
                    label: 'Proposal Document'
                }
            ];

            requiredFields.forEach(f => {
                const el = document.getElementById(f.id) || document.querySelector(`[name="${f.id}"]`);
                // for category & inputs where id may not exist, use form elements
                let val = null;
                if (el) val = el.value.trim();
                else {
                    const sel = document.querySelector(`[name="${f.id}"]`);
                    if (sel) val = sel.value.trim();
                }
                if (!val) errors.push(`${f.label} is required.`);
            });

            // File checks
            const qFile = document.getElementById('qualification').files[0];
            const pFile = document.getElementById('proposal').files[0];
            if (qFile) {
                const ext = qFile.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXT.includes(ext)) errors.push('Qualification document must be PDF or Word.');
                if (qFile.size > MAX_FILE_SIZE) errors.push('Qualification document exceeds 10MB.');
            }
            if (pFile) {
                const ext = pFile.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXT.includes(ext)) errors.push('Proposal document must be PDF or Word.');
                if (pFile.size > MAX_FILE_SIZE) errors.push('Proposal document exceeds 10MB.');
            }

            if (errors.length) {
                formErrors.innerHTML = errors.map(e => `<div class="err-row">• ${e}</div>`).join('');
                return false;
            }
            return true;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            formErrors.innerHTML = '';
            if (!clientValidate()) return;

            const fd = new FormData(form);
            const xhr = new XMLHttpRequest();

            xhr.open('POST', 'submit_idea_process.php', true);

            // enable progress UI
            uploadProgress.style.display = 'block';
            progressFill.style.width = '0%';
            progressText.textContent = '0%';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading…';

            xhr.upload.addEventListener('progress', function(ev) {
                if (ev.lengthComputable) {
                    const pct = Math.round((ev.loaded / ev.total) * 100);
                    progressFill.style.width = pct + '%';
                    progressText.textContent = pct + '%';
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Idea';
                    // parse JSON
                    let res = {};
                    try {
                        res = JSON.parse(xhr.responseText);
                    } catch (ex) {
                        res = {
                            success: false,
                            errors: ['Unexpected server response.']
                        };
                    }
                    if (xhr.status >= 200 && xhr.status < 300 && res.success) {
                        // success
                        document.getElementById('successMsg').textContent = res.message || 'Your project idea has been sent for review.';
                        openModal(successModal);
                        resetProgress();
                        form.reset();
                    } else {
                        // display server-side errors
                        const errs = res.errors || [res.message || 'Upload failed.'];
                        errorList.innerHTML = errs.map(x => `<div class="err-row">• ${x}</div>`).join('');
                        openModal(errorModal);
                        resetProgress();
                    }
                }
            };

            xhr.send(fd);
        });
    </script>

</body>

</html>