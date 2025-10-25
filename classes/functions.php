<?php
// File: classes/functions.php

/**
 * Generates the JavaScript code block for client-side functionality.
 * This is embedded in the HTML body.
 */
function generate_form_scripts() {
    // We echo the JavaScript code block directly
    echo <<<JS
<script>
    /**
     * Updates the body background class based on the selected program.
     * @param {string} program - The selected program value ('ccs' or 'default').
     */
    function updateBackground(program) {
        const body = document.body;

        // Remove existing background classes
        body.classList.remove('ccs-program-bg', 'default-program-bg');
        
        // Apply the new class based on the selection
        if (program === 'ccs') {
            body.classList.add('ccs-program-bg');
        } else {
            body.classList.add('default-program-bg');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const selector = document.getElementById('program-select');
        const typeButtons = document.querySelectorAll('.type-button');

        // 1. Initialize the background on page load 
        if (selector) {
            updateBackground(selector.value); 
        }

        // 2. Add event listeners for the NEW/RENEWAL buttons (only exist on landing page)
        typeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type'); // 'new' or 'renewal'
                
                // Get the current value from the selector
                const program = selector ? selector.value : 'default';
                
                // Check if a program is selected before proceeding
                if (program === 'default') {
                    alert("Please select a Program before proceeding.");
                    return;
                }

                // Navigate to the first form section (alumni), passing type and program
                // FIX: Using string concatenation to prevent PHP variable interpolation warnings.
                window.location.href = 'index.php?section=alumni&program=' + program + '&type=' + type;
            });

            // Add visual toggle logic
            button.addEventListener('click', function() {
                typeButtons.forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    });
</script>
JS;
}