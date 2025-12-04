<?php include('header.php'); ?>
<style>
/* PAGE LAYOUT */
.submit-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 20px;
    padding: 30px 15px;
    min-height: 100vh;
}

/* SIDE BANNERS */
.ad-banner {
    width: 150px;
    height: 100%;
    background: var(--main-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: .9rem;
    text-align: center;
    padding: 10px;
    animation: bannerFade 1.5s ease-in-out infinite alternate;
}
@keyframes bannerFade {
    0% { opacity: 0.75; }
    100% { opacity: 1; }
}

/* FORM CARD */
.form-card {
    flex: 1;
    max-width: 750px;
    background: var(--light-bg);
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    border-radius: 18px;
    padding: 30px;
    animation: fadeUp .8s ease forwards;
    transform: translateY(20px);
    opacity: 0;
}
@keyframes fadeUp {
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* TITLES */
.form-card h2 {
    text-align: center;
    color: var(--main-color);
    font-weight: 700;
    margin-bottom: 20px;
}

/* FORM STYLING */
.input-group {
    margin-bottom: 15px;
}
.input-group label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .9rem;
    font-weight: 600;
    margin-bottom: 4px;
    color: var(--text-color);
}
.input-group input, 
.input-group select,
.input-group textarea {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #dcdcdc;
    outline: none;
    font-size: .95rem;
}
textarea {
    min-height: 100px;
}

/* SUBMIT BUTTON */
.submit-btn {
    width: 100%;
    padding: 14px;
    background: var(--main-color);
    color: #fff;
    font-size: 1rem;
    font-weight: 650;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: .3s;
}
.submit-btn:hover {
    background: #0049cc;
    transform: translateY(-2px);
}

/* RESPONSIVE */
@media screen and (max-width: 992px) {
    .ad-banner { display: none; }
    .submit-container { padding: 20px; }
}
</style>

<div class="submit-container">
    <!-- Left Banner -->
    <div class="ad-banner">Google / Microsoft Ad<br><small>(Left)</small></div>

    <!-- Center Form Area -->
    <div class="form-card">
        <h2>Submit Your Innovative Idea </h2>

        <form action="submit_idea_process.php" method="POST" enctype="multipart/form-data">
            
            <div class="input-group">
                <label><i class="fa fa-folder"></i> Category</label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <option>Dredging of River Niger & Upper Benue (Inland Ports)</option>
                    <option>Steel Industry & Heavy-Duty Equipment Factories</option>
                    <option>Retail System Reform & National Co-operative Capital Base</option>
                    <option>Land Mapping via Geopin & Ownership System</option>
                    <option>Water Dam, Pipeborne Water & City/Town Planning</option>
                </select>
            </div>

            <div class="input-group">
                <label><i class="fa fa-lightbulb"></i> Project Title</label>
                <input type="text" name="title" placeholder="Enter idea title" required>
            </div>

            <div class="input-group">
                <label><i class="fa fa-align-left"></i> Summary</label>
                <textarea name="summary" placeholder="Short summary…" required></textarea>
            </div>

            <div class="input-group">
                <label><i class="fa fa-file-alt"></i> Justification</label>
                <textarea name="justification" placeholder="Why is this idea important?…" required></textarea>
            </div>

            <div class="input-group">
                <label><i class="fa fa-money-bill"></i> Estimated Budget ($)</label>
                <input type="number" name="budget" placeholder="Example: 5000" required>
            </div>

            <div class="input-group">
                <label><i class="fa fa-map-marker-alt"></i> Location</label>
                <input type="text" name="location" placeholder="City/Country" required>
            </div>

            <div class="input-group">
                <label><i class="fa fa-file-upload"></i> Qualification Document</label>
                <input type="file" name="qualification_doc" accept=".pdf,.doc,.docx" required>
            </div>

            <div class="input-group">
                <label><i class="fa fa-file-upload"></i> Proposal Document</label>
                <input type="file" name="proposal_doc" accept=".pdf,.doc,.docx" required>
            </div>

            <button type="submit" class="submit-btn">
                Submit Idea
            </button>
        </form>
    </div>

    <!-- Right Banner -->
    <div class="ad-banner">Google / Microsoft Ad<br><small>(Right)</small></div>
</div>

<?php include('footer.php'); ?>
