/* Color code servers depending on their usage levels */
function applyColorCoding() {
    document.querySelectorAll(".usage").forEach(cell => {
        let value = parseFloat(cell.dataset.value);
        if (isNaN(value)) {
            cell.style.color = "#999"; // Gray for NaN
            return;
        }

        if (value < 75) {
            cell.style.color = "green"; // Low usage
        } else if (value < 90) {
            cell.style.color = "orange"; // Medium usage
        } else {
            cell.style.color = "red"; // High usage
        }
    });
}

/* Apply color coding on page load */
applyColorCoding();