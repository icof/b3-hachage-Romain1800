document.addEventListener('DOMContentLoaded', function () {
    var mdp = document.getElementById("motDePasse");
    var forceMdp = document.getElementById("forceMotDePasse");

    if (mdp && forceMdp) {
        mdp.addEventListener("input", function () {
            var motDePasse = mdp.value;
            var force = 0;

            if (motDePasse.length >= 8) force++;
            if (/[a-z]/.test(motDePasse)) force++;
            if (/[A-Z]/.test(motDePasse)) force++;
            if (/[0-9]/.test(motDePasse)) force++;
            if (/[^A-Za-z0-9]/.test(motDePasse)) force++;

            if (force <= 2) {
                forceMdp.textContent = "Force : faible";
            } else if (force <= 4) {
                forceMdp.textContent = "Force : moyenne";
            } else {
                forceMdp.textContent = "Force : forte";
            }
        });
    }
});