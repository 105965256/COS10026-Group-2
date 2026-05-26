<?php
$page_title = "Apply | ShopSphere";
$page_heading = "Apply for a role at ShopSphere";
$page_description = "Submit your application for customer-facing website, product listing, and digital workflow roles at ShopSphere.";

include 'header.inc';
include 'nav.inc';
?>

<main id="main-content" class="container page-main">
    <section class="page-intro">
        <p class="section-tag">Application Form</p>
        <h2>Submit your application</h2>
        <p>
            Complete every required field below and use the five-character reference number shown on the Jobs page for the role you are applying for.
        </p>
    </section>

    <div class="apply-layout">
        <section class="content-card form-card">
            <header class="card-header">
                <div>
                    <p class="card-tag">Online Application</p>
                    <h2>Candidate Details</h2>
                    <p class="card-summary">
                        All fields except the "Other Skills" textarea are required. Please enter details in the requested format before submitting.
                    </p>
                </div>
            </header>

            <form class="apply-form" action="process_eoi.php" method="post" novalidate>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="job-reference">Job Reference Number</label>
                        <input type="text" id="job-reference" name="job-reference" maxlength="5" required placeholder="Example: FWD25" aria-describedby="job-reference-note">
                        <p id="job-reference-note" class="field-note">Exactly five alphanumeric characters.</p>
                    </div>

                    <div class="field-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first-name" maxlength="50" required autocomplete="given-name">
                    </div>

                    <div class="field-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last-name" maxlength="50" required autocomplete="family-name">
                    </div>

                    <div class="field-group">
                        <label for="date-of-birth">Date of Birth</label>
                        <input type="text" id="date-of-birth" name="date-of-birth" placeholder="dd/mm/yyyy" required inputmode="numeric">
                    </div>

                    <fieldset class="choice-group full-width">
                        <legend>Gender</legend>
                        <div class="inline-options" aria-describedby="gender-note">
                            <label><input type="radio" name="gender" value="female" required> Female</label>
                            <label><input type="radio" name="gender" value="male"> Male</label>
                            <label><input type="radio" name="gender" value="other"> Other</label>
                            <label><input type="radio" name="gender" value="prefer-not-to-say"> Prefer not to say</label>
                        </div>
                        <p id="gender-note" class="field-note">Select the option that best reflects how you would like to identify for this application.</p>
                    </fieldset>

                    <div class="field-group full-width">
                        <label for="street-address">Street Address</label>
                        <input type="text" id="street-address" name="street-address" maxlength="100" required autocomplete="address-line1">
                    </div>

                    <div class="field-group">
                        <label for="suburb-town">Suburb / Town</label>
                        <input type="text" id="suburb-town" name="suburb-town" maxlength="50" required autocomplete="address-level2">
                    </div>

                    <div class="field-group">
                        <label for="state">State</label>
                        <select id="state" name="state" required autocomplete="address-level1">
                            <option value="">Please select</option>
                            <option value="VIC">VIC</option>
                            <option value="NSW">NSW</option>
                            <option value="QLD">QLD</option>
                            <option value="NT">NT</option>
                            <option value="WA">WA</option>
                            <option value="SA">SA</option>
                            <option value="TAS">TAS</option>
                            <option value="ACT">ACT</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode" maxlength="4" required inputmode="numeric" autocomplete="postal-code">
                    </div>

                    <div class="field-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="email" placeholder="name@example.com">
                    </div>

                    <div class="field-group">
                        <label for="phone-number">Phone Number</label>
                        <input type="tel" id="phone-number" name="phone-number" required inputmode="numeric" autocomplete="tel" placeholder="8 to 12 digits">
                    </div>

                    <fieldset class="choice-group full-width">
                        <legend>Skill List</legend>
                        <div class="inline-options" aria-describedby="skills-note">
                            <label><input type="checkbox" name="skills[]" value="front-end-development"> Front-End HTML/CSS</label>
                            <label><input type="checkbox" name="skills[]" value="product-listing-management"> Product Listing Management</label>
                            <label><input type="checkbox" name="skills[]" value="cms-content-updates"> CMS Content Updates</label>
                            <label><input type="checkbox" name="skills[]" value="accessibility-testing"> Accessibility Testing</label>
                        </div>
                        <p id="skills-note" class="field-note">Select one or more skills that match your experience.</p>
                    </fieldset>

                    <div class="field-group full-width">
                        <label for="other-skills">Other Skills</label>
                        <textarea id="other-skills" name="other-skills" rows="6" placeholder="Add any other relevant platforms, retail tools, or customer experience skills here."></textarea>
                    </div>

                    <div class="full-width button-row">
                        <input type="submit" value="Submit Application">
                        <input type="reset" value="Reset Form">
                    </div>
                </div>
            </form>
        </section>

        <aside class="apply-sidebar" aria-label="Application support information">
            <section class="sidebar-card">
                <p class="section-tag">Before You Submit</p>
                <h2>Application checklist</h2>
                <ol class="application-steps">
                    <li>Match the job reference number to the role listed on the Jobs page.</li>
                    <li>Check that your date of birth uses the dd/mm/yyyy format.</li>
                    <li>Use a current email address and a phone number with 8 to 12 digits.</li>
                    <li>Select the skills that best represent your web and digital retail experience.</li>
                </ol>
            </section>

            <section class="sidebar-card">
                <p class="section-tag">Support</p>
                <h2>Need help?</h2>
                <p>
                    If you are unsure which role fits your background, email our recruitment team at
                    <a href="mailto:careers@shopsphere.com">careers@shopsphere.com</a>
                    before submitting your application.
                </p>
                <p class="field-note">
                    Applications are reviewed for digital retail, product content, and user experience support roles.
                </p>
            </section>
        </aside>
    </div>
</main>

<?php include 'footer.inc'; ?>